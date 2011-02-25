<?php
/**
 * Product category model
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_Product_Category extends AutoModeler_ORM
{
	protected $_table_name = 'product_categories';

	protected $_data = array(
		'id' => '',
		'name' => '',
		'parent_id' => NULL,
		'order' => '',
	);

	protected $_rules = array(
		'name' => array('not_empty'),
		'parent_id' => array('numeric'),
		'order' => array('not_empty', 'numeric'),
	);

	protected $_has_many = array(
		'vendo_products',
		'vendo_product_variants'
	);

	/**
	 * Overriding __get() to allow for self relations with parent_id
	 * 
	 * @param mixed $key the key to return
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		if ('parent_id' == $key)
		{
			return db::select(array_keys($this->_data))->from(
				$this->_table_name
			)->where('id', '=', $this->_data['parent_id'])->as_object(
				'Model_Category'
			)->execute($this->_db)->current();
		}

		return parent::__get($key);
	}

	/**
	 * Helper method to determine if this category is a subcategory of
	 * another category
	 * 
	 * @param int $category_id the parent category to check
	 *
	 * @return bool
	 */
	public function has_category($category_id)
	{
		$product_categories = Model::factory(
			'vendo_product_category'
		)->load(
			db::select()->where('parent_id', '=', $category_id), NULL
		);
		foreach ($product_categories as $category)
		{
			if ($category->id == $this->id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Returns a full tree of nested product categories starting at a category
	 * 
	 * @param int    $start       the category to start at
	 * @param bool   $remove_this should this category be included?
	 * @param object $product     an optional product to compare checked against
	 *
	 * @return array
	 */
	public function full_tree(
		$start = NULL, $remove_this = FALSE, $product = NULL
	)
	{
		$tree = array();
		$compare_object = NULL == $product ? $this : $product;

		$product_categories = Model::factory('vendo_product_category')->load(
			db::select()->where('parent_id', '=', $start), NULL
		);
		foreach ($product_categories as $category)
		{
			$sub_tree = $this->full_tree(
				$category->id, $remove_this, $product
			);

			if ( ! $remove_this OR ! $this->id // Root always gets shown
				OR ($remove_this AND $this->id != $category->id)
			)
			{
				$tree[] = array(
					'id' => $category->id,
					'name' => $category->name,
					'has_category' => $compare_object->has_category(
						$category->id
					),
					'has_children' => (bool) count($sub_tree),
					'children' => $sub_tree,
				);
			}
		}

		return $tree;
	}
}