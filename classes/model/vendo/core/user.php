<?php

/**
 * User model
 *
 * @package    Vendo
 * @author     Jeremy Bush
 * @copyright  (c) 2010 Jeremy Bush
 * @license    http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_User extends AutoModeler_ORM
{
	protected $_table_name = 'users';

	protected $_data = array(
		'id' => '',
		'email' => '',
		'first_name' => '',
		'last_name' => '',
		'password' => '',
		'address_id' => NULL,
	);

	protected $_rules = array(
		'email' => array('not_empty', 'email'),
		'first_name' => array('not_empty'),
		'last_name' => array('not_empty'),
		'password' => array('not_empty'),
		'address_id' => array('numeric'),
	);

	protected $_callbacks = array(
		'email' => 'check_unique_email',
	);

	protected $_has_many = array(
		'vendo_roles',
	);

	protected $_shopping_cart;

	/**
	 * Sets a value to this object. Used for hashing passwords for the user
	 * 
	 * @param string $key   the key to set
	 * @param mixed  $value the value to set
	 * 
	 * @return null
	 */
	public function __set($key, $value)
	{
		if ('password' == $key AND $value)
		{
			$value = Auth::instance()->hash_password($value);
		}

		parent::__set($key, $value);
	}

	/**
	 * Constructor to load the object by an email address
	 * 
	 * @param mixed $id the id to load by. A numerical ID or an email address
	 * 
	 * @return null
	 */
	public function __construct($id = NULL)
	{
		if ( ! is_numeric($id) AND NULL != $id)
		{
			// try and get a row with this ID
			$data = db::select_array(array_keys($this->_data))
				->from($this->_table_name)
				->where('email', '=', $id)
				->execute($this->_db);

			// try and assign the data
			if (count($data) == 1 AND $data = $data->current())
			{
				foreach ($data as $key => $value)
					$this->_data[$key] = $value;
			}
		}
		else
		{
			parent::__construct($id);
		}

		$this->_shopping_cart = new Model_Order;
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

		return parent::__get($key);
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
	 * Empty complete_login() method for Auth. May contain behavior later.
	 * 
	 * @return null
	 */
	public function complete_login()
	{
		
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
}