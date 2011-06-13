<?php

/**
 * Product attribute value model, tracks the values for product variants
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */

class Model_Vendo_Core_Product_Attribute_Value extends AutoModeler_ORM
{
	protected $_table_name = 'product_attribute_values_';

	protected $_data = array(
		'id'                   => NULL,
		'value'                => '',
		'product_attribute_id' => '',
		'product_id'           => '',
		'price'                => 0,
	);

	protected $_rules = array(
		'value' => array(
			array('not_empty'),
		),
		'product_id' => array(
			array('numeric'),
			array('not_empty'),
		),
	);

	/**
	 * Sets the attribute table name for this model
	 *
	 * @return $this
	 */
	public function set_table_name($value)
	{
		$this->_table_name = 'product_attribute_values_'.url::title($value);

		return $this;
	}
}