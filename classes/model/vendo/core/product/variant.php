<?php

/**
 * Product variant model
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */

class Model_Vendo_Core_Product_Variant extends Model_Vendo_Product
{
	protected $_table_name = 'product_variants';

	/**
	 * overload the constructor to set model specific fields
	 *
	 * @return null
	 */
	public function __construct($id = NULL)
	{
		$this->_data+=array(
			'parent_id' => ''
		);

		$this->_rules+=array(
			'parent_id' => array('not_empty', 'numeric')
		);

		parent::__construct($id);
	}

	/**
	 * Overriding __get() to allow for product relations with parent_id
	 * 
	 * @param mixed $key the key to return
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		if ('parent_id' == $key)
		{
			return new Model_Vendo_Product($this->$key);
		}

		return parent::__get($key);
	}
}