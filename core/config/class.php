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
namespace Iko;

class config extends config_loader
{
	private static $configs = array ();

	/**
	 * @param unknown $type
	 * @param unknown $args
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

	/**
	 * @param unknown $type
	 * @param unknown $args
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
			$this->config = $this->config_loader->load_config();
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::add()
	 */
	public function add ($name, $value, $comment = "")
	{
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

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::set()
	 */
	public function set ($name, $value, $comment = "")
	{
		if ($this->config[ $name ] != $value) {
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
		else {
			return TRUE;
		}
	}

	/**
	 * @param unknown $names
	 */
	public function get ($names)
	{
		if (isset($this->config[ $names ])) {
			return $this->config[ $names ];
		}
		else {
			return NULL;
		}
	}

	public function get_all ()
	{
		return $this->config;
	}
}