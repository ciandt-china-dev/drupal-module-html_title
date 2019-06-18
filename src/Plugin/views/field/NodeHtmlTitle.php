<?php

namespace Drupal\html_title\Plugin\views\field;

/**
 * @file
 * Contains \Drupal\html_title\Plugin\views\field\NodeHtmlTitle.
 */

use Drupal\node\Plugin\views\field\Node;
use Drupal\views\ResultRow;

/**
 * A field that displays node html title .
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("node_html_title")
 */
class NodeHtmlTitle extends Node {

  /**
   * Render title with html tags.
   */
  public function render(ResultRow $values) {
    $output = parent::render($values);
    $filter = \Drupal::service('html_title.filter');
    $elements = $filter->getAllowHtmlTags();

    if (count($elements)) {
      static $done = FALSE;

      // Ensure this block executes only once.
      if (!$done) {

        // Add permitted elements to options so they are not stripped later.
        $tags = array_map(function ($element) {
          return '<' . $element . '>';
        }, $elements);

        $this->options['alter']['preserve_tags'] .= ' ' . implode(' ', $tags);
        $done = TRUE;
      }

      $output = $filter->decodeToMarkup($output);
    }

    return $output;
  }

}
