<?php

/**
 * Product model
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_Product extends AutoModeler_ORM
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
		'name' => array(
			array('not_empty'),
		),
		'price' => array(
			array('not_empty'),
		),
		'description' => array(
			array('not_empty'),
		),
		'order' => array(
			array('not_empty'),
			array('numeric'),
		),
		'primary_photo_id' => array(
			array('numeric'),
		),
	);

	protected $_belongs_to = array(
		'vendo_product_categories',
	);

	protected $_has_many = array(
		'vendo_photos',
	);

	/**
	 * Helper method to determin if this product has a category
	 * 
	 * @param int $category_id the category to check
	 *
	 * @return bool
	 */
	public function has_category($category_id)
	{
		return Model::factory(
			'vendo_product_category', $category_id
		)->has('vendo_products', $this->id);
	}

	/**
	 * returns the primary photo object for this product. Can return an empty
	 * model if product has no photos
	 *
	 * @return Model_Vendo_Photo
	 */
	public function primary_photo()
	{
		return new Model_Vendo_Photo($this->primary_photo_id);
	}

	/**
	 * Returns an array of attributes with sub-arrays of their values
	 *
	 * @return array
	 */
	public function attributes()
	{
		$attributes = array();
		$key = 0;

		// Look in each attribute table for rows with this product's id
		foreach (
			Model::factory('vendo_product_attribute')->load(NULL, NULL)
			as $attribute
		)
		{
			
			foreach (
				Model::factory('vendo_product_attribute_value')->set_table_name(
					$attribute->name
				)->load(
					db::select()->where('product_id', '=', $this->id),
					FALSE
				) as $value
			)
			{
				$attributes[$key]['values'][] = array(
					'value' => $value->value,
					'price' => $value->price,
				);
			}

			if (
				isset($attributes[$key])
				AND count($attributes[$key])
			)
			{
				$attributes[$key]['name'] = $attribute->name;
			}

			$key++;
		}

		return $attributes;
	}
}