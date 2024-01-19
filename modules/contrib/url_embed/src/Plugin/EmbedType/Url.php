<?php

namespace Drupal\url_embed\Plugin\EmbedType;

use Drupal\embed\EmbedType\EmbedTypeBase;

/**
 * URL embed type.
 *
 * @EmbedType(
 *   id = "url",
 *   label = @Translation("URL")
 * )
 */
class Url extends EmbedTypeBase {

  /**
   * {@inheritdoc}
   */
  public function getDefaultIconUrl() {
    return "{$this->getModulePath('url_embed')}/js/ckeditor5_plugins/urlembed/urlembed.svg";
  }

}
