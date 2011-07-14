<?php

/**
 * PHPTALView Test file
 *
 * CakePHP 1.3+
 * PHP 5.2+
 *
 * Copyright 2011, nojimage (http://php-tips.com/)
 * 
 * @filesource
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2011 nojimage (http://php-tips.com/)
 * @license    http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
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
 * @property PhptalViewPeopleController $PeopleController
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
        $this->testAppPath = dirname(dirname(dirname(__FILE__))) . DS . 'test_app' . DS;
        App::build(array(
            'controllers' => array($this->testAppPath . 'controllers' . DS),
            'models' => array($this->testAppPath . 'models' . DS),
            'plugins' => array($this->testAppPath . 'plugins' . DS),
            'views' => array($this->testAppPath . 'views' . DS),), true);
        App::import('Controller', 'People');
    }

    /**
     * tearDown method
     *
     * @access public
     * @return void
     */
    function tearDown() {
        
    }

    /**
     * endTest
     *
     * @access public
     * @return void
     */
    function startTest($method) {
        $this->Controller = new Controller();
        $this->PeopleController = new PeopleController();
        $this->PeopleController->viewPath = 'people';
        $this->PeopleController->action = 'index';
        $this->PeopleController->params['action'] = 'index';
        $this->PeopleController->index();
        $this->View = new TestPhptalView($this->PeopleController);
        $this->View->Phptal->setForceReparse(true);
    }

    /**
     * endTest
     *
     * @access public
     * @return void
     */
    function endTest() {
        App::build();
        unset($this->View);
        unset($this->PeopleController);
        unset($this->Controller);
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

    function testHelerModifier() {
        $actual = $this->View->render('helper');
        $this->assertPattern('/' . preg_quote('<img src="img/dummy01.gif" alt="" />', '/') . '/', $actual);
        $this->assertPattern('/' . preg_quote('<form id="PersonIndexForm" method="post" action="/people" accept-charset="utf-8">', '/') . '/', $actual);
        $this->assertPattern('/' . preg_quote('<input name="data[Person][name]" type="text" id="PersonName" />', '/') . '/', $actual);
    }

    function testUrlModifier() {
        Router::setRequestInfo(array(array('controller' => 'people', 'action' => 'index'), array('base' => '/')));
        $this->View->set('posts', array(
            array('Post' => array('id' => 1, 'title' => 'Post Title 1')),
            array('Post' => array('id' => 2, 'title' => 'Post Title 2')),
            array('Post' => array('id' => 3, 'title' => 'Post Title 3')),
        ));
        $actual = $this->View->render('url');
        $this->assertPattern('!' . preg_quote('<a href="/posts/view/id:1">リンクA</a>', '!') . '!', $actual);
        $this->assertPattern('!' . preg_quote('<a href="/people/view/2">リンクB</a>', '!') . '!', $actual);
        $this->assertPattern('!<a href="http://(?:.+?)' . preg_quote('/posts/view/id:1">リンクC</a>', '!') . '!', $actual);
        $this->assertPattern('!' . preg_quote('<a href="/posts/view/id:1">Post Title 1</a>', '!') . '!', $actual);
        $this->assertPattern('!' . preg_quote('<a href="/posts/view/id:2">Post Title 2</a>', '!') . '!', $actual);
        $this->assertPattern('!' . preg_quote('<a href="/posts/view/id:3">Post Title 3</a>', '!') . '!', $actual);
    }

    function testCakeNamespace() {
        $actual = $this->View->render('namespace');
        $this->assertPattern('/' . preg_quote('<img src="img/dummy01.gif" alt="" />', '/') . '/', $actual);
        $this->assertPattern('/' . preg_quote('<form id="PersonIndexForm" method="post" action="/people" accept-charset="utf-8">', '/') . '/', $actual);
        $this->assertPattern('/' . preg_quote('<input name="data[Person][name]" type="text" id="PersonName" />', '/') . '/', $actual);
    }

}
