<?php

namespace Drupal\job_application\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ApplicationController.
 */
class ApplicationController extends ControllerBase {

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->database = $container->get('database');
    return $instance;
  }

  /**
   * Gettable.
   *
   * @return string
   *   Return Hello string.
   */
  public function getTable() {
    $data = $this->getApplicationPoints();
    $jobs = array_pop($data);
    return [
      '#theme' => 'application_table',
      '#data' => $data,
      '#jobs' => $jobs,
    ];
  }

  /**
   * Funct getApplicationPoints.
   *
   * @return array
   *   Return array of table data.
   */
  public function getApplicationPoints() {
    $applicants = $this->getAllApplicants();
    $data = [];
    foreach ($applicants as $uid => $uname) {
      $subQuery = $this->database->select('application', 'app')
        ->fields('app', ['job_id', 'points'])
        ->condition('app.user_id', $uid, '=');
      $query = $this->database->select('node_field_data', 'n')
        ->fields('n', ['title'])
        ->fields('a', ['points'])
        ->condition('n.type', 'job_opening');
      $query->addJoin('LEFT', $subQuery, 'a', 'n.nid = a.job_id');
      $query->orderBy('n.nid', 'ASC');
      $results = $query->execute()->fetchAll();

      // Build the points array for each user.
      $points = $jobs = [];
      foreach ($results as $item) {
        $points[] = $item->points;
        $jobs[] = $item->title;
      }
      $data[$uname] = $points;
    }

    // Jobs array for printing table header.
    $data['jobs'] = $jobs;

    return $data;
  }

  /**
   * Funct getAllApplicants.
   *
   * @return array
   *   Return array of applicant ids.
   */
  public function getAllApplicants() {

    // Excluded Admin from the applicants list because
    // Admin has permission to do multiple applications for a job,
    // So some error will occur on the points table.
    $query = $this->database->select('application', 'a')
      ->distinct()
      ->fields('a', ['user_id'])
      ->fields('u', ['name'])
      ->condition('a.user_id', 1, '!=');
    $query->addJoin('LEFT', 'users_field_data', 'u', 'u.uid = a.user_id');
    $query->orderBy('a.user_id', 'ASC');
    $results = $query->execute()->fetchAllKeyed(0, 1);
    return $results;
  }

}
