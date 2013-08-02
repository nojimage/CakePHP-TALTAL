<?php

/**
 * PHPTAL Namespace Cake
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
class Cake_PHPTAL_Namespace_Cake extends PHPTAL_Namespace {

	const NAMESPACE_URI = 'http://php-tips.com/ns/phptal/cake';

	public function __construct() {
		parent::__construct('cake', self::NAMESPACE_URI);
		$this->addAttribute(new PHPTAL_NamespaceAttributeReplace('helper', 1));
	}

	public function createAttributeHandler(PHPTAL_NamespaceAttribute $att, PHPTAL_Dom_Element $tag, $expression) {
		$name = Inflector::classify($att->getLocalName());
		$class = 'Cake_PHPTAL_Php_Attribute_Cake_' . $name;

		include_once dirname(__FILE__) . DS . $class . '.php';
		$result = new $class($tag, $expression);
		return $result;
	}

}
