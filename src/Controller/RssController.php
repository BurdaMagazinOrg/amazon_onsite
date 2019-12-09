<?php

namespace Drupal\amazon_onsite\Controller;

use Drupal\amazon_onsite\AopFeedItemInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RssController.
 *
 * @package Drupal\amazon_onsite\Controller
 */
class RssController extends ControllerBase {


  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * Callback for RSS feed.
   *
   * @return \Drupal\Core\Cache\CacheableResponse
   *   A CacheableResponse object.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function buildResponse() {
    $build = $this->build();
    // Set up an empty response, so for example RSS can set the proper
    // Content-Type header.
    $response = new CacheableResponse('', 200);
    $response->headers->set('Content-Type', 'application/xml; charset=utf-8');
    $build['#response'] = $response;

    $output = (string) $this->renderer->renderRoot($build);

    if (empty($output)) {
      throw new NotFoundHttpException();
    }

    $response->setContent($output);
    $cache_metadata = CacheableMetadata::createFromRenderArray($build);
    $response->addCacheableDependency($cache_metadata);

    return $response;
  }

  /**
   * Build a render array for the RSS feed.
   *
   * @return array
   *   The render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function build() {
    $config = $this->config('amazon_onsite.settings');

    $build = [
      '#theme' => 'rss_feed',
      '#title' => $config->get('channel_title'),
      '#link' => $config->get('website_url'),
      '#description' => $config->get('feed_description'),
      '#langcode' => $config->get('language'),
      '#last_build_date' => 'Wed, 1 Jan 2018 20:40:47 +0000',
      '#logo_path' => $config->get('logo_path'),
      '#items' => $this->getItems(),
    ];

    return $build;
  }

  /**
   * Load entities and build a render array for item part of rss feed.
   *
   * @return array
   *   The render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getItems() {
    $build = [];
    $storage = $this->entityTypeManager()->getStorage('aop_feed_item');

    // Query the entities.
    $query = $storage->getQuery()
      ->condition('status', 1)
      ->sort('created', 'DESC');
    $result = $query->execute();
    $items = $storage->loadMultiple(array_keys($result));

    foreach ($items as $item) {
      $elements = [
        'title' => $item->getTitle(),
        'amzn:subtitle' => $item->field_subtitle->first()->view(),
        'link' => $item->field_url->first()->getUrl()->toString(),
        'pubDate' => $this->dateFormatter->format($item->getChangedTime(), 'custom', 'D, d M Y H:i:s T'),
        'author' => $item->field_author->first()->view(),
        'content:encoded' => $item->field_content->first()->view(),
        'amzn:heroImage' => $item->field_hero_image->entity->url(),
        'amzn:heroImageCaption' => $item->field_hero_image_caption->first()->view(),
        'amzn:introText' => $item->field_intro_text->first()->view(),
        'amzn:indexContent' => $item->field_index_content->first()->view(),
        'amzn:products' => $this->getProductsfromItem($item),
      ];

      $build[] = [
        '#theme' => 'rss_feed_item',
        '#item_elements' => $elements,
      ];
    }

    return $build;
  }

  /**
   * Build a render array for product part of rss feed.
   *
   * @param \Drupal\amazon_onsite\AopFeedItemInterface $entity
   *   The entity object.
   *
   * @return array
   *   The render array
   */
  public function getProductsfromItem(AopFeedItemInterface $entity) {
    $build = [];
    foreach ($entity->field_products as $product) {
      $build[] = [
        '#theme' => 'rss_product_item',
        '#item_elements' => [
          'amzn:productURL' => 'https://amazon.de/dp/' . $product->asin,
          'amzn:productHeadline' => $product->headline,
          'amzn:productSummary' => $product->summary,
          'amzn:rank' => $product->rank,
          'amzn:award' => $product->award,
          // 'amzn:rating' => NULL,
          // 'amzn:ratingValue' => NULL,
          // 'amzn:applyToVariants' => NULL,
          // 'amzn:bestRating' => NULL,
          // 'amzn:worstRating' => NULL
        ],
      ];
    }

    return $build;
  }

}
