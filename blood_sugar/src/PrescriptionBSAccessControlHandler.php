<?php

namespace Drupal\blood_sugar;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Prescription entity.
 *
 * @see \Drupal\blood_sugar\Entity\PrescriptionBS.
 */
class PrescriptionBSAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\blood_sugar\Entity\PrescriptionBSInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished prescription entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published prescription entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit prescription entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete prescription entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add prescription entities');
  }

}
