<?php

/**
 * PHPTALView Test file
 *
 * CakePHP 2.1+
 * PHP 5.3+
 *
 * Copyright 2013, nojimage (http://php-tips.com/)
 * 
 * @filesource
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2013 nojimage (http://php-tips.com/)
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @link       http://php-tips.com/
 * @package    taltal
 * @subpackage taltal.tests.cases.views
 * @since      File available since Release 0.1
 */
App::uses('PhptalView', 'Taltal.View');
App::uses('Controller', 'Controller');

/**
 * TestPhptalView class
 *
 * @package       taltal
 * @subpackage    taltal.tests.cases.views
 */
class TestPhptalView extends PhptalView {

/**
 * getViewFileName method
 *
 * @param mixed $name
 * @access public
 * @return void
 */
	public function getViewFileName($name = null) {
		return $this->_getViewFileName($name);
	}

/**
 * getLayoutFileName method
 *
 * @param mixed $name
 * @access public
 * @return void
 */
	public function getLayoutFileName($name = null) {
		return $this->_getLayoutFileName($name);
	}

/**
 * loadHelpers method
 *
 * @param mixed $loaded
 * @param mixed $helpers
 * @param mixed $parent
 * @access public
 * @return void
 */
	public function loadHelpers() {
		return parent::loadHelpers();
	}

/**
 * paths method
 *
 * @param string $plugin
 * @param boolean $cached
 * @access public
 * @return void
 */
	public function paths($plugin = null, $cached = true) {
		return $this->_paths($plugin, $cached);
	}

/**
 * cakeError method
 *
 * @param mixed $method
 * @param mixed $messages
 * @access public
 * @return void
 */
	public function cakeError($method, $messages) {
		return compact('method', 'messages');
	}

}

/**
 * PhptalViewTest class
 *
 * @package       taltal
 * @subpackage    taltal.tests.cases.views
 * @property Controller $Controller
 * @property PeopleController $PeopleController
 * @property PhptalView $View
 */
class PhptalViewTest extends CakeTestCase {

	public $testAppPath = '';

	public function setUp() {
		parent::setUp();
		$this->testAppPath = dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS;
		App::build(array(
			'Controller' => array($this->testAppPath . 'Controller' . DS),
			'Model' => array($this->testAppPath . 'Model' . DS),
			'Plugin' => array($this->testAppPath . 'Plugin' . DS),
			'View' => array($this->testAppPath . 'View' . DS),), true);
		App::import('Controller', 'People');

		$request = new CakeRequest('/people/index');
		$request->params['action'] = 'index';

		$this->Controller = new Controller($request);
		$this->PeopleController = new PeopleController($request);
		$this->PeopleController->viewPath = 'people';
		$this->PeopleController->action = 'index';
		$this->PeopleController->index();
		$this->View = new TestPhptalView($this->PeopleController);
		$this->View->helpers = array('Html', 'Js', 'Form', 'Session');
		$this->View->Phptal->setForceReparse(true);
	}

	public function tearDown() {
		App::build();
		unset($this->View);
		unset($this->PeopleController);
		unset($this->Controller);
		parent::tearDown();
	}

	public function testGetTemplate() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Pages';
		$this->Controller->viewPath = 'pages';
		$this->Controller->action = 'display';
		$this->Controller->params['pass'] = array('home');

		$View = new TestPhptalView($this->Controller);
		$expected = $this->testAppPath . 'View' . DS . 'pages' . DS . 'home.zpt';
		$actual = $View->getViewFileName('home');
		$this->assertIdentical($expected, $actual);

