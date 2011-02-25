<?php
/**
 * Policy message file for basic policies
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */

set_exception_handler(array('Vendo_Exception_Handler', 'handle'));

Route::set(
	'photo',
	'<controller>/<filename>',
	array(
		'controller' => 'photo',
		'filename' => '.+',
	)
)->defaults(array('method' => 'index'));