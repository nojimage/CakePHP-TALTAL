<?php

/**
 * PHPTAL View
 *
 * CakePHP 2.1+
 * PHP 5.3+
 *
 * Copyright 2013, nojimage (http://php-tips.com/)
 * 
 * @filesource
 * @version    2.1.0
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2013 nojimage (http://php-tips.com/)
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://php-tips.com/
 * @package    taltal
 * @subpackage taltal.views
 * @since      File available since Release 0.1
 */
App::uses('View', 'View');
if (!class_exists('PHPTAL')) {
	App::import('Vendor', 'Taltal.PHPTAL', array('file' => 'phptal' . DS . 'classes' . DS . 'PHPTAL.php'));
}
App::uses('Cake_PHPTAL_Namespace_Cake', 'Taltal.Lib');

/**
 * PHPTALView
 * 
 * @property PHPTAL $Phptal
 */
class PhptalView extends View {

/**
 * @var PHPTAL_Namespace_Cake
 */
	protected $_namespaceCake;

/**
 * PHPTALView constructor
 *
 * @param Controller $controller
 */
	public function __construct($controller) {
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

		foreach ($helpers as $properties) {
			list(, $class) = pluginSplit($properties['class']);
			$helper = $this->{$class};
			$class = Inflector::variable($class);

			if (!isset($this->viewVars[$class])) {
				$this->viewVars[$class] = $helper;
			}

			$this->Phptal->set($class, $helper);
			$this->_createHelperModifier($class);
		}

		unset($class, $helpers, $helper);
	}

/**
 * Sandbox method to evaluate a template / view script in.
 *
 * @param string $viewFn Filename of the view
 * @param array $dataForView Data to include in rendered view.
 *    If empty the current View::$viewVars will be used.
 * @return string Rendered output
 */
	protected function _evaluate($viewFile, $dataForView) {
		if (!preg_match('/(?:\.zpt|\.xhtml|\.html)$/', $viewFile)) {
			return parent::_evaluate($viewFile, $dataForView);
		}

		if (empty($dataForView)) {
			$dataForView = $this->viewVars;
		}

		// -- set template
		$this->Phptal->setTemplate($viewFile);

		// -- set values
		foreach ($dataForView as $key => $value) {
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
		return $out;
	}

/**
 * Get the extensions that view files can use.
 *
 * @return array Array of extensions view files use.
 * @access protected
 */
	protected function _getExtensions() {
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
		if (isset($this->_namespaceCake) || $defs->isHandledNamespace(Cake_PHPTAL_Namespace_Cake::NAMESPACE_URI)) {
			return;
		}
		$this->_namespaceCake = new Cake_PHPTAL_Namespace_Cake();
		$defs->registerNamespace($this->_namespaceCake);
	}

}
