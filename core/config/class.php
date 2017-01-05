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

class config extends config_loader implements \Iterator
{
	private static $configs = array ();

	/**
	 * @param string $type
	 * @param mixed  $args
	 *
	 * @return NULL|mixed
	 */
	public static function load ($type, $args)
	{
		if (!is_array($args)) {
			$args = array ($args);
		}
		$class = NULL;
		$var = FALSE;
		for ($i = 0; $i < count(self::$configs); $i++) {
			if (self::$configs[ $i ]["type"] == $type && self::$configs[ $i ]["args"] == $args) {
				$var = $i;
				break;
			}
		}
		if ($var !== FALSE) {
			if (!isset(self::$configs[ $var ]["class"]) || self::$configs[ $var ]["class"] == NULL) {
				self::$configs[ $var ]["class"] = new config(self::$configs[ $var ]["type"],
					self::$configs[ $var ]["args"]);
			}
			$class = self::$configs[ $var ]["class"];
		}
		else {
			$array = array (
				"type"  => $type,
				"args"  => $args,
				"class" => new config($type, $args));
			array_push(self::$configs, $array);
			$class = self::$configs[ (count(self::$configs) - 1) ]["class"];
		}

		return $class;
	}

	private $config_loader = NULL;
	private $create_args = NULL;
	private $config = array ();

	/** @noinspection PhpMissingParentConstructorInspection */

	/**
	 * @param string $type
	 * @param mixed  $args
	 *
	 * @throws \Exception
	 */
	public function __construct ($type, $args)
	{
		if (!is_array($args)) {
			$args = array ($args);
		}
		switch (strtolower($type)) {
			case 'file':
				$this->config_loader = new config_loader_file($args, $this);
			break;
			case 'pdo':
				$this->config_loader = new config_loader_pdo($args, $this);
			break;
			case 'create':
				$this->config_loader = NULL;
				$this->create_args = $args;
			break;
			default:
				throw new \Exception("Config kann nur mit als File, PDO, Create initalisiert werden.");
			break;
		}
		$this->load_Config();
	}

	/**
	 *
	 */
	protected function load_Config ()
	{
		if ($this->config_loader != NULL) {
			$this->config = array ();
			/** @noinspection PhpUndefinedMethodInspection */
			$this->config = $this->config_loader->load_config();
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::add()
	 */
	public function add ($name, $value, $comment = "")
	{
		/** @noinspection PhpUndefinedMethodInspection */
		if (!isset($this->config[ $name ])) {
			if ($this->config_loader->add($name, $value, $comment)) {
				$this->load_Config();
				if (isset($this->config[ $name ])) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			return $this->set($name, $value, $comment);
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::set()
	 */
	public function set ($name, $value, $comment = "")
	{
		if (isset($this->config[ $name ]) && $this->config[ $name ] != $value) {
			/** @noinspection PhpUndefinedMethodInspection */
			if ($this->config_loader->set($name, $value, $comment)) {
				$this->load_Config();
				if (isset($this->config[ $name ]) && $this->config[ $name ] == $value) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		else if (isset($this->config[ $name ]) && $this->config[ $name ] == $value) {
			return TRUE;
		}
		else {
			return $this->add($name, $value, $comment);
		}
	}

	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function get ($name)
	{
		/** @noinspection PhpIllegalArrayKeyTypeInspection */
		/** @noinspection PhpIllegalArrayKeyTypeInspection */
		return (isset($this->config[ $name ])) ? $this->config[ $name ] : NULL;
	}

	/**
	 * @return mixed
	 */
	public function get_all ()
	{
		return $this->config;
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get ($name)
	{
		return $this->get($name);
	}

	/**
	 * @param $name
	 * @param $value
	 * If change the commentary, please use a Array with following style:
	 * array(VALUE, COMMENT);
	 */
	public function __set ($name, $value)
	{
		if (is_array($value)) {
			$this->set($name, $value[0], $value[1]);
		}
		else {
			$this->set($name, $value);
		}
	}

	public function __isset ($name)
	{
		if (isset($this->config[ $name ])) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/*
	 * Iteratoren Functions
	 */

	public function current ()
	{
		return current($this->config);
	}

	public function key ()
	{
		return key($this->config);
	}

	public function next ()
	{
		return next($this->config);
	}

	public function rewind ()
	{
		return reset($this->config);
	}

	public function valid ()
	{
		return key($this->config) !== NULL;
	}
}