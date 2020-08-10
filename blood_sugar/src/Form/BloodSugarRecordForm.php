<?php

namespace Drupal\blood_sugar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Blood sugar record edit forms.
 *
 * @ingroup blood_sugar
 */
class BloodSugarRecordForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    $instance->database = $container->get('database');
    $instance->now = \Drupal::time()->getCurrentTime();
    $instance->isAdmin = in_array('administrator', $instance->account->getAccount()->getRoles());
    $instance->wait = 0;
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\blood_sugar\Entity\BloodSugarRecord $entity */
    $form = parent::buildForm($form, $form_state);
    $form['blood_sugar_value']['widget'][0]['value']['#step'] = 0.1;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Check validation for non-admin users.
    if (!$this->isAdmin) {
      // Time interval in minutes.
      $time_interval = \Drupal::config('blood_sugar.settings')->get('minimum_time_interval');

      // Converting to secs.
      $time_interval *= 60;

      // Getting the time that user last submitted.
      $query = $this->database->select('blood_sugar_record', 'bs');
      $query->addField('bs', 'created', 'last_entry');
      $query->condition('bs.user_id', $this->account->id());
      $query->orderBy('bs.id', 'DESC');
      $last_entry = $query->execute()->fetch()->last_entry;

      if ($last_entry + $time_interval > $this->now) {
        $remaining = $last_entry + $time_interval - $this->now;
        $this->wait = $remaining;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Redirecting to dashboard for non admin users.
    // Assuming admin has no BS form on dashboard.
    if ($this->isAdmin) {
      $status = parent::save($form, $form_state);
      $form_state->setRedirect('entity.blood_sugar_record.canonical', ['blood_sugar_record' => $entity->id()]);
      switch ($status) {
        case SAVED_NEW:
          $this->messenger()->addMessage($this->t('Created the %label Blood sugar record.', [
            '%label' => $entity->label(),
          ]));
          break;

        default:
          $this->messenger()->addMessage($this->t('Saved the %label Blood sugar record.', [
            '%label' => $entity->label(),
          ]));
      }
    }
    elseif ($this->wait == 0) {
      $status = parent::save($form, $form_state);
      $this->messenger()->addMessage($this->t('Successfully Submitted the BS value.'));
      $form_state->setRedirect('blood_sugar.user_dashboard');
    }
    else {
      $remaining = ($this->wait < 60) ? $this->wait . ' seconds' : intval($this->wait / 60) . ' minutes';
      $this->messenger()->addWarning($this->t('You have to wait %time for next entry.', [
        '%time' => $remaining,
      ]));
      $form_state->setRedirect('blood_sugar.user_dashboard');
    }
  }

}
