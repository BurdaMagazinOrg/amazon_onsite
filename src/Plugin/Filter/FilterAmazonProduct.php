<?php

namespace Drupal\amazon_onsite\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a amazon product filter.
 *
 * @Filter(
 *   id = "filter_amazon_product",
 *   title = @Translation("Render amazon product cards"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class FilterAmazonProduct extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $result = new FilterProcessResult($text);

    if (strpos($text, 'data-amazon-onsite-product') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);

      foreach ($xpath->query('//a[@data-amazon-onsite-product]') as $node) {
        // Groom link: remove data attribute and inner HTML.
        $node->removeAttribute('data-amazon-onsite-product');
        $node->firstChild->nodeValue = '';
        // Add wrapper div.
        $wrapper = $dom->createElement('div');
        $wrapper->setAttribute('data-itemtype', 'product');
        $wrapper->appendChild($node->cloneNode(TRUE));
        $node->parentNode->replaceChild($wrapper, $node);
      }

      $result->setProcessedText(Html::serialize($dom));
    }
    return $result;
  }

}
