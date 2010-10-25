<?php
/**
 * Policy message file for basic policies
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */

return array(
	'manage_preferences' => array(
		Model_Role::LOGIN => TRUE,
		Model_Role::ADMIN => TRUE,
	)
);