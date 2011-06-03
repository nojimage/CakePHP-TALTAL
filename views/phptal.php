<?php

/**
 * PHPTAL View
 *
 * CakePHP 1.3+
 * PHP 5.2+
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 * 
 * @filesource
 * @version    0.1
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

/**
 * PHPTALView
 * 
 * @property PHPTAL $Phptal
 */
class PhptalView extends ThemeView {

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
    }

    /**
     * Renders and returns output for given view filename with its
     * array of data.
     *
     * @param string $___viewFn Filename of the view
     * @param array $___dataForView Data to include in rendered view
     * @param boolean $loadHelpers Boolean to indicate that helpers should be loaded.
     * @param boolean $cached Whether or not to trigger the creation of a cache file.
     * @return string Rendered output
     * @access protected
     */
    function _render($___viewFn, $___dataForView, $loadHelpers = true, $cached = false) {

        if (!preg_match('/(?:\.zpt|\.xhtml|\.html)$/', $___viewFn)) {
            return parent::_render($___viewFn, $___dataForView, $loadHelpers, $cached);
        }

        $loadedHelpers = array();

        if ($this->helpers != false && $loadHelpers === true) {
            $loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);
            $helpers = array_keys($loadedHelpers);
            $helperNames = array_map(array('Inflector', 'variable'), $helpers);

            for ($i = count($helpers) - 1; $i >= 0; $i--) {
                $name = $helperNames[$i];
                $helper = $loadedHelpers[$helpers[$i]];

                if (!isset($___dataForView[$name])) {
                    ${$name} = $helper;
                }
                $this->loaded[$helperNames[$i]] = $helper;
                $this->{$helpers[$i]} = $helper;
            }
            $this->_triggerHelpers('beforeRender');
            unset($name, $loadedHelpers, $helpers, $i, $helperNames, $helper);
        }

        // -- set template
        $this->Phptal->setTemplate($___viewFn);

        // -- set values
        foreach ($___dataForView as $key => $value) {
            $this->Phptal->set($key, $value);
        }
        // set helpers
        foreach ($this->loaded as $helperName => $helper) {
            $this->Phptal->set($helperName, $helper);
            $this->_createHelperModifier($helperName);
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

        if ($loadHelpers === true) {
            $this->_triggerHelpers('afterRender');
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
    
}
