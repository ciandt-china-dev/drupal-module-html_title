<?php

namespace Drupal\html_title;

/**
 * @file
 * Contains \Drupal\html_title\HtmlTitleFilter.
 */

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
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Helper function to help filter out unwanted XSS opportunities.
   *
   * Use this function if you expect to have junk or incomplete HTML. It uses the
   *   same strategy as the "Fix HTML" filter option in configuring the HTML
   *   filter in the text format configuration.
   */
  private function filterXSS($title) {
    $dom = new \DOMDocument();
    // Ignore warnings during HTML soup loading.
    @$dom->loadHTML('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>' . $title . '</body></html>', LIBXML_NOENT);
    $xp = new \DOMXPath($dom);
    $q = "//body//text()";
    $nodes = $xp->query($q);

    foreach ($nodes as $n) {
      $n->nodeValue = htmlentities($n->nodeValue, ENT_QUOTES);
    }
    $body = str_replace(
      array('&amp;quot;', '&amp;#039;', '&amp;lsquo;', '&amp;rsquo;', '&amp;ldquo;', '&amp;rdquo;'),
      array('&quot;', '&#039;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;'),
      $dom->saveHTML($dom->getElementsByTagName('body')->item(0))
    );
    return Xss::filter($body, $this->getAllowHtmlTags());
  }

  /**
   * Filter string with allowed HTML tags.
   */
  public function decodeToText($str) {
    return $this->filterXSS(Html::decodeEntities((string) $str));
  }

  /**
   * Filter string with allowed HTML tags.
   */
  public function decodeToMarkup($str) {
    return Markup::create($this->decodeToText($str));
  }

  /**
   * Get allowed HTML tags array.
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
