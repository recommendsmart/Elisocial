<?php

namespace Drupal\social_lets_connect_usage\Plugin\ShareUsageDataPlugin;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\social_lets_connect_usage\Plugin\ShareUsageDataPluginBase;

/**
 * Provides a 'ModulesInstalled' share usage data plugin.
 *
 * @ShareUsageDataPlugin(
 *  id = "modules_installed",
 *  label = @Translation("Modules installed"),
 *  setting = "modules_installed",
 *  weight = -420,
 * )
 */
class ModulesInstalled extends ShareUsageDataPluginBase {

  /**
   * Get the value.
   *
   * @return array
   *   $json array.
   */
  public function getValue() {
    $value = [];
    $modules = \Drupal::service('extension.list.module')->reset()->getList();
    $modules = $this->getExtensionsInfo($modules);
    $theme_data = \Drupal::service('theme_handler')->rebuildThemeData();
    $themes = $this->getExtensionsInfo($theme_data);
    $profiles = \Drupal::service('extension.list.profile')->reset()->getList();
    $profiles = $this->getExtensionsInfo($profiles);

    $value['modules'] = $modules;
    $value['profiles'] = $profiles;
    $value['themes'] = [
      'themes' => $themes,
      'default' => \Drupal::service('theme_handler')->getDefault(),
    ];

    return $value;
  }

  /**
   * Get safe extensions info.
   *
   * @param \Drupal\Core\Extension\Extension[] $projects
   *   An array of extensions.
   *
   * @return array
   *   Returns an array of projects with safe values.
   */
  private function getExtensionsInfo(array $projects): array {
    $value = [];
    uasort($projects, [ModuleExtensionList::class, 'sortByName']);
    foreach ($projects as $project) {
      $name = $project->getName();
      $value[$name] = [
        'type' => $project->getType(),
        'name' => $name,
      ];
    }
    return $value;
  }

}
