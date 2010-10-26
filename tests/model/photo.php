<?php
/**
 * Tests the photo model class	
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 *
 * @group Vendo_Core
 */
class Model_Vendo_Photo_Test extends Vendo_TestCase
{
	/**
	 * Tests that we can process and read and delete a photo
	 * 
	 * @return null
	 */
	public function test_do_it()
	{
		$photo = new Model_Vendo_Photo;
		$photo->file = DOCROOT.'media/images/grid.png';
		$photo->save();

		$path = $photo->path().$photo->filename;

		$this->assertFalse($photo->id === NULL);
		$this->assertTrue(file_exists($path));

		$photo->delete();
		$this->assertFalse(file_exists($path));
	}
}