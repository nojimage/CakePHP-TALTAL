<?php
/**
 * PHPTAL templating engine
 *
 * PHP Version 5
 *
 * @category HTML
 * @package  PHPTAL
 * @author   Laurent Bedubourg <lbedubourg@motion-twin.com>
 * @author   Kornel Lesiński <kornel@aardvarkmedia.co.uk>
 * @license  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @version  SVN: $Id: I18NDummyTranslator.php 868 2010-05-25 22:27:39Z kornel $
 * @link     http://phptal.org/
 */


class DummyTranslator implements PHPTAL_TranslationService
{
    public $vars = array();
    public $translations = array();
    public $domain;

    public function setLanguage()
    {
    }

    public function setEncoding($enc) {}

    public function useDomain($domain)
    {
        $this->domain = $domain;
    }

    public function setVar($key, $value)
    {
        $this->vars[$key] = $value;
    }

    public function setTranslation($key, $translation)
    {
        $this->translations[$key] = $translation;
    }

    public function translate($key, $escape = true)
    {
        if (array_key_exists($key, $this->translations)) {
            $v = $this->translations[$key];
        } else {
            $v = $key;
        }

        if ($escape) $v = htmlspecialchars($v);

        while (preg_match('/\$\{(.*?)\}/sm', $v, $m)) {
            list($src, $var) = $m;
            if (!isset($this->vars[$var])) return "!*$var* is not defined!";
            $v = str_replace($src, $this->vars[$var], $v);
        }


        return $v;
    }
}
