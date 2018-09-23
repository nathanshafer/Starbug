<?php
namespace Starbug\Core;

class DropdownDisplay extends ItemDisplay {
  public $type = "list";
  public $template = "select.html";
  public function buildDisplay($options) {
    $this->attributes['class'][] = $this->template;
    if (!empty($options['attributes'])) {
      if (!empty($options['attributes']['class']) && !is_array($options['attributes']['class'])) $options['attributes']['class'] = [$options['attributes']['class']];
      $this->attributes = array_merge_recursive($this->attributes, $options['attributes']);
    }
    if (!empty($options['model']) && !empty($options['collection'])) {
      $this->model = $options['model'];
      $this->collection = $options['collection'];
    } elseif (!empty($options['options'])) {
      foreach ($options['options'] as $option) {
        $this->items[] = ["id" => $option, "label" => $option];
      }
    }
  }
}
