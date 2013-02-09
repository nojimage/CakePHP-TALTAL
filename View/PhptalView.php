<?php

/**
 * PHPTAL View
 *
 * CakePHP 2.0+
 * PHP 5.2+
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 * 
 * @filesource
 * @version    0.3.1
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2011 nojimage (http://php-tips.com/)
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://php-tips.com/
 * @package    taltal
 * @subpackage taltal.views
 * @since      File available since Release 0.1
 */
if (!class_exists('ThemeView')) {
    App::import('View', 'Theme');
}
if (!class_exists('PHPTAL')) {
    App::import('Vendor', 'Taltal.PHPTAL', array('file' => 'phptal' . DS . 'PHPTAL.php'));
}

include_once dirname(dirname(__FILE__)) . DS . 'Lib' . DS . 'PHPTAL_Namespace_Cake.php';

/**
 * PHPTALView
 * 
 * @property PHPTAL $Phptal
 */
class PhptalView extends ThemeView {

    /**
     * @var PHPTAL_Namespace_Cake
     */
    protected $_namespaceCake;

    /**
     * PHPTALView constructor
     *
     * @param Controller $controller
     */
    function __construct($controller) {
        parent::__construct($controller);
        $this->Phptal = new PHPTAL();
        $this->Phptal->setEncoding(Configure::read('App.encoding'));
        $this->Phptal->setPhpCodeDestination(CACHE . 'views');
        $this->_createUrlModifier();
        $this->_registerNamespace();
    }

    public function loadHelpers() {
        parent::loadHelpers();
		$helpers = HelperCollection::normalizeObjectArray($this->helpers);

        foreach ($helpers as $k => $v) {
            $name = Inflector::variable($k);
            $helper = $this->{$v['class']};

            if (!isset($this->viewVars[$name]))
                $this->viewVars[$name] = $helper;

            $this->Phptal->set($name, $helper);
            $this->_createHelperModifier($name);
        }

        unset($name, $helpers, $k, $v, $helper);
    }

    /**
     * Renders and returns output for given view filename with its
     * array of data.
     *
     * @param string $___viewFn Filename of the view
     * @param array $___dataForView Data to include in rendered view
     * @return string Rendeed output
     * @access protected
     */
    protected function _render($___viewFn, $___dataForView = array()) {

        if (!preg_match('/(?:\.zpt|\.xhtml|\.html)$/', $___viewFn)) {
            return parent::_render($___viewFn, $___dataForView);
        }

		if (empty($___dataForView)) {
			$___dataForView = $this->viewVars;
		}

        // -- set template
        $this->Phptal->setTemplate($___viewFn);

        // -- set values
        foreach ($___dataForView as $key => $value) {
            $this->Phptal->set($key, $value);
        }
        // set this View class
        $this->Phptal->set('view', $this);

        // -- render
        ob_start();
        try {
            echo $this->Phptal->execute();
        } catch (Exception $e) {
            debug($e->__toString());
        }

        $out = ob_get_clean();

        $caching = (
                isset($this->loaded['cache']) &&
                (($this->cacheAction != false)) && (Configure::read('Cache.check') === true)
                );

        if ($caching) {
            if (is_a($this->loaded['cache'], 'CacheHelper')) {
                $cache = $this->loaded['cache'];
                $cache->base = $this->base;
                $cache->here = $this->here;
                $cache->helpers = $this->helpers;
                $cache->action = $this->action;
                $cache->controllerName = $this->name;
                $cache->layout = $this->layout;
                $cache->cacheAction = $this->cacheAction;
                $cache->viewVars = $this->viewVars;
                $cache->cache($___viewFn, $out, $cached);
            }
        }
        return $out;
    }

    /**
     * Get the extensions that view files can use.
     *
     * @return array Array of extensions view files use.
     * @access protected
     */
    function _getExtensions() {
        $exts = parent::_getExtensions();
        if ($this->ext !== '.html') {
            array_unshift($exts, '.html');
        }
        if ($this->ext !== '.xhtml') {
            array_unshift($exts, '.xhtml');
        }
        if ($this->ext !== '.zpt') {
            array_unshift($exts, '.zpt');
        }
        return $exts;
    }

    /**
     * create helper modifier
     *
     * @param string $helperName
     */
    protected function _createHelperModifier($helperName) {
        $functionName = 'phptal_tales_' . Inflector::underscore($helperName);
        $helperName = Inflector::camelize($helperName);
        if (!function_exists($functionName)) {
            $func = "function {$functionName}(\$src, \$nothrow) {
                \$src = trim(\$src);
                \$src = 'php:view.{$helperName}.' . \$src;
                return phptal_tales(\$src, \$nothrow);
            }";
            eval($func);
        }
    }

    /**
     * create url modifier
     */
    protected function _createUrlModifier() {
        if (!function_exists('phptal_tales_url')) {

            function phptal_tales_url($src, $nothrow) {
                $src = trim($src);
                if (preg_match('/^[a-z0-9_]+:/i', $src)) {
                    $src = phptal_tales($src, $nothrow);
                } else if (preg_match('/^[a-z0-9_]+\(/i', $src)) {
                    $src = phptal_tales('php:' . $src, $nothrow);
                } else {
                    $src = "'" . $src . "'";
                }
                return 'Router::url(' . $src . ')';
            }

        }
        if (!function_exists('phptal_tales_fullurl')) {

            function phptal_tales_fullurl($src, $nothrow) {
                $src = trim($src);
                if (preg_match('/^[a-z0-9_]+:/i', $src)) {
                    $src = phptal_tales($src, $nothrow);
                } else if (preg_match('/^[a-z0-9_]+\(/i', $src)) {
                    $src = phptal_tales('php:' . $src, $nothrow);
                } else {
                    $src = "'" . $src . "'";
                }
                return 'Router::url(' . $src . ', true)';
            }

        }
    }

    /**
     * register cake namespace
     */
    protected function _registerNamespace() {
        $defs = PHPTAL_Dom_Defs::getInstance();
        /* @var $defs PHPTAL_Dom_Defs */
        if (isset($this->_namespaceCake) || $defs->isHandledNamespace(PHPTAL_Namespace_Cake::NAMESPACE_URI)) {
            return;
        }
        $this->_namespaceCake = new PHPTAL_Namespace_Cake();
        $defs->registerNamespace($this->_namespaceCake);
    }

}
