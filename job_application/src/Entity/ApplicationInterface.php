<?php

namespace Drupal\job_application\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Application entities.
 *
 * @ingroup job_application
 */
interface ApplicationInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Application name.
   *
   * @return string
   *   Name of the Application.
   */
  public function getName();

  /**
   * Sets the Application name.
   *
   * @param string $name
   *   The Application name.
   *
   * @return \Drupal\job_application\Entity\ApplicationInterface
   *   The called Application entity.
   */
  public function setName($name);

  /**
   * Gets the Application creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Application.
   */
  public function getCreatedTime();

  /**
   * Sets the Application creation timestamp.
   *
   * @param int $timestamp
   *   The Application creation timestamp.
   *
   * @return \Drupal\job_application\Entity\ApplicationInterface
   *   The called Application entity.
   */
  public function setCreatedTime($timestamp);

}
