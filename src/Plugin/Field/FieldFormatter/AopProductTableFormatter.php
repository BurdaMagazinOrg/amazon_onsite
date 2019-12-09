<?php

namespace Drupal\amazon_onsite\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\amazon_onsite\Plugin\Field\FieldType\AopProductItem;

/**
 * Plugin implementation of the 'aop_product_table' formatter.
 *
 * @FieldFormatter(
 *   id = "aop_product_table",
 *   label = @Translation("Table"),
 *   field_types = {"aop_product"}
 * )
 */
class AopProductTableFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $header[] = '#';
    $header[] = $this->t('ASIN');
    $header[] = $this->t('Headline');
    $header[] = $this->t('Summary');
    $header[] = $this->t('Rank');
    $header[] = $this->t('Award');

    $table = [
      '#type' => 'table',
      '#header' => $header,
    ];

    foreach ($items as $delta => $item) {
      $row = [];

      $row[]['#markup'] = $delta + 1;

      $row[]['#markup'] = $item->asin ? $this->t('Yes') : $this->t('No');

      $row[]['#markup'] = $item->headline;

      $row[]['#markup'] = $item->summary;

      if ($item->rank) {
        $allowed_values = AopProductItem::allowedRankValues();
        $row[]['#markup'] = $allowed_values[$item->rank];
      }
      else {
        $row[]['#markup'] = '';
      }

      $row[]['#markup'] = $item->award;

      $table[$delta] = $row;
    }

    return [$table];
  }

}
