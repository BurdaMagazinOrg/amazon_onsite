<?php

namespace Drupal\amazon_onsite\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\amazon_onsite\AopFeedItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AopItemController.
 *
 *  Returns responses for AOP RSS Item routes.
 */
class AopItemController extends ControllerBase {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
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
   * Displays a AOP RSS Item revision.
   *
   * @param int $aop_item_revision
   *   The AOP RSS Item revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function revisionShow($aop_item_revision) {
    $aop_item = $this->entityTypeManager()->getStorage('aop_item')
      ->loadRevision($aop_item_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('aop_item');

    return $view_builder->view($aop_item);
  }

  /**
   * Page title callback for a AOP RSS Item revision.
   *
   * @param int $aop_item_revision
   *   The AOP RSS Item revision ID.
   *
   * @return string
   *   The page title.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function revisionPageTitle($aop_item_revision) {
    $aop_item = $this->entityTypeManager()->getStorage('aop_item')
      ->loadRevision($aop_item_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $aop_item->label(),
      '%date' => $this->dateFormatter->format($aop_item->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a AOP RSS Item.
   *
   * @param \Drupal\amazon_onsite\AopFeedItemInterface $aop_item
   *   A AOP RSS Item object.
   *
   * @return array
   *   An array as expected by drupal_render().
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function revisionOverview(AopFeedItemInterface $aop_item) {
    $account = $this->currentUser();
    $aop_item_storage = $this->entityTypeManager()->getStorage('aop_item');

    $build['#title'] = $this->t('Revisions for %title', ['%title' => $aop_item->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all aop rss item revisions") || $account->hasPermission('administer aop rss item entities')));
    $delete_permission = (($account->hasPermission("delete all aop rss item revisions") || $account->hasPermission('administer aop rss item entities')));

    $rows = [];

    $vids = $aop_item_storage->revisionIds($aop_item);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\amazon_onsite\AopFeedItemInterface $revision */
      $revision = $aop_item_storage->loadRevision($vid);
      $username = [
        '#theme' => 'username',
        '#account' => $revision->getRevisionUser(),
      ];

      // Use revision link to link to revisions that are not active.
      $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
      if ($vid != $aop_item->getRevisionId()) {
        $link = $this->l($date, new Url('entity.aop_item.revision', [
          'aop_item' => $aop_item->id(),
          'aop_item_revision' => $vid,
        ]));
      }
      else {
        $link = $aop_item->link($date);
      }

      $row = [];
      $column = [
        'data' => [
          '#type' => 'inline_template',
          '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
          '#context' => [
            'date' => $link,
            'username' => $this->renderer->renderPlain($username),
            'message' => [
              '#markup' => $revision->getRevisionLogMessage(),
              '#allowed_tags' => Xss::getHtmlTagList(),
            ],
          ],
        ],
      ];
      $row[] = $column;

      if ($latest_revision) {
        $row[] = [
          'data' => [
            '#prefix' => '<em>',
            '#markup' => $this->t('Current revision'),
            '#suffix' => '</em>',
          ],
        ];
        foreach ($row as &$current) {
          $current['class'] = ['revision-current'];
        }
        $latest_revision = FALSE;
      }
      else {
        $links = [];
        if ($revert_permission) {
          $links['revert'] = [
            'title' => $this->t('Revert'),
            'url' => Url::fromRoute('entity.aop_item.revision_revert', [
              'aop_item' => $aop_item->id(),
              'aop_item_revision' => $vid,
            ]),
          ];
        }

        if ($delete_permission) {
          $links['delete'] = [
            'title' => $this->t('Delete'),
            'url' => Url::fromRoute('entity.aop_item.revision_delete', [
              'aop_item' => $aop_item->id(),
              'aop_item_revision' => $vid,
            ]),
          ];
        }

        $row[] = [
          'data' => [
            '#type' => 'operations',
            '#links' => $links,
          ],
        ];
      }

      $rows[] = $row;
    }

    $build['aop_item_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
