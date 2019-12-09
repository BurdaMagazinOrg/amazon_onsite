<?php

namespace Drupal\amazon_onsite;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining an aop feed item entity type.
 */
interface AopFeedItemInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  /**
   * Gets the aop feed item title.
   *
   * @return string
   *   Title of the aop feed item.
   */
  public function getTitle();

  /**
   * Sets the aop feed item title.
   *
   * @param string $title
   *   The aop feed item title.
   *
   * @return \Drupal\amazon_onsite\AopFeedItemInterface
   *   The called aop feed item entity.
   */
  public function setTitle($title);

  /**
   * Gets the aop feed item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the aop feed item.
   */
  public function getCreatedTime();

  /**
   * Sets the aop feed item creation timestamp.
   *
   * @param int $timestamp
   *   The aop feed item creation timestamp.
   *
   * @return \Drupal\amazon_onsite\AopFeedItemInterface
   *   The called aop feed item entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the aop feed item status.
   *
   * @return bool
   *   TRUE if the aop feed item is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets the aop feed item status.
   *
   * @param bool $status
   *   TRUE to enable this aop feed item, FALSE to disable.
   *
   * @return \Drupal\amazon_onsite\AopFeedItemInterface
   *   The called aop feed item entity.
   */
  public function setStatus($status);

}
