<?php

namespace Drupal\display_layout\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\display_layout\DisplayLayoutFormTrait;
use Drupal\layout_builder\Form\LayoutBuilderEntityViewDisplayForm;
use Drupal\layout_builder\LayoutBuilderEnabledInterface;

/**
 * Overrides the Layout Builder edit form for the EntityViewDisplay entity type.
 */
class LBEntityViewDisplayLayoutEditForm extends LayoutBuilderEntityViewDisplayForm {

  use DisplayLayoutFormTrait;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    if (!$this->isLayoutBuilderEnabled()) {
      $default_layout = $this->entity->getThirdPartySetting('display_layout', 'layout');
      $form['display_layout'] = $this->getLayoutForm($default_layout);
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegions() {
    if ($this->isLayoutBuilderEnabled()) {
      return parent::getRegions();
    }
    $layout_id = $this->entity->getThirdPartySetting('display_layout', 'layout') ?? NULL;
    return $this->getLayoutRegions($layout_id);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->entity->unsetThirdPartySetting('display_layout', 'layout');

    if (!$this->isLayoutBuilderEnabled()) {
      $layout_id = $form_state->getValue('display_layout_id');
      if (!empty($layout_id) && $layout_id != "_none") {
        $this->entity->setThirdPartySetting('display_layout', 'layout', $layout_id);
      }
    }
  }

  /**
   * Check if the entity view display uses layout builder.
   */
  protected function isLayoutBuilderEnabled() {
    return $this->entity instanceof LayoutBuilderEnabledInterface && $this->entity->isLayoutBuilderEnabled();
  }

}
