<?php

/**
 * @file
 * Contains aop_item.page.inc.
 *
 * Page callback for AOP RSS Item entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for AOP RSS Item templates.
 *
 * Default template: aop_item.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_aop_item(array &$variables) {
  // Fetch AopItem Entity Object.
  $aop_item = $variables['elements']['#aop_item'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
