<?php

namespace Drupal\entity_reference_views\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Views;

/**
 * Plugin implementation of the 'entity_reference_views_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "entity_reference_views_autocomplete",
 *   label = @Translation("Autocomplete (Views)"),
 *   description = @Translation("An autocomplete text field with views to
 *   render."), field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class AutocompleteViewsWidget extends EntityReferenceAutocompleteWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'view' => NULL,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
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
  public function settingsSummary() {
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
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $referenced_entities = $items->referencedEntities();

    // See the difference between elements and element.
    $elements['target_id'] = $element['target_id'];

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
      $elements['target_id']['#ajax'] = [
        'callback' => [get_class($this), 'ajaxRefresh'],
        // This event covers add and modify case. But not delete case.
        'event' => 'autocompleteclose',
        'wrapper' => $wrapper_id,
      ];
    }

    return $elements;
  }

  /**
   * Update the table view element with new form input input.
   */
  public function ajaxRefresh(array $form, FormStateInterface $form_state) {
    $trigger_element = $form_state->getTriggeringElement();
    // Get the new entity ID from the form states via #parents.
    $values = $form_state->getValues();
    $referenced_id = NestedArray::getValue($values, $trigger_element['#parents']);

    // Use #array_parents to get the table element in the form.
    $parents = $trigger_element['#array_parents'];
    // Remove the autocomplete field as the table is in the same level.
    array_pop($parents);
    // Add the table to the parents.
    $parents[] = 'table';
    $element = NestedArray::getValue($form, $parents);

    // Attach the new reference as argument to the view.
    $element['#arguments'] = [$referenced_id];

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
