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
		// Empty array for view name chunks
		$view_name = array('View');
		
		// If current request's route is set to a directory, prepend to view name
		if ($this->request->directory())
		{
			array_push($view_name, $this->request->directory());
		}
		
		// Append controller and action name to the view name
		array_push($view_name, $this->request->controller(), $this->request->action());
		
		// Merge all parts together to get the class name
		$view_name = implode('_', $view_name);
		
		// Get the path respecting the class naming convention
		$view_path = strtolower(str_replace('_', '/', $view_name));
		
		if (Kohana::find_file('classes', $view_path))
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