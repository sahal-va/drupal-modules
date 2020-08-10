<?php

namespace Drupal\blood_sugar;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Prescription entities.
 *
 * @ingroup blood_sugar
 */
class PrescriptionBSListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Prescription ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\blood_sugar\Entity\PrescriptionBS $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.prescription_b_s.edit_form',
      ['prescription_b_s' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
