<?php
/**
 * Policy message file for basic policies
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */

return array(
	'manage_preferences' => array(
		Model_Vendo_Role::LOGIN => TRUE,
		Model_Vendo_Role::ADMIN => TRUE,
	)
);