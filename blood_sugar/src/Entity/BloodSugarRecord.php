<?php

namespace Drupal\blood_sugar\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Blood sugar record entity.
 *
 * @ingroup blood_sugar
 *
 * @ContentEntityType(
 *   id = "blood_sugar_record",
 *   label = @Translation("Blood sugar record"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\blood_sugar\BloodSugarRecordListBuilder",
 *     "views_data" = "Drupal\blood_sugar\Entity\BloodSugarRecordViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\blood_sugar\Form\BloodSugarRecordForm",
 *       "add" = "Drupal\blood_sugar\Form\BloodSugarRecordForm",
 *       "edit" = "Drupal\blood_sugar\Form\BloodSugarRecordForm",
 *       "delete" = "Drupal\blood_sugar\Form\BloodSugarRecordDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\blood_sugar\BloodSugarRecordHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\blood_sugar\BloodSugarRecordAccessControlHandler",
 *   },
 *   base_table = "blood_sugar_record",
 *   translatable = FALSE,
 *   admin_permission = "administer blood sugar record entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/blood_sugar_record/{blood_sugar_record}",
 *     "add-form" = "/admin/structure/blood_sugar_record/add",
 *     "edit-form" = "/admin/structure/blood_sugar_record/{blood_sugar_record}/edit",
 *     "delete-form" = "/admin/structure/blood_sugar_record/{blood_sugar_record}/delete",
 *     "collection" = "/admin/structure/blood_sugar_record",
 *   },
 *   field_ui_base_route = "blood_sugar_record.settings"
 * )
 */
class BloodSugarRecord extends ContentEntityBase implements BloodSugarRecordInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Blood sugar record entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['blood_sugar_value'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Blood Sugar Value'))
      ->setDescription(t('Enter your Blood Sugar Value'))
      ->setSettings([
        'step' => 1,
        'min' => 0,
        'max' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'number_decimal',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that this record was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that this record was last edited.'));

    return $fields;
  }

}
