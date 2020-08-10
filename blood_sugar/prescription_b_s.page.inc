<?php

/**
 * @file
 * Contains prescription_b_s.page.inc.
 *
 * Page callback for Prescription entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Prescription templates.
 *
 * Default template: prescription_b_s.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_prescription_b_s(array &$variables) {
  // Fetch PrescriptionBS Entity Object.
  $prescription_b_s = $variables['elements']['#prescription_b_s'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
