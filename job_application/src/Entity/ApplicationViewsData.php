<?php

namespace Drupal\job_application\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Application entities.
 */
class ApplicationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
