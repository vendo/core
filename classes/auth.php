<?php
/**
 * Extending auth class to provide visitor support
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
abstract class Auth extends Kohana_Auth
{
	static $salt = NULL;

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

	/**
	 * Overload auth's hashing with PBKDF2
	 *
	 * @return string
	 */
	public function hash($password, $salt = NULL)
	{
		if (NULL == $salt)
		{
			throw new Kohana_Exception('Please set a salt value for auth.');
		}

		$config['iterations'] = '10000';
		$config['hash_type'] = 'sha512';
		$config['hash_size'] = strlen(hash($config['hash_type'], null, true));
		$config['output_length'] = $config['hash_size'] * 1.5;

		$block_count = ceil($config['output_length'] / $config['hash_size']);
		$output = '';

		# Create key
		for ( $block = 1; $block <= $block_count; $block++ )
		{
			# Initial hash for this block
			$ib = $b = hash_hmac(
				$config['hash_type'],
				$salt.pack('N', $block),
				$password,
				true
			);

			# Perform block iterations
			for ( $i = 1; $i < $config['iterations']; $i ++ )
			{
				# XOR each iterate
				$ib ^= ($b = hash_hmac(
					$config['hash_type'], $b, $password, true
				));
			}
			$output .= $ib; # Append iterated block
		}

		# Return derived key of correct length
		return substr($output, 0, $config['output_length']);
	}

	/**
	 * Overload login() to use auth salt and hash()
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE)
	{
		if (empty($password))
			return FALSE;

		if (is_string($password))
		{
			// Create a hashed password using the salt from the stored password
			$password = $this->hash($password, Auth::$salt);
		}

		return $this->_login($username, $password, $remember);
	}
}