<?php

/**
 * @file
 * HtmlTitleFilter 
 */

namespace Drupal\html_title;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Component\Utility\Xss;
use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Markup;

/**
* Drupal\html_titleHtmlTitleFilter.
*/
class HtmlTitleFilter {
  
  protected $configFactory;

  /**
   * Construct.
   */
  function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Filte string with allow html tags.
   */
  public function decodeToText($str) {
    return Xss::filter(Html::decodeEntities((string)$str), $this->getAllowHtmlTags());
  }

  public function decodeToMarkup($str) {
    return Markup::create($this->decodeToText($str));
  }

  /**
   * Get allow html tags array.
   */
  public function getAllowHtmlTags() {
    $tags = [];
    $html = str_replace('>', ' />', $this->configFactory->get('html_title.setting')->get('allow_html_tags'));

    $body_child_nodes = Html::load($html)->getElementsByTagName('body')->item(0)->childNodes;

    foreach ($body_child_nodes as $node) {
      if ($node->nodeType === XML_ELEMENT_NODE) {
        $tags[] = $node->tagName;
      }
    }

    return $tags;
  }

}
