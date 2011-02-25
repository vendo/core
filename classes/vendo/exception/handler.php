<?php
/**
 * Vendo's custom exception handler, mainly handles 404s and other status codes
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Vendo_Exception_Handler
{
	/**
	 * Handles exceptions
	 * 
	 * @param Exception $e the exception to handle
	 *
	 * @return bool
	 */
	public static function handle(Exception $e)
	{
		switch (get_class($e))
		{
			case 'Vendo_404':
			case 'ReflectionException': // This is bad
				Request::current()->status = 404;
				$view = new View_Error_404;
				$view->message = $e->getMessage();
				$view->title = 'File Not Found';
				echo $view;
				return TRUE;
				break;
			default:
				return Kohana_Exception::handler($e);
				break;
		}
	}
}