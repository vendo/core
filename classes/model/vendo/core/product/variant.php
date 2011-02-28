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

	protected $_load_with = array('vendo_product' => 'parent');

	protected $_rules = array(
		'order'            => array('numeric'),
		'primary_photo_id' => array('numeric'),
	);

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
		elseif ( ! parent::__get($key) OR '0.00' == parent::__get($key))
		{
			// If we don't have a local value, try and get it from the parent
			return arr::get($this->_lazy['product'], $key);
		}

		return parent::__get($key);
	}
}