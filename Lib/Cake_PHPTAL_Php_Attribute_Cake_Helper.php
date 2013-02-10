<?php

/**
 * PHPTAL Attribute Cake Helper
 *
 * CakePHP 2.1+
 * PHP 5.2+
 *
 * Copyright 2013, nojimage (http://php-tips.com/)
 * 
 * @filesource
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2013 nojimage (http://php-tips.com/)
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://php-tips.com/
 * @package    taltal
 * @subpackage taltal.libs
 * @since      File available since Release 0.3
 */
class Cake_PHPTAL_Php_Attribute_Cake_Helper extends PHPTAL_Php_Attribute_TAL_Replace {

	protected $_echoType = PHPTAL_Php_Attribute::ECHO_STRUCTURE;

	protected function extractEchoType($expression) {
		$echoType = self::ECHO_STRUCTURE;
		$expression = trim($expression);
		if (preg_match('/^(text|structure)\s+(.*?)$/ism', $expression, $m)) {
			list(, $echoType, $expression) = $m;
		}
		$this->_echoType = strtolower($echoType);
		$expression .= " . ''";
		return trim($expression);
	}

}
