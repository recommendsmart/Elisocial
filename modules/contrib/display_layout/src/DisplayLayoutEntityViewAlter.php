<?php

namespace Drupal\display_layout;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Layout\LayoutDefinition;
use Drupal\Core\Layout\LayoutInterface;
use Drupal\Core\Layout\LayoutPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Alters entity view render arrays to use display layout settings.
 */
class DisplayLayoutEntityViewAlter implements ContainerInjectionInterface {

  /**
   * Layout plugin manager.
   *
   * @var \Drupal\Core\Layout\LayoutPluginManager
   */
  protected $layoutPluginManager;

  /**
   * Constructs a new DisplayLayoutEntityViewAlter instance.
   */
  public function __construct(LayoutPluginManager $layout_plugin_manager) {
    $this->layoutPluginManager = $layout_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.core.layout'));
  }

  /**
   * Performs entity view build alteration.
   */
  public function alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
    $layout_id = $display->getThirdPartySetting('display_layout', 'layout');
    if (empty($layout_id)) {
      return;
    }
    $layout_definition = $this->layoutPluginManager->getDefinition($layout_id, FALSE);
    if (!$layout_definition instanceof LayoutDefinition) {
      return;
    }
    $layout = $this->layoutPluginManager->createInstance($layout_id);
    assert($layout instanceof LayoutInterface);

    $layout_regions = [];
    foreach ($layout_definition->getRegionNames() as $region) {
      $layout_regions[$region] = [];
    }

    // Move fields from build root to layout region configuration.
    foreach ($display->getComponents() as $field_name => $component) {
      $region = $component['region'] ?? NULL;
      if (!empty($region) && isset($layout_regions[$region])) {
        // Pull field from build root.
        $field_render_array = $build[$field_name];
        unset($build[$field_name]);

        // Sort the field render array under it's configured layout region.
        $layout_regions[$region][$field_name] = $field_render_array;
      }
    }

    $layout_build = $layout->build($layout_regions);

    /* Nest layout build inside of entity display.
     * We do this so that the entity wrapper stays in place for caching,
     * contextual links, and theme hook suggestions.
     */
    $build['content'] = $layout_build;
  }

}
