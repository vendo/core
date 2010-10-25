<?php

set_exception_handler(array('Vendo_Exception_Handler', 'handle'));

Route::set(
	'photo',
	'<controller>/<filename>',
	array(
		'controller' => 'photo',
		'filename' => '.+',
	)
)->defaults(array('method' => 'index'));