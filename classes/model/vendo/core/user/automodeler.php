<?php

/**
 * User model
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_User_AutoModeler
	extends Model_User implements Model_ACL_User
{
	protected $_shopping_cart;

	/**
	 * Overload constructor to set shopping cart
	 * 
	 * @param int $id the id to load
	 *
	 * @return null
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		$this->_shopping_cart = new Model_Order;

		$this->_has_many = array_merge($this->_has_many, array('vendo_roles'));

		$this->_callbacks['email'] = 'check_unique_email';
	}

	/**
	 * Overload __get to return empty address objects
	 * 
	 * @param mixed $key the key to get
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		if ($key == 'address' AND ! $this->_data['address_id'])
		{
			return new Model_Vendo_Address;
		}
		else if ($key == 'address')
		{
			return new Model_Vendo_Address($this->address_id);
		}

		return parent::__get($key);
	}

	/**
	 * Overload has() to translate role to vendo_role for auth
	 * 
	 * @param string $key   they key to compare
	 * @param mixed  $value the value to compare
	 *
	 * @return bool
	 */
	public function has($key, $value)
	{
		if ('roles' == $key)
		{
			$key = 'vendo_roles';
		}

		return parent::has($key, $value);
	}

	/**
	 * Sets and/or returns the user's shopping cart
	 * 
	 * @param Model_Order $cart an optional cart object to set to this user
	 *
	 * @return Model_Order
	 */
	public function cart(Model_Order $cart = NULL)
	{
		if ($cart)
		{
			$this->_shopping_cart = $cart;
		}

		return $this->_shopping_cart;
	}

	/**
	 * Validation callback to check that this user has a unique email
	 * 
	 * @param Validate $array the validate object to use
	 * @param string   $field the field name to check
	 * 
	 * @return null
	 */
	public function check_unique_email(Validate $array, $field)
	{
		$user = new Model_Vendo_User($array[$field]);

		// Only error if this is a new or different object
		if ($user->id AND $user->id != $this->id)
		{
			$array->error($field, 'not_unique');
		}
	}

	/**
	 * Wrapper method to execute ACL policies. Only returns a boolean, if you
	 * need a specific error code, look at Policy::$last_code
	 * 
	 * @param string $policy_name the policy to run
	 * @param array  $args        arguments to pass to the rule
	 *
	 * @return boolean
	 */
	public function can($policy_name, $args = array())
	{
		$status = FALSE;

		try
		{
			$refl = new ReflectionClass('Policy_' . $policy_name);
			$class = $refl->newInstanceArgs();
			$status = $class->execute($this, $args);

			if (TRUE === $status)
				return TRUE;
		}
		catch (ReflectionException $ex) // try and find a message based policy
		{
			// Try each of this user's roles to match a policy
			foreach ($this->find_related('roles') as $role)
			{
				$status = Kohana::message('policy', $policy_name.'.'.$role->id);
				if ($status)
					return TRUE;
			}
		}

		// We don't know what kind of specific error this was
		if (FALSE === $status)
		{
			$status = Policy::GENERAL_FAILURE;
		}

		Policy::$last_code = $status;

		return TRUE === $status;
	}

	/**
	 * Wrapper method for self::can() but throws an exception instead of bool
	 * 
	 * @param string $policy_name the policy to run
	 * @param array  $args        arguments to pass to the rule
	 * 
	 * @throws Policy_Exception
	 *
	 * @return null
	 */
	public function assert($policy_name, $args = array())
	{
		$status = $this->can($policy_name, $args);

		if (TRUE !== $status)
		{
			throw new Policy_Exception(
				'Could not authorize policy :policy',
				array(':policy' => $policy_name),
				Policy::$last_code
			);
		}
	}

	/**
	 * Returns standard password validation for this object for use in the
	 * controller
	 * 
	 * @param array $user_post an array of user parameters
	 *
	 * @return Validate
	 */
	public static function get_password_validation($user_post)
	{
		return Validation::factory(
			array(
				'password' => arr::get($user_post, 'password'),
				'repeat_password' => arr::get($user_post, 'repeat_password'),
			)
		)	->rule('repeat_password', 'not_empty')
			->rule('password', 'matches', array(':validation', 'password', 'repeat_password'));
	}
}