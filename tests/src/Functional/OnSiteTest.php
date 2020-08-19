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
  public function testSingleItem() {

    $this->drupalLogin($this->anonymousUser);

    AopFeedItem::create([
      'title' => 'Bacon ipsum',
      'field_url' => 'http://example.com/bacon',
      'field_author' => 'Bernd am Grill',
      'field_content' => 'Bacon ipsum dolor amet chuck tenderloin sirloin, chicken tail kevin doner meatball jerky landjaeger jowl alcatra.',
      'field_intro_text' => 'Capicola biltong leberkas hamburger.',
      'changed' => \DateTime::createFromFormat('Y-m-d H:i:s', '2020-08-19 10:00:00')->getTimestamp(),
    ])->save();

    $http_client = $this->getHttpClient();
    $url = Url::fromRoute('amazon_onsite.rss')
      ->setAbsolute()
      ->toString();

    $response = $http_client->request('GET', $url, [
      'cookies' => $this->getSessionCookies(),
      'http_errors' => FALSE,
    ]);

    $this->assertStringEqualsFile(__DIR__ . '/../../fixtures/single-item.rss', (string) $response->getBody());
  }

}
