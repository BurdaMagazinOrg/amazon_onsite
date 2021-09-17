<?php

namespace Drupal\amazon_onsite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Defines the 'aop_product' field widget.
 *
 * @FieldWidget(
 *   id = "aop_product_widget",
 *   label = @Translation("AOP product"),
 *   field_types = {"aop_product"},
 * )
 */
class AopProductWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['asin'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ASIN'),
      '#default_value' => isset($items[$delta]->asin) ? $items[$delta]->asin : NULL,
    ];

    $element['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#default_value' => isset($items[$delta]->headline) ? $items[$delta]->headline : NULL,
    ];

    $element['summary'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Summary'),
      '#default_value' => isset($items[$delta]->summary) ? $items[$delta]->summary : NULL,
    ];

    $element['rank'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Rank'),
      '#default_value' => isset($items[$delta]->rank) ? $items[$delta]->rank : NULL,
    ];

    $element['award'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Award'),
      '#default_value' => isset($items[$delta]->award) ? $items[$delta]->award : NULL,
    ];

    $element['#theme_wrappers'] = ['container', 'form_element'];
    $element['#attributes']['class'][] = 'aop-product-elements';
    $element['#attached']['library'][] = 'amazon_onsite/aop_product';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    // @see https://www.drupal.org/project/drupal/issues/2600790 ff.
    $property_path_array = explode('.', $violation->getPropertyPath());
    return isset($property_path_array[0]) ? $element[$property_path_array[0]] : $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value) {
      if ($value['asin'] === '') {
        $values[$delta]['asin'] = NULL;
      }
      if ($value['headline'] === '') {
        $values[$delta]['headline'] = NULL;
      }
      if ($value['summary'] === '') {
        $values[$delta]['summary'] = NULL;
      }
      if ($value['rank'] === '') {
        $values[$delta]['rank'] = NULL;
      }
      if ($value['award'] === '') {
        $values[$delta]['award'] = NULL;
      }
    }
    return $values;
  }

}
