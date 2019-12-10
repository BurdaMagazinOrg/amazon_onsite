<?php

namespace Drupal\amazon_onsite\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'aop_product_default' formatter.
 *
 * @FieldFormatter(
 *   id = "aop_product_default",
 *   label = @Translation("Default"),
 *   field_types = {"aop_product"}
 * )
 */
class AopProductDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {

      $element[$delta]['asin'] = [
        '#type' => 'item',
        '#title' => $this->t('ASIN'),
        '#markup' => $item->asin,
      ];

      if ($item->headline) {
        $element[$delta]['headline'] = [
          '#type' => 'item',
          '#title' => $this->t('Headline'),
          '#markup' => $item->headline,
        ];
      }

      if ($item->summary) {
        $element[$delta]['summary'] = [
          '#type' => 'item',
          '#title' => $this->t('Summary'),
          '#markup' => $item->summary,
        ];
      }

      if ($item->rank) {
        $element[$delta]['rank'] = [
          '#type' => 'item',
          '#title' => $this->t('Rank'),
          '#markup' => $item->rank,
        ];
      }

      if ($item->award) {
        $element[$delta]['award'] = [
          '#type' => 'item',
          '#title' => $this->t('Award'),
          '#markup' => $item->award,
        ];
      }

    }

    return $element;
  }

}
