<?php

namespace Drupal\amazon_onsite\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a AOP RSS Item revision.
 *
 * @ingroup amazon_onsite
 */
class AopItemRevisionDeleteForm extends ConfirmFormBase {

  /**
   * The AOP RSS Item revision.
   *
   * @var \Drupal\amazon_onsite\Entity\AopItemInterface
   */
  protected $revision;

  /**
   * The AOP RSS Item storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $aopItemStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->aopItemStorage = $container->get('entity_type.manager')->getStorage('aop_item');
    $instance->connection = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aop_item_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the revision from %revision-date?', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.aop_item.version_history', ['aop_item' => $this->revision->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $aop_item_revision = NULL) {
    $this->revision = $this->AopItemStorage->loadRevision($aop_item_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->AopItemStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('AOP RSS Item: deleted %title revision %revision.', [
      '%title' => $this->revision->label(),
      '%revision' => $this->revision->getRevisionId(),
    ]);
    $this->messenger()->addMessage($this->t('Revision from %revision-date of AOP RSS Item %title has been deleted.', [
      '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
      '%title' => $this->revision->label(),
    ]));
    $form_state->setRedirect(
      'entity.aop_item.canonical',
       ['aop_item' => $this->revision->id()],
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {aop_item_field_revision} WHERE id = :id', [':id' => $this->revision->id()])->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.aop_item.version_history',
         ['aop_item' => $this->revision->id()]
      );
    }
  }

}
