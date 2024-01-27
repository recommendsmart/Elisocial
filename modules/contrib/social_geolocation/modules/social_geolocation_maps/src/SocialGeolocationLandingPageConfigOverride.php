<?php

namespace Drupal\social_geolocation_maps;

use Drupal\Component\Transliteration\TransliterationInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Example configuration override.
 */
class SocialGeolocationLandingPageConfigOverride implements ConfigFactoryOverrideInterface {

  use StringTranslationTrait;

  /**
   * The transliteration service.
   */
  protected TransliterationInterface $transliteration;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Constructs the configuration override.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The Drupal configuration factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Component\Transliteration\TransliterationInterface $transliteration
   *   The transliteration service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler, TransliterationInterface $transliteration) {
    $this->configFactory = $config_factory;
    $this->moduleHandler = $module_handler;
    $this->transliteration = $transliteration;
  }

  /**
   * Returns config overrides.
   *
   * @param array $names
   *   A list of configuration names that are being loaded.
   *
   * @return array
   *   An array keyed by configuration name of override data. Override data
   *   contains a nested array structure of overrides.
   * @codingStandardsIgnoreStart
   */
  public function loadOverrides($names): array {
    $overrides = [];

    // Override social landing page references if the module is enabled,
    // so our custom blocks are also added.
    if (!$this->moduleHandler->moduleExists('social_landing_page')) {
      return $overrides;
    }

    $config_overrides = [
      'field.field.paragraph.block.field_block_reference_secondary',
      'field.field.paragraph.block.field_block_reference',
    ];

    // Add landing page block to primary and secondary block options.
    foreach ($config_overrides as $config_name) {
      if (in_array($config_name, $names)) {
        // Grab current configuration and push the new values.
        $config = $this->configFactory->getEditable($config_name);
        // We have to add config dependencies to field storage.
        $plugin_ids = $config->getOriginal('settings.selection_settings.plugin_ids', FALSE);
        $plugin_ids['views_block:social_geolocation_leaflet_commonmap_with_marker_icons-block_upcomming_events_map'] = 'views_block:social_geolocation_leaflet_commonmap_with_marker_icons-block_upcomming_events_map';
        $overrides[$config_name]['settings']['selection_settings']['plugin_ids'] = $plugin_ids;
      }
    }

    // Add social_tags filter to the group map.
    $config_name = 'views.view.social_geolocation_groups';

    if (in_array($config_name, $names)) {
      // Check if tagging service is available.
      if (!\Drupal::hasService('social_tagging.tag_service')) {
        return $overrides;
      }
      /** @var \Drupal\social_tagging\SocialTaggingService $tag_service */
      $tag_service = \Drupal::service('social_tagging.tag_service');

      // Check if tagging is active.
      if (!($tag_service->active() && $tag_service->hasContent())) {
        return $overrides;
      }

      // Prepare fields.
      $fields = [];
      $fields['social_tagging_target_id'] = [
        'identifier' => 'tag',
        'label' => $this->t('Tags'),
      ];

      if ($tag_service->allowSplit()) {
        $fields = [];
        foreach ($tag_service->getCategories() as $tid => $value) {
          if (!empty($tag_service->getChildren($tid))) {
            $fields['social_tagging_target_id_' . $tid] = [
              'identifier' => $this->transform($value),
              'label' => $value,
            ];
          }
        }
      }

      $overrides[$config_name]['dependencies']['config'][] = 'taxonomy.vocabulary.social_tagging';
      $overrides[$config_name]['display']['default']['cache_metadata']['contexts'][] = 'user';

      $group = 1;

      if (count($fields) > 1) {
        $overrides[$config_name]['display']['default']['display_options']['filter_groups']['groups'][1] = 'AND';
        $overrides[$config_name]['display']['default']['display_options']['filter_groups']['groups'][2] = 'OR';
        $group++;
      }

      // Add tagging fields to the views filters.
      foreach ($fields as $field => $data) {
        $overrides[$config_name]['display']['default']['display_options']['filters'][$field] = [
          'id' => $field,
          'table' => 'group__social_tagging',
          'field' => 'social_tagging_target_id',
          'relationship' => 'none',
          'group_type' => 'group',
          'admin_label' => '',
          'operator' => '=',
          'value' => [
            'min' => '',
            'max' => '',
            'value' => '',
          ],
          'group' => $group,
          'exposed' => TRUE,
          'expose' => [
            'operator_id' => $field . '_op',
            'label' => $data['label'],
            'description' => '',
            'use_operator' => FALSE,
            'operator' => $field . '_op',
            'identifier' => $data['identifier'],
            'required' => FALSE,
            'remember' => FALSE,
            'multiple' => FALSE,
            'remember_roles' => [
              'authenticated' => 'authenticated',
              'anonymous' => '0',
              'administrator' => '0',
              'contentmanager' => '0',
              'sitemanager' => '0',
            ],
            'placeholder' => '',
            'min_placeholder' => '',
            'max_placeholder' => '',
          ],
          'is_grouped' => FALSE,
          'group_info' => [
            'label' => '',
            'description' => '',
            'identifier' => '',
            'optional' => TRUE,
            'widget' => 'select',
            'multiple' => FALSE,
            'remember' => FALSE,
            'default_group' => 'All',
            'default_group_multiple' => [],
            'group_items' => [],
          ],
          'entity_type' => 'group',
          'entity_field' => 'social_tagging',
          'plugin_id' => 'numeric',
        ];
      }
    }

    return $overrides;
  }

  /**
   * Transforms given string to machine name.
   *
   * @param string $value
   *   The value to be transformed.
   * @param string $pattern
   *   The replacement pattern for regex.
   */
  private function transform(string $value, string $pattern = '/[^a-z0-9_]+/'): string {
    $value = $this->transliteration->transliterate(
      $value,
      LanguageInterface::LANGCODE_DEFAULT,
      '_',
    );

    if (
      ($value = preg_replace($pattern, '_', strtolower($value))) !== NULL &&
      ($value = preg_replace('/_+/', '_', $value)) !== NULL
    ) {
      return $value;
    }

    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix(): string {
    return 'SocialGeolocationLandingPageConfigOverride';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name): CacheableMetadata {
    return new CacheableMetadata();
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

}
