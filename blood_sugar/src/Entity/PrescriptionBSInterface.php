<?php

namespace Drupal\blood_sugar\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Prescription entities.
 *
 * @ingroup blood_sugar
 */
interface PrescriptionBSInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Prescription name.
   *
   * @return string
   *   Name of the Prescription.
   */
  public function getName();

  /**
   * Sets the Prescription name.
   *
   * @param string $name
   *   The Prescription name.
   *
   * @return \Drupal\blood_sugar\Entity\PrescriptionBSInterface
   *   The called Prescription entity.
   */
  public function setName($name);

  /**
   * Gets the Prescription creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Prescription.
   */
  public function getCreatedTime();

  /**
   * Sets the Prescription creation timestamp.
   *
   * @param int $timestamp
   *   The Prescription creation timestamp.
   *
   * @return \Drupal\blood_sugar\Entity\PrescriptionBSInterface
   *   The called Prescription entity.
   */
  public function setCreatedTime($timestamp);

}
