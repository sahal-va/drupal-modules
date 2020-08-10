<?php

/**
 * @file
 * Contains application.page.inc.
 *
 * Page callback for Application entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Application templates.
 *
 * Default template: application.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_application(array &$variables) {
  // Fetch Application Entity Object.
  $application = $variables['elements']['#application'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
