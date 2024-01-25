<?php

namespace Drupal\entity_reference_views\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\select2\Plugin\Field\FieldWidget\Select2EntityReferenceWidget;
use Drupal\views\Views;

/**
 * Plugin implementation of the 'select2' widget.
 *
 * @FieldWidget(
 *   id = "entity_reference_views_select2",
 *   label = @Translation("Select2 (view)"),
 *   field_types = {
 *     "entity_reference",
 *   },
 *   multiple_values = TRUE
 * )
 */
class Select2ViewsWidget extends Select2EntityReferenceWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
        'view' => NULL,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $element = parent::settingsForm($form, $form_state);

    $element['view'] = [
      '#type' => 'select',
      '#options' => $this->getViewsList(),
      '#title' => $this->t('View'),
      '#default_value' => $this->getSetting('view'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = parent::settingsSummary();
    $view_id = $this->getSetting('view');

    if (empty($view_id)) {
      $summary[] = t('No view');
      return $summary;
    }

    $summary[] = t('View: @view', ['@view' => $view_id]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $referenced_entities = $items->referencedEntities();

    // See the difference between elements and element.
    $elements['parent_widget'] = $element;

    if (!empty($this->getSetting('view'))) {
      [$view_id, $display_id] = explode(':', $this->getSetting('view'));

      if (empty($view = Views::getView($view_id))) {
        return $elements;
      }
      $args = [];
      if (!empty($referenced_entities[$delta])) {
        $args = [$referenced_entities[$delta]->id()];
      }

      // Make the wrapper element specific, so that more than one element could
      // exist on a page.
      // Note: we can't use Html::UniqueId() here as it wouldn't work once the
      // old ID has been replaced.
      $wrapper_id = 'efv-' . $items->getName() . '-ajax-target';

      $elements['table'] = [
        '#type' => 'view',
        '#name' => $view->storage->id(),
        '#display_id' => $display_id,
        '#arguments' => $args,
        '#embed' => TRUE,
        '#view' => $view,
        '#prefix' => '<div id="' . $wrapper_id . '">',
        '#suffix' => '</div>',
      ];

      $elements['parent_widget']['#ajax'] = [
        'callback' => [get_class($this), 'ajaxRefresh'],
        'event' => 'change',
        'wrapper' => $wrapper_id,
      ];
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return $values['parent_widget'];
  }

  /**
   * Update the table view element with new form input input.
   */
  public static function ajaxRefresh(array $form, FormStateInterface $form_state) {
    $trigger_element = $form_state->getTriggeringElement();

    // Use #array_parents to get the table element in the form.
    $parents = $trigger_element['#array_parents'];
    // Remove the autocomplete field as the table is in the same level.
    array_pop($parents);
    // Add the table to the parents.
    $parents[] = 'table';
    $element = NestedArray::getValue($form, $parents);

    // Get the new entity ID from the form states via #parents.
    $values = $form_state->getValues();
    $referenced_id = NestedArray::getValue($values, $trigger_element['#parents']);
    if (isset($referenced_id[0]['target_id'])) {
      // Attach the new reference as argument to the view.
      $element['#arguments'] = [$referenced_id[0]['target_id']];
    }

    return $element;
  }

  protected function getViewsList() {
    $entity_type_id = $this->getFieldSetting('target_type');

    $displays = Views::getApplicableViews('entity_reference_field_display');

    // Filter views that list the entity type we want, and group the separate
    // displays by view.
    $entity_type = $this->entityTypeManager()->getDefinition($entity_type_id);
    $view_storage = $this->entityTypeManager()->getStorage('view');

    $options = [];
    foreach ($displays as $data) {
      [$view_id, $display_id] = $data;
      $view = $view_storage->load($view_id);
      if (in_array($view->get('base_table'), [
        $entity_type->getBaseTable(),
        $entity_type->getDataTable(),
      ])) {
        $display = $view->get('display');
        $options[$view_id . ':' . $display_id] = $view_id . ' - ' . $display[$display_id]['display_title'];
      }
    }
    return $options;
  }

  /**
   * Provides entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   Entity type manager.
   */
  protected function entityTypeManager() {
    return \Drupal::entityTypeManager();
  }

}
