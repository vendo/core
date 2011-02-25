<?php
/**
 * Extended controller class to render profiler
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Controller extends Kohana_Controller
{
	public function before()
	{
		parent::before();
		$directory = Request::current()->directory() ? Request::current()->directory().'_' : '';
		$view_name = 'View_'.$directory.Request::current()->controller().'_'.Request::current()->action();
		if(Kohana::find_file('classes', strtolower(str_replace('_', '/', $view_name))))
		{
			$this->view = new $view_name;
		}
	}

	public function after()
	{
		if (isset($this->view))
			$this->response->body(
				$this->view->render().View::factory('profiler/stats')->render()
			);
		else
			$this->response->body(
				'<h1>No template found for View_'.
				Request::current()->controller().'_'.
				Request::current()->action().'!</h1>'
			);
	}
}