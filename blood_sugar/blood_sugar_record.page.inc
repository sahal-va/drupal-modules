<?php

/**
 * @file
 * Contains blood_sugar_record.page.inc.
 *
 * Page callback for Blood sugar record entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Blood sugar record templates.
 *
 * Default template: blood_sugar_record.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_blood_sugar_record(array &$variables) {
  // Fetch BloodSugarRecord Entity Object.
  $blood_sugar_record = $variables['elements']['#blood_sugar_record'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
