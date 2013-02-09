<?php

/**
 * PeopleController class
 *
 * @package       taltal
 * @subpackage    taltal.tests.test_app.controllers
 */
class PeopleController extends Controller {

	public $name = 'People';

	public $uses = array('Person');

/**
 * index method
 *
 * @access public
 * @return void
 */
	public function index() {
		$datas = array(
			array('Person' => array('name' => 'foo', 'phone' => '01-344-121-021')),
			array('Person' => array('name' => 'bar', 'phone' => '05-999-165-541')),
			array('Person' => array('name' => 'baz', 'phone' => '01-389-321-024')),
			array('Person' => array('name' => 'quz', 'phone' => '05-321-378-654')),
		);
		$this->set(compact('datas'));
	}

}
