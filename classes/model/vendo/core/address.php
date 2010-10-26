<?php

/**
 * Address model
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_Address extends AutoModeler_ORM
{
	protected $_table_name = 'addresses';

	protected $_data = array(
		'id'                   => '',
		'billing_address'      => '',
		'billing_city'         => '',
		'billing_state'        => '',
		'billing_postal_code'  => '',
		'shipping_address'     => '',
		'shipping_city'        => '',
		'shipping_state'       => '',
		'shipping_postal_code' => '',
	);

	protected $_rules = array(
		'billing_address'      => array('not_empty'),
		'billing_city'         => array('not_empty'),
		'shipping_address'     => array('not_empty'),
		'shipping_city'        => array('not_empty'),
	);
}