<?php

class Person extends Model {

	public $name = 'Person';

	public $alias = 'Person';

	public $useTable = false;

	protected $_schema = array(
		'id' => array('type' => 'integer'),
		'name' => array('type' => 'string'),
		'phone' => array('type' => 'string'),
	);

}