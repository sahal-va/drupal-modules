<?php

namespace Drupal\blood_sugar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Prescription edit forms.
 *
 * @ingroup blood_sugar
 */
class PrescriptionBSForm extends ContentEntityForm {

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
    $instance->isAdmin = in_array('administrator', $instance->account->getAccount()->getRoles());
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\blood_sugar\Entity\PrescriptionBS $entity */
    $form = parent::buildForm($form, $form_state);

    // Hiding the user_id field for non-admin users.
    if (!$this->isAdmin) {
      $form['user_id']['#access'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    // Redirecting to dashboard for non admin users.
    // Assuming admin has no Add Prescription on dashboard.
    if ($this->isAdmin) {
      $form_state->setRedirect('entity.prescription_b_s.canonical', ['prescription_b_s' => $entity->id()]);
      switch ($status) {
        case SAVED_NEW:
          $this->messenger()->addMessage($this->t('Created the %label Prescription record.', [
            '%label' => $entity->label(),
          ]));
          break;

        default:
          $this->messenger()->addMessage($this->t('Saved the %label Prescription record.', [
            '%label' => $entity->label(),
          ]));
      }
    }
    else {
      $this->messenger()->addMessage($this->t('Successfully Submitted Prescription.'));
      $form_state->setRedirect('blood_sugar.user_dashboard');
    }
  }

}
