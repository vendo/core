<?php

/**
 * Product model
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Product extends AutoModeler_ORM
{
	protected $_table_name = 'products';

	protected $_data = array(
		'id'               => '',
		'name'             => '',
		'price'            => '',
		'description'      => '',
		'order'            => '',
		'primary_photo_id' => NULL,
	);

	protected $_rules = array(
		'name'             => array('not_empty'),
		'price'            => array('not_empty'),
		'description'      => array('not_empty'),
		'order'            => array('not_empty', 'numeric'),
		'primary_photo_id' => array('numeric'),
	);

	protected $_belongs_to = array(
		'product_categories',
	);

	protected $_has_many = array(
		'photos',
	);

	/**
	 * Helper method to determin if this product has a category
	 *
	 * @return bool
	 */
	public function has_category($category_id)
	{
		return $this->has('product_categories', $category_id);
	}

	/**
	 * returns the primary photo object for this product. Can return an empty
	 * model if product has no photos
	 *
	 * @return Model_Photo
	 */
	public function primary_photo()
	{
		return new Model_Photo($this->primary_photo_id);
	}
}