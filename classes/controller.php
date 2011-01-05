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
	/**
	 * Overloaded after method to render profiler
	 *
	 * @return null
	 */
	public function after()
	{
		$this->request->response = $this->request->response->render().
			View::factory('profiler/stats')->render();
	}
}