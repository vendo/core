<?php
/**
 * Override kohana security class for better csrf token handling
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Security extends Kohana_Security
{
	/**
	 * Generate and store a unique token which can be used to help prevent
	 * [CSRF](http://wikipedia.org/wiki/Cross_Site_Request_Forgery) attacks.
	 *
	 *     $token = Security::token();
	 *
	 * And then check it when using [Validate]:
	 *
	 *     $array->rules('csrf', array(
	 *         'not_empty'       => NULL,
	 *         'Security::check' => NULL,
	 *     ));
	 *
	 * This provides a basic, but effective, method of preventing CSRF attacks.
	 *
	 * @param   boolean  force a new token to be generated?
	 * @return  string
	 * @uses    Session::instance
	 */
	public static function token($name = FALSE, $key = NULL)
	{
		return Form::hidden('token', self::raw_token($name, $key));
	}

	/**
	 * Generates a unique token and stores it in the session
	 *
	 * @return string
	 */
	public static function raw_token($name, $key)
	{
		$token = uniqid('security');
		$csrf = Session::instance()->get('csrf', array());
		$csrf[$name.'_'.$key] = $token;
		Session::instance()->set('csrf', $csrf);
		return $token;
	}

	/**
	 * Determines if a valid CSRF token exists in the session. Deletes the
	 * session key if it exists, and returns a boolean.
	 *
	 * @param string $name   the name of the form
	 * @param string $key    the value of the form key to inspect
	 * @param bool   $delete Delete the session token
	 *
	 * @return boolean
	 */
	public static function check($name, $key = NULL, $delete = true)
	{
		$token = Arr::get($_POST, 'token', false);
		return self::validate($token, $name, $key, $delete);
	}

	/**
	 * Determines if a valid CSRF token exists in the session. Deletes the
	 * session key if $delete, and returns a boolean.
	 *
	 * @param string $value  the posted token value
	 * @param string $name   the name of the form
	 * @param string $key    the value of the form key to inspect
	 * @param bool   $delete Delete the session token
	 *
	 * @return boolean
	 *
	 * This method is easily hooked into a Kohana Validator:
	 * @example
	 *          $token_params = array('location', $location->location_id);
	 *          $validator = Validate::factory($args)
	 *          ->rule('token', 'Csrf::validate', $token_params);
	 */
	public static function validate($value, $name, $key, $delete = true)
	{
		$csrf = Session::instance()->get('csrf', array());
		$csrf = (array)$csrf;
		$exists = (Arr::get($csrf, $name.'_'.$key) === $value);

		if ($delete)
		{
			Session::instance()->set('csrf', null);
		}

		return $exists;
	}
}