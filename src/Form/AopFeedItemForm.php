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
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->entity;
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

    return $result;
  }

}
