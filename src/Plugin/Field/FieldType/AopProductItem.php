<?php

namespace Drupal\amazon_onsite\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'aop_product' field type.
 *
 * @FieldType(
 *   id = "aop_product",
 *   label = @Translation("AOP product"),
 *   category = @Translation("General"),
 *   default_widget = "aop_product_widget",
 *   default_formatter = "aop_product_default"
 * )
 */
class AopProductItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if ($this->asin !== NULL) {
      return FALSE;
    }
    elseif ($this->headline !== NULL) {
      return FALSE;
    }
    elseif ($this->summary !== NULL) {
      return FALSE;
    }
    elseif ($this->rank !== NULL) {
      return FALSE;
    }
    elseif ($this->award !== NULL) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['asin'] = DataDefinition::create('string')
      ->setLabel(t('ASIN'));
    $properties['headline'] = DataDefinition::create('string')
      ->setLabel(t('Headline'));
    $properties['summary'] = DataDefinition::create('string')
      ->setLabel(t('Summary'));
    $properties['rank'] = DataDefinition::create('integer')
      ->setLabel(t('Rank'));
    $properties['award'] = DataDefinition::create('string')
      ->setLabel(t('Award'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $options['asin']['NotBlank'] = [];
    $options['asin']['Length'] = ['min' => 10, 'max' => 10];

    $options['rank']['Regex'] = [
      'pattern' => '/^([0-9]+)$/',
      'message' => 'Enter a whole number.',
    ];

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    // @todo Add more constrains here.
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'asin' => [
        'type' => 'varchar',
        'length' => 10,
      ],
      'headline' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'summary' => [
        'type' => 'text',
        'size' => 'big',
      ],
      'rank' => [
        'type' => 'int',
        'size' => 'normal',
      ],
      'award' => [
        'type' => 'text',
        'size' => 'big',
      ],
    ];

    $schema = [
      'columns' => $columns,
      'indexes' => [
        'asin' => ['asin'],
      ],
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $random = new Random();

    $values['asin'] = $random->string(random_int(10));

    $values['headline'] = $random->word(random_int(1, 255));

    $values['summary'] = $random->paragraphs(5);

    $values['rank'] = random_int(0, 10);

    $values['award'] = $random->paragraphs(5);

    return $values;
  }

}
