<?php

namespace Drupal\amazon_onsite\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the aop feed item entity edit forms.
 */
class AopFeedItemForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);

    $form['#attached']['library'][] = 'amazon_onsite/drupal.amazon_onsite.insert_asin';
    $form['#attached']['drupalSettings']['amazon_onsite']['_path'] = drupal_get_path('module', 'amazon_onsite');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New aop feed item %label has been created.', $message_arguments));
      $this->logger('amazon_onsite')->notice('Created new aop feed item %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The aop feed item %label has been updated.', $message_arguments));
      $this->logger('amazon_onsite')->notice('Updated new aop feed item %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.aop_feed_item.canonical', ['aop_feed_item' => $entity->id()]);
  }

}
