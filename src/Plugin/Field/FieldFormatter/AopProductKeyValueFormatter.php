<?php

namespace Drupal\amazon_onsite\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\amazon_onsite\Plugin\Field\FieldType\AopProductItem;

/**
 * Plugin implementation of the 'aop_product_key_value' formatter.
 *
 * @FieldFormatter(
 *   id = "aop_product_key_value",
 *   label = @Translation("Key-value"),
 *   field_types = {"aop_product"}
 * )
 */
class AopProductKeyValueFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $element = [];

    foreach ($items as $delta => $item) {

      $table = [
        '#type' => 'table',
      ];

      // ASIN.
      if ($item->asin) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('ASIN'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->asin ? $this->t('Yes') : $this->t('No'),
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Headline.
      if ($item->headline) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Headline'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->headline,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Summary.
      if ($item->summary) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Summary'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->summary,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Rank.
      if ($item->rank) {
        $allowed_values = AopProductItem::allowedRankValues();

        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Rank'),
              ],
            ],
            [
              'data' => [
                '#markup' => $allowed_values[$item->rank],
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      // Award.
      if ($item->award) {
        $table['#rows'][] = [
          'data' => [
            [
              'header' => TRUE,
              'data' => [
                '#markup' => $this->t('Award'),
              ],
            ],
            [
              'data' => [
                '#markup' => $item->award,
              ],
            ],
          ],
          'no_striping' => TRUE,
        ];
      }

      $element[$delta] = $table;

    }

    return $element;
  }

}
