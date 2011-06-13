<?php

/**
 * Product attribute model. Keeps track of related attribute value tables.
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */

class Model_Vendo_Core_Product_Attribute extends AutoModeler_ORM
{
	protected $_table_name = 'product_attributes';

	protected $_data = array(
		'id'   => NULL,
		'name' => '',
	);

	protected $_rules = array(
		'name' => array(
			array('not_empty'),
		),
	);

	/**
	 * Overload save to create tables when needed
	 *
	 * @return bool
	 */
	public function save($validation = NULL)
	{
		$create_table = ! $this->loaded();

		$result = parent::save($validation);

		// Make the related table
		if ($create_table AND $result)
		{
			$this->_db->query(
				NULL,
				'CREATE TABLE `product_attribute_values_'.
					url::title($this->name).
				'` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`value` varchar(100) NOT NULL,
				`product_attribute_id` bigint(20) unsigned NOT NULL,
				`product_id` bigint(20) unsigned NOT NULL,
				`price` decimal(10,2) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'
			);
		}

		return $result;
	}

	/**
	 * Overload delete to delete the related table
	 *
	 * @return bool
	 */
	public function delete()
	{
		parent::delete();

		$this->_db->query(
			NULL,
			'DROP TABLE `product_attribute_values_'.url::title($this->name).'`'
		);
	}
}