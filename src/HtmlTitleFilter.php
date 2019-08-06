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
      array('&amp;amp;', '&amp;quot;', '&amp;#039;', '&amp;Agrave;', '&amp;Aacute;', '&amp;Acirc;', '&amp;Atilde;', '&amp;Auml;', '&amp;Aring;', '&amp;AElig;', '&amp;Ccedil;', '&amp;Egrave;', '&amp;Eacute;', '&amp;Ecirc;', '&amp;Euml;', '&amp;Igrave;', '&amp;Iacute;', '&amp;Icirc;', '&amp;Iuml;', '&amp;ETH;', '&amp;Ntilde;', '&amp;Ograve;', '&amp;Oacute;', '&amp;Ocirc;', '&amp;Otilde;', '&amp;Ouml;', '&amp;Oslash;', '&amp;Ugrave;', '&amp;Uacute;', '&amp;Ucirc;', '&amp;Uuml;', '&amp;Yacute;', '&amp;THORN;', '&amp;szlig;', '&amp;agrave;', '&amp;aacute;', '&amp;acirc;', '&amp;atilde;', '&amp;auml;', '&amp;aring;', '&amp;aelig;', '&amp;ccedil;', '&amp;egrave;', '&amp;eacute;', '&amp;ecirc;', '&amp;euml;', '&amp;igrave;', '&amp;iacute;', '&amp;icirc;', '&amp;iuml;', '&amp;eth;', '&amp;ntilde;', '&amp;ograve;', '&amp;oacute;', '&amp;ocirc;', '&amp;otilde;', '&amp;ouml;', '&amp;oslash;', '&amp;ugrave;', '&amp;uacute;', '&amp;ucirc;', '&amp;uuml;', '&amp;yacute;', '&amp;thorn;', '&amp;yuml;', '&amp;nbsp;', '&amp;iexcl;', '&amp;cent;', '&amp;pound;', '&amp;curren;', '&amp;yen;', '&amp;brvbar;', '&amp;sect;', '&amp;uml;', '&amp;copy;', '&amp;ordf;', '&amp;laquo;', '&amp;not;', '&amp;shy;', '&amp;reg;', '&amp;macr;', '&amp;deg;', '&amp;plusmn;', '&amp;sup2;', '&amp;sup3;', '&amp;acute;', '&amp;micro;', '&amp;para;', '&amp;cedil;', '&amp;sup1;', '&amp;ordm;', '&amp;raquo;', '&amp;frac14;', '&amp;frac12;', '&amp;frac34;', '&amp;iquest;', '&amp;times;', '&amp;divide;', '&amp;forall;', '&amp;part;', '&amp;exist;', '&amp;empty;', '&amp;nabla;', '&amp;isin;', '&amp;notin;', '&amp;ni;', '&amp;prod;', '&amp;sum;', '&amp;minus;', '&amp;lowast;', '&amp;radic;', '&amp;prop;', '&amp;infin;', '&amp;ang;', '&amp;and;', '&amp;or;', '&amp;cap;', '&amp;cup;', '&amp;int;', '&amp;there4;', '&amp;sim;', '&amp;cong;', '&amp;asymp;', '&amp;ne;', '&amp;equiv;', '&amp;le;', '&amp;ge;', '&amp;sub;', '&amp;sup;', '&amp;nsub;', '&amp;sube;', '&amp;supe;', '&amp;oplus;', '&amp;otimes;', '&amp;perp;', '&amp;sdot;', '&amp;Alpha;', '&amp;Beta;', '&amp;Gamma;', '&amp;Delta;', '&amp;Epsilon;', '&amp;Zeta;', '&amp;Eta;', '&amp;Theta;', '&amp;Iota;', '&amp;Kappa;', '&amp;Lambda;', '&amp;Mu;', '&amp;Nu;', '&amp;Xi;', '&amp;Omicron;', '&amp;Pi;', '&amp;Rho;', '&amp;Sigma;', '&amp;Tau;', '&amp;Upsilon;', '&amp;Phi;', '&amp;Chi;', '&amp;Psi;', '&amp;Omega;', '&amp;alpha;', '&amp;beta;', '&amp;gamma;', '&amp;delta;', '&amp;epsilon;', '&amp;zeta;', '&amp;eta;', '&amp;theta;', '&amp;iota;', '&amp;kappa;', '&amp;lambda;', '&amp;mu;', '&amp;nu;', '&amp;xi;', '&amp;omicron;', '&amp;pi;', '&amp;rho;', '&amp;sigmaf;', '&amp;sigma;', '&amp;tau;', '&amp;upsilon;', '&amp;phi;', '&amp;chi;', '&amp;psi;', '&amp;omega;', '&amp;thetasym;', '&amp;upsih;', '&amp;piv;', '&amp;OElig;', '&amp;oelig;', '&amp;Scaron;', '&amp;scaron;', '&amp;Yuml;', '&amp;fnof;', '&amp;circ;', '&amp;tilde;', '&amp;ensp;', '&amp;emsp;', '&amp;thinsp;', '&amp;zwnj;', '&amp;zwj;', '&amp;lrm;', '&amp;rlm;', '&amp;ndash;', '&amp;mdash;', '&amp;lsquo;', '&amp;rsquo;', '&amp;sbquo;', '&amp;ldquo;', '&amp;rdquo;', '&amp;bdquo;', '&amp;dagger;', '&amp;Dagger;', '&amp;bull;', '&amp;hellip;', '&amp;permil;', '&amp;prime;', '&amp;Prime;', '&amp;lsaquo;', '&amp;rsaquo;', '&amp;oline;', '&amp;euro;', '&amp;trade;', '&amp;larr;', '&amp;uarr;', '&amp;rarr;', '&amp;darr;', '&amp;harr;', '&amp;crarr;', '&amp;lceil;', '&amp;rceil;', '&amp;lfloor;', '&amp;rfloor;', '&amp;loz;', '&amp;spades;', '&amp;clubs;', '&amp;hearts;', '&amp;diams;'),
      array('&amp;', '&quot;', '&#039;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&times;', '&divide;', '&forall;', '&part;', '&exist;', '&empty;', '&nabla;', '&isin;', '&notin;', '&ni;', '&prod;', '&sum;', '&minus;', '&lowast;', '&radic;', '&prop;', '&infin;', '&ang;', '&and;', '&or;', '&cap;', '&cup;', '&int;', '&there4;', '&sim;', '&cong;', '&asymp;', '&ne;', '&equiv;', '&le;', '&ge;', '&sub;', '&sup;', '&nsub;', '&sube;', '&supe;', '&oplus;', '&otimes;', '&perp;', '&sdot;', '&Alpha;', '&Beta;', '&Gamma;', '&Delta;', '&Epsilon;', '&Zeta;', '&Eta;', '&Theta;', '&Iota;', '&Kappa;', '&Lambda;', '&Mu;', '&Nu;', '&Xi;', '&Omicron;', '&Pi;', '&Rho;', '&Sigma;', '&Tau;', '&Upsilon;', '&Phi;', '&Chi;', '&Psi;', '&Omega;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&thetasym;', '&upsih;', '&piv;', '&OElig;', '&oelig;', '&Scaron;', '&scaron;', '&Yuml;', '&fnof;', '&circ;', '&tilde;', '&ensp;', '&emsp;', '&thinsp;', '&zwnj;', '&zwj;', '&lrm;', '&rlm;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&dagger;', '&Dagger;', '&bull;', '&hellip;', '&permil;', '&prime;', '&Prime;', '&lsaquo;', '&rsaquo;', '&oline;', '&euro;', '&trade;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&crarr;', '&lceil;', '&rceil;', '&lfloor;', '&rfloor;', '&loz;', '&spades;', '&clubs;', '&hearts;', '&diams;'),
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