		$expected = $this->testAppPath . 'View' . DS . 'Layouts' . DS . 'default.html';
		$actual = $this->View->getLayoutFileName();
		$this->assertIdentical($expected, $actual);
	}

	public function testGetTemplateHome1() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Pages';
		$this->Controller->viewPath = 'pages';
		$this->Controller->action = 'display';
		$this->Controller->params['pass'] = array('home_1');

		$View = new TestPhptalView($this->Controller);
		$expected = $this->testAppPath . 'View' . DS . 'pages' . DS . 'home_1.xhtml';
		$actual = $View->getViewFileName('home_1');
		$this->assertIdentical($expected, $actual);
	}

	public function testGetTemplateHome2() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Pages';
		$this->Controller->viewPath = 'pages';
		$this->Controller->action = 'display';
		$this->Controller->params['pass'] = array('home_2');

		$View = new TestPhptalView($this->Controller);
		$expected = $this->testAppPath . 'View' . DS . 'pages' . DS . 'home_2.html';
		$actual = $View->getViewFileName('home_2');
		$this->assertIdentical($expected, $actual);
	}

	public function testGetTemplateHome3() {
		$this->Controller->plugin = null;
		$this->Controller->name = 'Pages';
		$this->Controller->viewPath = 'pages';
		$this->Controller->action = 'display';
		$this->Controller->params['pass'] = array('home_3');

		$View = new TestPhptalView($this->Controller);
		$expected = $this->testAppPath . 'View' . DS . 'pages' . DS . 'home_3.ctp';
		$actual = $View->getViewFileName('home_3');
		$this->assertIdentical($expected, $actual);
	}

	public function testRender() {
		$actual = $this->View->render('index');
		$this->assertRegExp('/foo/', $actual);
		$this->assertRegExp('/bar/', $actual);
		$this->assertRegExp('/baz/', $actual);
		$this->assertRegExp('/quz/', $actual);
		$this->assertRegExp('/01-344-121-021/', $actual);
		$this->assertRegExp('/05-999-165-541/', $actual);
		$this->assertRegExp('/01-389-321-024/', $actual);
		$this->assertRegExp('/05-321-378-654/', $actual);
	}

	public function testRenderUsingHelper() {
		$actual = $this->View->render('index');
		$this->assertRegExp('/' . preg_quote('<a href="http://example.com">テキストリンク1</a>', '/') . '/', $actual);
		$this->assertRegExp('/' . preg_quote('<a href="http://example.com" style="font-size: 32px;">テキストリンク2</a>', '/') . '/', $actual);
	}

	public function testHelerModifier() {
		$actual = $this->View->render('helper');
		$this->assertRegExp('/' . preg_quote('<img src="' . $this->PeopleController->webroot . 'img/dummy01.gif" alt="" />', '/') . '/', $actual);
		$this->assertRegExp('/<form action="\/people(\/index)?(\?[^"]*)?" id="PersonIndexForm" method="post" accept-charset="utf-8">/', $actual);
		$this->assertRegExp('/' . preg_quote('<input name="data[Person][name]" type="text" id="PersonName"/>', '/') . '/', $actual);
	}

	public function testUrlModifier() {
		Router::setRequestInfo(array(array('controller' => 'people', 'action' => 'index'), array('base' => '/')));
		$this->View->set('posts', array(
			array('Post' => array('id' => 1, 'title' => 'Post Title 1')),
			array('Post' => array('id' => 2, 'title' => 'Post Title 2')),
			array('Post' => array('id' => 3, 'title' => 'Post Title 3')),
		));
		$actual = $this->View->render('url');
		$this->assertRegExp('!' . preg_quote('<a href="/posts/view/id:1">リンクA</a>', '!') . '!', $actual);
		$this->assertRegExp('!' . preg_quote('<a href="/people/view/2">リンクB</a>', '!') . '!', $actual);
		$this->assertRegExp('!<a href="http://(?:.+?)' . preg_quote('/posts/view/id:1">リンクC</a>', '!') . '!', $actual);
		$this->assertRegExp('!' . preg_quote('<a href="/posts/view/id:1">Post Title 1</a>', '!') . '!', $actual);
		$this->assertRegExp('!' . preg_quote('<a href="/posts/view/id:2">Post Title 2</a>', '!') . '!', $actual);
		$this->assertRegExp('!' . preg_quote('<a href="/posts/view/id:3">Post Title 3</a>', '!') . '!', $actual);
	}

	public function testCakeNamespace() {
		$actual = $this->View->render('namespace');
		$this->assertRegExp('/' . preg_quote('<img src="' . $this->PeopleController->webroot . 'img/dummy01.gif" alt="" />', '/') . '/', $actual);
		$this->assertRegExp('/<form action="\/people(\/index)?(\?[^"]*)?" id="PersonIndexForm" method="post" accept-charset="utf-8">/', $actual);
		$this->assertRegExp('/' . preg_quote('<input name="data[Person][name]" type="text" id="PersonName"/>', '/') . '/', $actual);
	}

	public function testCakeNamespaceSessionFlashIsEmpty() {
		$actual = $this->View->render('session_flash');
		$this->assertRegExp('!<body>\s+<div></div>\s+</body>!m', $actual);
	}

}
