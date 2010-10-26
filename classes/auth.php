<?php
/**
 * Extending auth class to provide visitor support
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
abstract class Auth extends Kohana_Auth
{
	/**
	 * Gets the currently logged in user from the session.
	 * Creates a non-saved user object no user is currently logged in.
	 *
	 * @return Model_Vendo_User
	 */
	public function get_user()
	{
		$status = $this->_session->get($this->_config['session_key'], FALSE);

		if ( ! $status)
		{
			$user = new Model_Vendo_User;
			$this->_session->set($this->_config['session_key'], $user);
			return $user;
		}

		return $status;
	}
}