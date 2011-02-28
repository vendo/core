<?php
/**
 * Tests the photo model class	
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 *
 * @group Vendo_Core
 */
class Model_Vendo_Product_Test extends Vendo_TestCase
{
	/**
	 * Tests that a product variant will read data from it's parent
	 *
	 * @return null
	 */
	public function test_variant_will_read_parent()
	{
		$product = new Model_Vendo_Product;
		$product->set_fields(
			array(
				'name' => 'unit test',
				'price' => '9.99',
				'description' => 'foo',
				'order' => 1,
			)
		);
		$product->save();

		$product_variant = new Model_Vendo_Product_Variant;
		$product_variant->parent_id = $product->id;
		$product_variant->save();
		$product_variant_id = $product_variant->id;

		$product_variant = new Model_Vendo_Product_Variant($product_variant_id);
		$this->assertEquals($product_variant->price, $product->price);
		$this->assertEquals($product_variant->name, $product->name);
		$this->assertEquals($product_variant->description, $product->description);
		$this->assertEquals($product_variant->order, $product->order);

		$product->delete();
		$product_variant->delete();
	}
}