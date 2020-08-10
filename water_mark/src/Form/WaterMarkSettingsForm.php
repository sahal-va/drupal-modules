<?php

namespace Drupal\water_mark\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WaterMarkSettingsForm.
 */
class WaterMarkSettingsForm extends ConfigFormBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\Component\Datetime\TimeInterface definition.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $datetimeTime;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->currentUser = $container->get('current_user');
    $instance->database = $container->get('database');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'water_mark.watermarksettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'water_mark_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user_id = $this->currentUser->id();
    $config = $this->config('water_mark.watermarksettings');
    $values = $config->get($user_id);
    $form['watermark_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Watermark Type'),
      '#options' => [
        'image' => 'Image Watermark',
        'text' => 'Text Overlay',
      ],
      '#description' => $this->t('Please choose the type of watermark.'),
      '#default_value' => $values['watermark_type'],
      '#required' => TRUE,
    ];
    $form['watermark_image'] = [
      '#type' => 'details',
      '#title' => $this->t('Watermark Image Settings'),
      '#tree' => TRUE,
      '#description' => $this->t('Settings for Image watermark.'),
      '#states' => [
        'visible' => [
          ':input[name="watermark_type"]' => ['value' => 'image'],
        ],
      ],
    ];
    $form['watermark_image']['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Watermark Image'),
      '#description' => $this->t('Upload Watermark image. (png, jpg, jpeg)'),
      '#default_value' => $values['watermark_image']['image'],
      '#upload_location' => 'public://WATERMARKS/',
      '#upload_validators' => [
        'file_validate_extensions' => ["png jpg jpeg"],
      ],
      '#states' => [
        'visible' => [
          ':input[name="watermark_type"]' => ['value' => 'image'],
        ],
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'image'],
        ],
      ],
    ];
    $form['watermark_image']['margin_right'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin Right.'),
      '#default_value' => $values['watermark_image']['margin_right'],
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'image'],
        ],
      ],
    ];
    $form['watermark_image']['margin_bottom'] = [
      '#type' => 'number',
      '#title' => $this->t('Margin Bottom.'),
      '#default_value' => $values['watermark_image']['margin_bottom'],
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'image'],
        ],
      ],
    ];
    $form['watermark_text'] = [
      '#type' => 'details',
      '#title' => $this->t('Text Overlay Settings'),
      '#tree' => TRUE,
      '#description' => $this->t('Settings for text overlay.'),
      '#states' => [
        'visible' => [
          ':input[name="watermark_type"]' => ['value' => 'text'],
        ],
      ],
    ];
    $form['watermark_text']['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Overlay Text'),
      '#description' => $this->t('Please give text to be added as Overlay.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $values['watermark_text']['text'],
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'text'],
        ],
      ],
    ];
    $form['watermark_text']['fontsize'] = [
      '#type' => 'number',
      '#title' => $this->t('Font Size'),
      '#default_value' => $values['watermark_text']['fontsize'],
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'text'],
        ],
      ],
    ];
    $form['watermark_text']['angle'] = [
      '#type' => 'number',
      '#title' => $this->t('Angle.'),
      '#description' => $this->t('The angle in degrees, with 0 degrees being left-to-right reading text. Higher values represent a counter-clockwise rotation. For example, a value of 90 would result in bottom-to-top reading text.'),
      '#default_value' => $values['watermark_text']['angle'],
      '#step' => 0.5,
      '#min' => 0,
      '#max' => 359,
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'text'],
        ],
      ],
    ];
    $form['watermark_text']['x'] = [
      '#type' => 'number',
      '#title' => $this->t('The x-ordinate.'),
      '#description' => $this->t('The coordinates given by x and y will define the basepoint of the first character'),
      '#default_value' => $values['watermark_text']['x'],
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'text'],
        ],
      ],
    ];
    $form['watermark_text']['y'] = [
      '#type' => 'number',
      '#title' => $this->t('The y-ordinate.'),
      '#description' => $this->t('This sets the position of the fonts baseline, not the very bottom of the character.'),
      '#default_value' => $values['watermark_text']['y'],
      '#states' => [
        'required' => [
          ':input[name="watermark_type"]' => ['value' => 'text'],
        ],
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Building the conf array for this user.
    $user_id = $this->currentUser->id();
    $values[$user_id] = [
      'watermark_type' => $form_state->getValue('watermark_type'),
      'watermark_image' => $form_state->getValue('watermark_image'),
      'watermark_text' => $form_state->getValue('watermark_text'),
    ];

    parent::submitForm($form, $form_state);

    // Saving conf per user.
    $this->config('water_mark.watermarksettings')
      ->set($user_id, $values[$user_id])
      ->save();

    // Clearing cache.
    $this->database->truncate('cache_dynamic_page_cache')->execute();
    $this->database->truncate('cache_render')->execute();

  }

}
