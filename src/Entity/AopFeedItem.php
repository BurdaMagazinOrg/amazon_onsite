<?php

namespace Drupal\amazon_onsite\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\UserInterface;

/**
 * Defines the aop feed item entity class.
 *
 * @ContentEntityType(
 *   id = "aop_feed_item",
 *   label = @Translation("AOP feed item"),
 *   label_collection = @Translation("AOP feed items"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\amazon_onsite\AopFeedItemListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\amazon_onsite\AopFeedItemAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\amazon_onsite\Form\AopFeedItemForm",
 *       "edit" = "Drupal\amazon_onsite\Form\AopFeedItemForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "aop_feed_item",
 *   revision_table = "aop_feed_item_revision",
 *   show_revision_ui = FALSE,
 *   admin_permission = "access aop feed item overview",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/aop-feed-item/add",
 *     "canonical" = "/aop_feed_item/{aop_feed_item}",
 *     "edit-form" = "/admin/content/aop-feed-item/{aop_feed_item}/edit",
 *     "delete-form" = "/admin/content/aop-feed-item/{aop_feed_item}/delete",
 *     "collection" = "/admin/content/aop-feed-item"
 *   },
 *   field_ui_base_route = "entity.aop_feed_item.settings"
 * )
 */
class AopFeedItem extends RevisionableContentEntityBase {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   *
   * When a new aop feed item entity is created, set the uid entity reference to
   * the current user as the creator of the entity.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += ['uid' => \Drupal::currentUser()->id()];
  }

  /**
   * Gets the aop feed item title.
   *
   * @return string
   *   Title of the aop feed item.
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * Sets the aop feed item title.
   *
   * @param string $title
   *   The aop feed item title.
   *
   * @return \Drupal\amazon_onsite\Entity\AopFeedItem
   *   The called aop feed item entity.
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * Returns the aop feed item status.
   *
   * @return bool
   *   TRUE if the aop feed item is enabled, FALSE otherwise.
   */
  public function isEnabled() {
    return (bool) $this->get('status')->value;
  }

  /**
   * Sets the aop feed item status.
   *
   * @param bool $status
   *   TRUE to enable this aop feed item, FALSE to disable.
   *
   * @return \Drupal\amazon_onsite\Entity\AopFeedItem
   *   The called aop feed item entity.
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * Gets the aop feed item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the aop feed item.
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * Sets the aop feed item creation timestamp.
   *
   * @param int $timestamp
   *   The aop feed item creation timestamp.
   *
   * @return \Drupal\amazon_onsite\Entity\AopFeedItem
   *   The called aop feed item entity.
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setRevisionable(TRUE)
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the aop feed item entity.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setRevisionable(TRUE)
      ->setLabel(t('Status'))
      ->setDescription(t('A boolean indicating whether the aop feed item is enabled.'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Published')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setRevisionable(TRUE)
      ->setLabel(t('Author'))
      ->setDescription(t('The user ID of the aop feed item author.'))
      ->setSetting('target_type', 'user')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the aop feed item was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the aop feed item was last edited.'));

    return $fields;
  }

}
