<?php

namespace Drupal\amazon_onsite\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Drupal\Core\Extension\ModuleHandlerInterface module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, FileSystemInterface $file_system) {
    parent::__construct($config_factory);
    $this->moduleHandler = $module_handler;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'amazon_onsite.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amazon_onsite_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('amazon_onsite.settings');

    $form['channel_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config->get('channel_title'),
      '#required' => TRUE,
    ];
    $form['website_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Website URL'),
      '#description' => $this->t('The website url which is associated with this RSS channel. (HTTPS is required)'),
      '#default_value' => $config->get('website_url'),
      '#pattern' => 'https://.*',
      '#required' => TRUE,
    ];
    $form['feed_description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feed description'),
      '#default_value' => $config->get('feed_description'),
      '#required' => TRUE,
    ];
    $form['logo'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Channel logo'),
    ];
    $form['logo']['logo_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path to image'),
      '#default_value' => $config->get('logo_path'),
    ];
    $form['logo']['logo_upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload image'),
      '#maxlength' => 40,
      '#description' => $this->t("If you don't have direct file access to the server, use this field to upload your logo."),
      '#upload_validators' => [
        'file_validate_is_image' => [],
      ],
    ];

    $form['language'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Language'),
      '#description' => $this->t('ISO639-1 language string'),
      '#default_value' => $config->get('language'),
      '#disabled' => TRUE,
    ];
    $form['channel_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Feed URL'),
      '#default_value' => Url::fromRoute('amazon_onsite.rss', [], ['absolute' => TRUE])->toString(),
      '#disabled' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($this->moduleHandler->moduleExists('file')) {

      // Check for a new uploaded logo.
      if (isset($form['logo'])) {
        $upload_location = isset($form['logo']['logo_upload']['#upload_location']) ? $form['logo']['logo_upload']['#upload_location'] : FALSE;
        $upload_name = implode('_', $form['logo']['logo_upload']['#parents']);
        $upload_validators = isset($form['logo']['logo_upload']['#upload_validators']) ? $form['logo']['logo_upload']['#upload_validators'] : [];

        $file = file_save_upload($upload_name, $upload_validators, $upload_location, 0);
        if ($file) {
          // Put the temporary file in form_values so we can save it on submit.
          $form_state->setValue('logo_upload', $file);
        }
      }
      // If the user provided a path for a logo or favicon file, make sure a
      // file exists at that path.
      if ($form_state->getValue('logo_path')) {
        $path = $this->validatePath($form_state->getValue('logo_path'));
        if (!$path) {
          $form_state->setErrorByName('logo_path', $this->t('The image path is invalid.'));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    try {
      if (!empty($form_state->getValue('logo_upload'))) {
        $filename = $this->fileSystem->copy($form_state->getValue('logo_upload')->getFileUri(), file_default_scheme() . '://');
        $logo_path = $filename;
      }
    }
    catch (FileException $e) {
      // Ignore.
    }

    $this->config('amazon_onsite.settings')
      ->set('channel_title', $form_state->getValue('channel_title'))
      ->set('website_url', $form_state->getValue('website_url'))
      ->set('feed_description', $form_state->getValue('channel_title'))
      ->set('language', $form_state->getValue('language'))
      ->set('logo_path', !empty($logo_path) ? $logo_path : $form_state->getValue('logo_path'))
      ->save();
  }

}
