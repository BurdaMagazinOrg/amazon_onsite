<?php

namespace Drupal\Tests\paragraphs\Functional;

use Drupal\amazon_onsite\Entity\AopFeedItem;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Onsite RSS feed.
 *
 * @group paragraphs
 */
class OnSiteTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'amazon_onsite',
  ];

  /**
   * A simulated anonymous user with access only to node content.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $anonymousUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->anonymousUser = $this->drupalCreateUser(['access content']);

    $this->config('amazon_onsite.settings')
      ->set('channel_title', 'Bacon blog')
      ->set('website_url', 'http://example.com')
      ->set('feed_description', 'Best meat ever.')
      ->set('language', 'de-DE')
      ->save();
  }

  /**
   * Tests if a single item is rendered correctly.
   */
  public function testFeed() {

    $this->drupalLogin($this->anonymousUser);

    AopFeedItem::create([
      'title' => 'Bacon ipsum',
      'field_url' => 'http://example.com/bacon',
      'field_author' => 'Bernd am Grill',
      'field_content' => 'Bacon ipsum dolor amet chuck tenderloin sirloin, chicken tail kevin doner meatball jerky landjaeger jowl alcatra.',
      'field_intro_text' => 'Capicola biltong leberkas hamburger.',
      'changed' => \DateTime::createFromFormat('Y-m-d H:i:s', '2020-08-19 10:00:00')->getTimestamp(),
      'field_products' => [
        [
          'asin' => 'asdfghjklk',
          'headline' => 'Bacon cooking',
          'summary' => 'Best backon cooking book ever',
          'rank' => 1,
          'award' => 'Award',
        ],
      ],
    ])->save();

    AopFeedItem::create([
      'title' => 'Bavaria ipsum dolor sit amet Prosd dei Marterl.',
      'field_url' => 'http://example.com/bavaria',
      'field_author' => 'Fonsi',
      'field_content' => 'Bavaria ipsum dolor sit amet Prosd dei Marterl. Diandldrahn boarischer wea ko, dea ko Blosmusi Watschnpladdla no gwiss. Wiavui i auszutzeln Zidern, es!',
      'field_intro_text' => 'Schaung kost nix Mamalad gor Mamalad hogg di hera.',
      'changed' => \DateTime::createFromFormat('Y-m-d H:i:s', '2020-08-19 10:00:00')->getTimestamp(),
      'field_products' => [
        [
          'asin' => 'asdfghjklk',
          'headline' => 'Bavaria ipsum',
          'summary' => 'Bavaria ipsum dolor sit amet Prosd',
          'rank' => 1,
          'award' => 'Award',
        ],
      ],
    ])->save();

    $http_client = $this->getHttpClient();
    $url = Url::fromRoute('amazon_onsite.rss')
      ->setAbsolute()
      ->toString();

    $response = $http_client->request('GET', $url, [
      'cookies' => $this->getSessionCookies(),
      'http_errors' => FALSE,
    ]);

    $this->assertStringEqualsFile(__DIR__ . '/../../fixtures/feed.rss', (string) $response->getBody());
  }

}
