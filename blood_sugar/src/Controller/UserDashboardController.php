<?php

namespace Drupal\blood_sugar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserDashboardController.
 */
class UserDashboardController extends ControllerBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->currentUser = $container->get('current_user');
    return $instance;
  }

  /**
   * Function userDashboard.
   *
   * @return array
   *   Return user dashboard.
   */
  public function userDashboard() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t(''),
      '#attached' => [
        'library' => [
          'blood_sugar/blood_sugar_library',
        ],
      ],
    ];
  }

  /**
   * Function getTitle.
   *
   * @return string
   *   Return page title.
   */
  public function getTitle() {
    if (in_array('administrator', $this->currentUser->getRoles())) {
      return $this->t('All Blood Sugar Records');
    }
    else {
      return $this->t('My Blood Sugar Records');
    }
  }

}
