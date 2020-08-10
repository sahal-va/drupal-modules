<?php

namespace Drupal\blood_sugar\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Blood sugar record entities.
 *
 * @ingroup blood_sugar
 */
interface BloodSugarRecordInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Blood sugar record name.
   *
   * @return string
   *   Name of the Blood sugar record.
   */
  public function getName();

  /**
   * Sets the Blood sugar record name.
   *
   * @param string $name
   *   The Blood sugar record name.
   *
   * @return \Drupal\blood_sugar\Entity\BloodSugarRecordInterface
   *   The called Blood sugar record entity.
   */
  public function setName($name);

  /**
   * Gets the Blood sugar record creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Blood sugar record.
   */
  public function getCreatedTime();

  /**
   * Sets the Blood sugar record creation timestamp.
   *
   * @param int $timestamp
   *   The Blood sugar record creation timestamp.
   *
   * @return \Drupal\blood_sugar\Entity\BloodSugarRecordInterface
   *   The called Blood sugar record entity.
   */
  public function setCreatedTime($timestamp);

}
