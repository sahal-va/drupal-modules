<?php

namespace Drupal\job_application\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form controller for Application edit forms.
 *
 * @ingroup job_application
 */
class ApplicationForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * The current Job.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $job;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    $instance->messenger = $container->get('messenger');
    $instance->database = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, NodeInterface $job = NULL) {
    /* @var \Drupal\job_application\Entity\Application $entity */
    $form = parent::buildForm($form, $form_state);
    $formMode = $form['#process'][1][0]->getMode();
    if (!empty($job)) {
      $this->job = $job;
      $jobId = $job->id();
      $jobPath = $job->toUrl()->toString();

      // Setting the allowed Resume formats for this Job.
      $form['resume']['widget'][0]['#upload_validators']['file_validate_extensions'] = $job->field_allowed_resume_file_format->value;
      $form['resume']['widget'][0]['#description'] = $this->t('Only files with the following extensions are allowed:') . $job->field_allowed_resume_file_format->value;

      // Checking whether the Job Opening is closed or
      // User is Already applied for this Opening (Admin is excluded).
      if (!in_array('administrator', $this->account->getRoles())) {
        $isClosed = $this->isClosedJob();
        $isApplied = $this->isAppliedJob($jobId);
        if ($isClosed) {
          $this->messenger->addWarning($this->t('Sorry! Applications are closed for %label.', [
            '%label' => $job->getTitle(),
          ]));
          $response = new RedirectResponse($jobPath);
          $response->send();
        }
        elseif ($isApplied && $formMode == 'add') {
          $this->messenger->addWarning($this->t('Sorry! You are already applied for %label.', [
            '%label' => $job->getTitle(),
          ]));
          $response = new RedirectResponse($jobPath);
          $response->send();
        }
      }
    }

    // Visually hiding the unwanted fields from non-admin user.
    // @todo This should be changed to some secured way.
    // As I get parent form validation errors, skipping by visually hided.
    if (!in_array('administrator', $this->account->getRoles())) {
      // unset($form['name']['widget']);
      // unset($form['user_id']['widget']);
      // unset($form['points']['widget']);
      // unset($form['job_id']['widget']);
      $form['name']['widget'][0]['value']['#type'] = 'hidden';
      $form['name']['widget'][0]['value']['#value'] = 'Job Application';
      $form['user_id']['widget'][0]['value']['#type'] = 'hidden';
      $form['points']['widget'][0]['value']['#type'] = 'hidden';
      $form['job_id']['#attributes']['class'][] = 'hidden';
      $form['user_id']['#attributes']['class'][] = 'hidden';
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    // Redirection and status messages when accessed from Job page.
    if (isset($this->job)) {
      $this->entity->job_id->target_id = $this->job->id();
      switch ($status) {
        case SAVED_NEW:
          $this->messenger->addMessage($this->t('You have successfully applied for %label Application.', [
            '%label' => $this->job->getTitle(),
          ]));
          break;

        default:
          $this->messenger->addMessage($this->t('Successfully updated the %label Application.', [
            '%label' => $this->job->getTitle(),
          ]));
      }
      $this->entity->save();
      $form_state->setRedirectUrl($this->job->toUrl());
    }

    // Redirection and status messages for admin UI.
    else {

      switch ($status) {
        case SAVED_NEW:
          $this->messenger->addMessage($this->t('Created the %label Application.', [
            '%label' => $entity->label(),
          ]));
          break;

        default:
          $this->messenger->addMessage($this->t('Saved the %label Application.', [
            '%label' => $entity->label(),
          ]));
      }
      $form_state->setRedirect('entity.application.canonical', ['application' => $entity->id()]);
    }
  }

  /**
   * Function to check Job is Closed.
   *
   * @return bool
   *   Boolean indicating Job is closed or not.
   */
  private function isClosedJob() {
    $deadlineTime = new DrupalDateTime($this->job->field_deadline_to_apply->value, 'UTC');
    $deadlineTime = $deadlineTime->getTimestamp();
    $currentTime = new DrupalDateTime();
    $currentTime = $currentTime->getTimestamp();
    return ($deadlineTime < $currentTime) ? TRUE : FALSE;
  }

  /**
   * Function to check User already applied for this Job.
   *
   * @param string $jobId
   *   JobId for the Application.
   *
   * @return bool
   *   Boolean indicating User already Applied for this Job or not.
   */
  private function isAppliedJob(&$jobId) {
    $query = $this->database->select('application', 'app')
      ->fields('app', ['id'])
      ->condition('app.user_id', $this->account->id(), '=')
      ->condition('app.job_id', $jobId, '=')
      ->execute();
    $result = $query->fetch();
    return empty($result->id) ? FALSE : TRUE;
  }

}
