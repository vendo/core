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
}