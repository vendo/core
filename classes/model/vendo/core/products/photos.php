<?php

/**
 * Join table model for metadata
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_Products_Photos extends AutoModeler_ORM
{
	protected $_table_name = 'products_photos';

	protected $_data = array(
		'id'         => NULL,
		'product_id' => NULL,
		'photo_id'   => NULL,
		'order'      => NULL,
	);

	protected $_rules = array(
		'product_id' => array('not_empty', 'numeric'),
		'photo_id'   => array('not_empty', 'numeric'),
		'order'      => array('not_empty', 'numeric'),
	);
}