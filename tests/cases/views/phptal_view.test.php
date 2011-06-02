<?php

/**
 * PHPTALView Test file
 *
 * CakePHP 1.3+
 * PHP 5.2+
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2011 nojimage (http://php-tips.com/)
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://php-tips.com/
 * @package    taltal
 * @subpackage taltal.tests.cases.views
 * @since      File available since Release 0.1
 */
App::import('View', array('Taltal.Phptal'));
App::import('Core', array('View', 'Controller'));

if (!class_exists('ErrorHandler')) {
    App::import('Core', array('Error'));
}

/**
 * PhptalViewPostsController class
 *
 * @package       taltal
 * @subpackage    taltal.tests.cases.views
 */
class PhptalViewPostsController extends Controller {

    public $name = 'Posts';
    public $uses = null;
    public $helpers = array('Html', 'Js');

    /**
     * index method
     *
     * @access public
     * @return void
     */
    function index() {
        $people = array(
            array('name' => 'foo', 'phone' => '01-344-121-021'),
            array('name' => 'bar', 'phone' => '05-999-165-541'),
            array('name' => 'baz', 'phone' => '01-389-321-024'),
            array('name' => 'quz', 'phone' => '05-321-378-654'),
        );
        $this->set(compact('people'));
    }

}

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
    function getViewFileName($name = null) {
        return $this->_getViewFileName($name);
    }

    /**
     * getLayoutFileName method
     *
     * @param mixed $name
     * @access public
     * @return void
     */
    function getLayoutFileName($name = null) {
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
    function loadHelpers(&$loaded, $helpers, $parent = null) {
        return $this->_loadHelpers($loaded, $helpers, $parent);
    }

    /**
     * paths method
     *
     * @param string $plugin
     * @param boolean $cached
     * @access public
     * @return void
     */
    function paths($plugin = null, $cached = true) {
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
    function cakeError($method, $messages) {
        return compact('method', 'messages');
    }

}

/**
 * PhptalViewTest class
 *
 * @package       taltal
 * @subpackage    taltal.tests.cases.views
 * @property Controller $Controller
 * @property PhptalViewPostsController $PostsController
 * @property PhptalView $View
 */
class PhptalViewTest extends CakeTestCase {

    public $testAppPath = '';

    /**
     * setUp method
     *
     * @access public
     * @return void
     */
    function setUp() {
        Router::reload();
        $this->Controller = new Controller();
        $this->PostsController = new PhptalViewPostsController();
        $this->PostsController->viewPath = 'posts';
        $this->PostsController->index();
        $this->View = new TestPhptalView($this->PostsController);
        $this->testAppPath = dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS;
    }

    /**
     * tearDown method
     *
     * @access public
     * @return void
     */
    function tearDown() {
        unset($this->View);
        unset($this->PostsController);
        unset($this->Controller);
    }

    /**
     * endTest
     *
     * @access public
     * @return void
     */
    function startTest($method) {
        App::build(array(
            'plugins' => array($this->testAppPath . 'plugins' . DS),
            'views' => array($this->testAppPath . 'views' . DS),
                ), true);
    }

    /**
     * endTest
     *
     * @access public
     * @return void
     */
    function endTest() {
        App::build();
    }

    function testGetTemplate() {
        $this->Controller->plugin = null;
        $this->Controller->name = 'Pages';
        $this->Controller->viewPath = 'pages';
        $this->Controller->action = 'display';
        $this->Controller->params['pass'] = array('home');

        $View = new TestPhptalView($this->Controller);
        $expected = $this->testAppPath . 'views' . DS . 'pages' . DS . 'home.zpt';
        $actual = $View->getViewFileName('home');
        $this->assertIdentical($expected, $actual);

        $expected = $this->testAppPath . 'views' . DS . 'layouts' . DS . 'default.html';
        $actual = $this->View->getLayoutFileName();
        $this->assertIdentical($expected, $actual);
    }

    function testGetTemplate_home_1() {
        $this->Controller->plugin = null;
        $this->Controller->name = 'Pages';
        $this->Controller->viewPath = 'pages';
        $this->Controller->action = 'display';
        $this->Controller->params['pass'] = array('home_1');

        $View = new TestPhptalView($this->Controller);
        $expected = $this->testAppPath . 'views' . DS . 'pages' . DS . 'home_1.xhtml';
        $actual = $View->getViewFileName('home_1');
        $this->assertIdentical($expected, $actual);
    }

    function testGetTemplate_home_2() {
        $this->Controller->plugin = null;
        $this->Controller->name = 'Pages';
        $this->Controller->viewPath = 'pages';
        $this->Controller->action = 'display';
        $this->Controller->params['pass'] = array('home_2');

        $View = new TestPhptalView($this->Controller);
        $expected = $this->testAppPath . 'views' . DS . 'pages' . DS . 'home_2.html';
        $actual = $View->getViewFileName('home_2');
        $this->assertIdentical($expected, $actual);
    }

    function testGetTemplate_home_3() {
        $this->Controller->plugin = null;
        $this->Controller->name = 'Pages';
        $this->Controller->viewPath = 'pages';
        $this->Controller->action = 'display';
        $this->Controller->params['pass'] = array('home_3');

        $View = new TestPhptalView($this->Controller);
        $expected = $this->testAppPath . 'views' . DS . 'pages' . DS . 'home_3.ctp';
        $actual = $View->getViewFileName('home_3');
        $this->assertIdentical($expected, $actual);
    }

    function testRender() {
        $actual = $this->View->render('index');
        $this->assertPattern('/foo/', $actual);
        $this->assertPattern('/bar/', $actual);
        $this->assertPattern('/baz/', $actual);
        $this->assertPattern('/quz/', $actual);
        $this->assertPattern('/01-344-121-021/', $actual);
        $this->assertPattern('/05-999-165-541/', $actual);
        $this->assertPattern('/01-389-321-024/', $actual);
        $this->assertPattern('/05-321-378-654/', $actual);
    }

    function testRenderUsingHelper() {
        $actual = $this->View->render('index');
        $this->assertPattern('/' . preg_quote('<a href="http://example.com">テキストリンク1</a>', '/') . '/', $actual);
        $this->assertPattern('/' . preg_quote('<a href="http://example.com" style="font-size: 32px;">テキストリンク2</a>', '/') . '/', $actual);
    }

}
