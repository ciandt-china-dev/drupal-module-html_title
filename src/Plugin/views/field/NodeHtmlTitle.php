<?php

/**
 * @file
 * Contains \Drupal\html_title\views\Plugin\views\field\NodeHtmlTitle.
 */

namespace Drupal\html_title\Plugin\views\field;

use Drupal\node\Plugin\views\field\Node;
use Drupal\Core\Render\Markup;
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
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $output = parent::render($values);
    $filter = \Drupal::service('html_title.filter');
    $elements = $filter->getAllowHtmlTags();

    if (count($elements)) {
      static $done = FALSE;

      // Ensure this block executes only once
      if (!$done) {

        // Add permitted elements to options so they are not stripped later
        $tags = array();
        foreach ($elements as $element) {
          $tags[] = '<'. $element .'>';
        }

        $this->options['alter']['preserve_tags'] .= ' '. implode(' ', $tags);
        $done = TRUE;
      }

      $output = $filter->decodeToMarkup($output);

      /*// Decode permitted HTML elements
      $pattern = "/&lt;(\/?)(". implode('|', $elements) .")&gt;/i";
      $output = preg_replace($pattern, '<$1$2>', $output);

      // Decode HTML character entities
      $pattern = "/&amp;([a-z0-9#]+);/";
      $output = preg_replace($pattern, '&$1;', $output);*/
    }

    return $output;
  }

}
