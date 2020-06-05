<?php

namespace Drupal\amazon_onsite\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "insertasin" plugin.
 *
 * @CKEditorPlugin(
 *   id = "insertasin",
 *   label = @Translation("Insert amazon product card"),
 *   module = "ckeditor"
 * )
 */
class InsertAsin extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'amazon_onsite') . '/js/plugins/insertasin/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = drupal_get_path('module', 'amazon_onsite') . '/js/plugins/insertasin';
    return [
      'InsertAsin' => [
        'label' => $this->t('Insert amazon product card'),
        'image' => $path . '/icons/insertasin.png',
      ],
    ];
  }

}
