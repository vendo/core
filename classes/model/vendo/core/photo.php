<?php

/**
 * Photo model
 *
 * @package   Vendo
 * @author    Jeremy Bush <contractfrombelow@gmail.com>
 * @copyright (c) 2010-2011 Jeremy Bush
 * @license   ISC License http://github.com/zombor/Vendo/raw/master/LICENSE
 */
class Model_Vendo_Core_Photo extends AutoModeler_ORM
{
	protected $_table_name = 'photos';

	protected $_data = array(
		'id' => NULL,
		'filename' => NULL,
	);

	protected $_rules = array(
		'filename' => array(
			array('not_empty'),
		),
	);

	protected $_belongs_to = array(
		'vendo_products'
	);

	/* Holding data for a new photo file. Set this to replace the image assoc
	 * with this model. Should be an absolute path
	 * 
	 */ 
	public $file = NULL;

	/**
	 * Overload __construct to load by a filename
	 * 
	 * @param int $id the id to load
	 *
	 * @return null
	 */
	public function __construct($id = NULL)
	{
		if ( ! ctype_digit($id))
		{
			parent::__construct();

			$id = basename($id);

			// try and get a row with this ID
			$data = db::select('*')->from($this->_table_name)->where(
				'filename', '=', $id
			)->execute($this->_db);

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
	}

	/**
	 * Overload save() to process a photo stored in $file
	 * 
	 * @param Validate $validation the validation object to process with
	 *
	 * @return int
	 */
	public function save($validation = NULL)
	{
		// Process the photo if there is one
		if ($this->file)
		{
			$path_info = pathinfo($this->file);
			$ext = $path_info['extension'];

			// Make a unique name for this
			$this->filename = uniqid(NULL, TRUE).'.'.$ext;

			// Shard the file on the filesystem
			$shards = array();
			for ($i = 0; $i<=4; $i+=2)
			{
				$shards[] = substr($this->filename, $i, 2);
			}
			$path = APPPATH.'photos/'.implode('/', $shards).'/';

			if ( ! is_dir($path))
			{
				mkdir($path, 0755, TRUE);
			}

			if (is_uploaded_file($this->file))
			{
				move_uploaded_file($this->file, $path.$this->filename);
			}
			else
			{
				copy($this->file, $path.$this->filename);
			}
		}

		try
		{
			return parent::save($validation);
		}
		catch (AutoModeler_Exception $e)
		{
			// Delete the photo we uploaded, since something went wrong
			if ($this->file)
			{
				unlink($path.$this->filename);
				// TODO: delete the straggler directory
			}

			throw $e;
		}
	}

	/**
	 * Overload delete method to delete file
	 *
	 * @return int
	 */
	public function delete()
	{
		if ($foo = parent::delete())
		{
			unlink($this->path().$this->filename);
			// Todo: delete straggler directory

			return $foo;
		}
	}

	/**
	 * Gets the full pathname to this photo, based on the shards
	 *
	 * @return string
	 */
	public function path()
	{
		$shards = array();
		for ($i = 0; $i<=4; $i+=2)
		{
			$shards[] = substr($this->filename, $i, 2);
		}
		$path = APPPATH.'photos/'.implode('/', $shards).'/';
		return $path;
	}

	/**
	 * Gets the full uri to this image for public display
	 *
	 * @return string
	 */
	public function uri()
	{
		// If this isn't loaded, return no photo image
		if ( ! $this->id)
		{
			return url::site('photo/no-photo.png');
		}

		$shards = array();
		for ($i = 0; $i<=4; $i+=2)
		{
			$shards[] = substr($this->filename, $i, 2);
		}
		$path = url::site('photo/'.implode('/', $shards).'/'.$this->filename);
		return $path;
	}
}