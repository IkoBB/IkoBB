<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * @author Marcel
 *
 */
namespace iko;

class config_value
{
	/**
	 * @param mixed $var
	 *
	 * @return string
	 */
	public static function get_Convert ($var)
	{
		return serialize($var);
	}

	private $value;
	private $name;
	private $comment;
	private $class_loader;
	private $module_name;
	private $class_main;


	/**
	 * config_value constructor.
	 *
	 * @param $args
	 */
	public function __construct ($args)
	{
		foreach ($args as $key => $value) {
			$key = str_replace("config_", "", $key);
			$this->{$key} = $value;
		}
	}

	/**
	 * @return mixed
	 */
	public function get ()
	{
		return unserialize($this->value);
	}

	/**
	 *
	 * $var->equals($other);
	 * This Funtion use string converted objects and look if the two objects are similar
	 *
	 * @param mixed $value
	 *
	 * @return boolean
	 */
	public function equals ($value)
	{
		if (self::get_Convert($value) === $this->get()) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public function __toString ()
	{
		return (string)$this->get();
	}

	/**
	 * @return string
	 */
	public function get_name ()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_comment ()
	{
		return $this->comment;
	}


	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __get ($value)
	{
		return $this->get();
	}

	public function get_module_name ()
	{
		return $this->module_name;
	}

	public function get_module ()
	{
		return module::get($this->get_module_name());
	}

	private function get_config_loader ()
	{
		return $this->class_loader;
	}

	public function get_config_class ()
	{
		if (!$this->class_main instanceof config && $this->get_config_loader() != NULL) {
			$this->class_main = $this->get_config_loader()->get_config_class();
		}

		return $this->class_main;
	}

	public function set ($value, $comment = "")
	{
		$this->get_config_class()->set($this->get_name(), $value, $comment);
	}

	public function add ()
	{
		if ($this->get_config_class() instanceof config) {
			$this->get_config_class()->add($this->get_name(), $this->value, $this->comment);
		}
	}
}