<?php

namespace Drupal\job_application;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Application entity.
 *
 * @see \Drupal\job_application\Entity\Application.
 */
class ApplicationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\job_application\Entity\ApplicationInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished application entities');
        }

        $access = AccessResult::allowedIfHasPermission($account, 'view published application entities');
        if (!$access->isAllowed() && $account->hasPermission('view own application entities')) {
          $access = $access->orIf(AccessResult::allowedIf($account->id() == $entity->getOwnerId())->cachePerUser()->addCacheableDependency($entity));
        }
        return $access;

      case 'update':

        $access = AccessResult::allowedIfHasPermission($account, 'edit application entities');
        if (!$access->isAllowed() && $account->hasPermission('edit own application entities')) {
          $access = $access->orIf(AccessResult::allowedIf($account->id() == $entity->getOwnerId())->cachePerUser()->addCacheableDependency($entity));
        }
        return $access;

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete application entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add application entities');
  }


}
