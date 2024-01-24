<?php

namespace Drupal\display_layout;

use Drupal\Core\Layout\LayoutDefinition;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Shared functionality for building Display layout form.
 */
trait DisplayLayoutFormTrait {

  use StringTranslationTrait;

  /**
   * Layout plugin manager.
   *
   * @var \Drupal\Core\Layout\LayoutPluginManager
   */
  protected $layoutPluginManager;

  /**
   * {@inheritdoc}
   */
  protected function getLayoutForm($default_layout = NULL) {
    return [
      '#title' => $this->t('Display layout settings'),
      '#type' => 'details',
      '#collapsible' => TRUE,
      'display_layout_id' => [
        '#title' => $this->t('Select a layout'),
        '#type' => 'select',
        '#empty_value' => '_none',
        '#options' => $this->getLayoutOptions(),
        '#default_value' => $default_layout,
      ],
    ];
  }

  /**
   * Get layout options grouped by category.
   */
  protected function getLayoutOptions() {
    $layout_options = [];

    $layouts = $this->getLayoutPluginManager()->getDefinitions();
    foreach ($layouts as $key => $layout_definition) {
      // Create new layout option group.
      if (empty($key)) {
        continue;
      }
      $category = (string) ($layout_definition->getCategory() ?: $this->t('Other'));
      if (!isset($layout_options[$category])) {
        $layout_options[$category] = [];
      }

      $layout_options[$category][$key] = $layout_definition->getLabel();
    }

    return $layout_options;
  }

  /**
   * {@inheritdoc}
   */
  protected function getLayoutRegions(?string $layout_id = NULL) {
    $regions = [];
    $default_message = $this->t('No field is displayed.');
    if (!empty($layout_id) && $layout_id != "_none") {
      $layout_definition = $this->getLayoutPluginManager()->getDefinition($layout_id, FALSE);
      if ($layout_definition instanceof LayoutDefinition) {
        foreach ($layout_definition->getRegions() as $region_id => $region_data) {
          $regions[$region_id] = [
            'title' => $region_data['label'],
            'message' => $default_message,
          ];
        }
      }
    }
    if (empty($regions)) {
      $regions['content'] = [
        'title' => $this->t('Content'),
        'message' => $default_message,
      ];
    }
    $regions['hidden'] = [
      'title' => $this->t('Disabled', [], ['context' => 'Plural']),
      'message' => $this->t('No field is hidden.'),
    ];
    return $regions;
  }

  /**
   * Get the layout plugin manager.
   */
  protected function getLayoutPluginManager() {
    if (empty($this->layoutPluginManager)) {
      $this->layoutPluginManager = \Drupal::service('plugin.manager.core.layout');
    }
    return $this->layoutPluginManager;
  }

}
