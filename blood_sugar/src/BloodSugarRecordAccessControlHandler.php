<?php

namespace Drupal\blood_sugar;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Blood sugar record entity.
 *
 * @see \Drupal\blood_sugar\Entity\BloodSugarRecord.
 */
class BloodSugarRecordAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\blood_sugar\Entity\BloodSugarRecordInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished blood sugar record entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published blood sugar record entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit blood sugar record entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete blood sugar record entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add blood sugar record entities');
  }

}
