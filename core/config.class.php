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

/**
 * @author Marcel
 *
 */
interface config_interface
{

	/**
	 *
	 */
	function reload_config ();

	/**
	 * @param unknown $name
	 * @param unknown $value
	 * @param string  $comment
	 */
	function add ($name, $value, $comment = "");

	/**
	 * @param unknown $name
	 * @param unknown $value
	 * @param string  $comment
	 */
	function set ($name, $value, $comment = "");
}

/**
 * @author Marcel
 *
 */
abstract class config_loader implements config_interface
{
	protected $config = array ();
	protected $config_class;

	protected function __construct ($config_class)
	{
		$this->config_class = $config_class;
	}

	/**
	 *
	 */
	public function get_config ()
	{
		if (is_array($this->config)) {
			return $this->config;
		}
		else {
			return array ();
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_interface::reload_config()
	 */
	public function reload_config ()
	{
		$this->config = array ();
		$this->load_Config();
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_interface::set()
	 */
	public abstract function set ($name, $value, $comment = "");

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_interface::add()
	 */
	public abstract function add ($name, $value, $comment = "");

	protected abstract function load_Config ();

	public function get_config_class ()
	{
		return $this->config_class;
	}
}

/**
 * @author Marcel
 *
 */
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
				$this->config_loader = new config_loader_file($args);
			break;
			case 'pdo':
				$this->config_loader = new config_loader_pdo($args);
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
			$this->config = $this->config_loader->get_config();
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::add()
	 */
	public function add ($name, $value, $comment = "")
	{
		if ($this->config_loader->add($name, $value, $comment)) {
			$this->reload_config();
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
				$this->reload_config();
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

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::reload_config()
	 */
	public function reload_config ()
	{
		$this->config_loader->reload_config();
		parent::reload_config();
	}

}

/**
 * @author Marcel
 *
 */
class config_loader_file extends config_loader
{
	private $file = "";

	/**
	 * @param unknown $args
	 */
	public function __construct ($args, $config_class)
	{
		parent::__construct($config_class);
		$this->file = $args[0];
		$this->load_Config();
	}

	/**
	 * @throws \Exception
	 */
	protected function load_Config ()
	{
		$inc = @include $this->file;
		if ($inc === FALSE) {
			/* $this->FirstCreateConfig();
			$this->loadConfig(); */
			throw new \Exception("Die Datei ist nicht vorhanden. " . $this->file);
		}
		else {
			$this->config = $config;
		}
	}
	/*protected function FirstCreateConfig() {
		$delete = true;
		if(file_exists($this->datei)) {
			$delete = unlink($this->datei);
		}
		if($delete == true) {
			$main = fopen($this->datei, "r");
			$string = "";
			while(!feof($main)) {
				$string .= fread($main, 100);
			}
			fclose($main);
			$handle = fopen(modules::getDir($this->c_module) . "config.inc.php", "x");
			fwrite($handle, $string);
			fclose($handle);
		}
	}*/
	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::get_config()
	 */
	public function get_config ()
	{
		return $this->config;
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::set()
	 */
	public function set ($name, $value, $comment = "")
	{
		//Inhalt der Datei
		$string = "";
		//Definiert den zu Eintragenden Index
		$name_temp = $name;
		//Sollte dieser ein String sein muss dieser entsprechend erweitert werden
		if (is_string($name)) {
			$name_temp = '"' . $name . '"';
		}
		if (is_string($value)) {
			$value = '"' . $value . '"';
		}
		//�berpr�fung ob die Einstellung gesetzt ist?
		if (isset($this->config[ $name ])) {
			$search = $this->config[ $name ];
			if (is_string($search)) {
				$search = '"' . $search . '"';
			}
			if ($this->config[ $name ] != $value) {
				$main = fopen($this->file, "r");
				while ($read = fgets($main)) {
					if (strpos($read, '$config[' . $name_temp . ']') !== FALSE) {
						$read = str_replace($search, $value, $read);
					}
					$string .= $read;
				}
				fclose($main);
			}
		}
		if ($string != "") {
			$delete = unlink($this->file);
			if ($delete === TRUE) {
				$handle = fopen($this->file, "x");
				$write = fwrite($handle, $string);
				fclose($handle);
				if ($write !== FALSE) {
					$this->reload_config();

					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else return FALSE;
		}
		else return FALSE;
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::add()
	 */
	public function add ($name, $value, $comment = "")
	{
		$string = "";
		//Definiert den zu Eintragenden Index
		$name_temp = $name;
		//Sollte dieser ein String sein muss dieser entsprechend erweitert werden
		if (is_string($name)) {
			$name_temp = '"' . $name . '"';
		}
		if (is_string($value)) {
			$value = '"' . $value . '"';
		}
		if (!isset($this->config[ $name ])) {
			$main = fopen($this->file, "r");
			while ($read = fgets($main)) {
				if (strpos($read, '?>') !== FALSE) {
					if ($comment != "") {
						$comment = '/*
 * ' . $comment . '
 */
';
					}
					$read = $comment . '$config[' . $name_temp . '] = ' . $value . ';
' . $read;
				}
				$string .= $read;
			}
			fclose($main);
		}
		if ($string != "") {
			$delete = unlink($this->file);
			if ($delete === TRUE) {
				$handle = fopen($this->file, "x");
				$write = fwrite($handle, $string);
				fclose($handle);
				if ($write !== FALSE) {
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
			return FALSE;
		}
	}
}

class config_loader_pdo extends config_loader // TODO: Fertig machen.
{
	const table = "{prefix}configs";
	/*
	 *
	 */
	private $module = "";
	private $table = self::table;
	private $args;

	public function __construct ($args, $config_class)
	{
		parent::__construct($config_class);
		$this->args = $args;
		if (count($this->args) == 1) {
			$this->module = $this->args[0];
		}
		if (count($this->args) == 2) {
			if (strpos(strtolower($this->args[0]), "table")) {
				$this->table = $this->args[1];
			}
		}
		if (count($this->args) == 3) {
			if (strpos(strtolower($this->args[0]), "table")) {
				$this->table = $this->args[1];
				$this->module = $this->args[2];
			}
		}
		$this->load_Config();
	}

	protected function load_Config ()
	{
		$query = "SELECT * FROM " . $this->table();
		if ($this->module != "") {
			$query .= " WHERE module_name = '" . $this->module . "'";
		}
		$statement = Core::$PDO->query($query);
		$fetch = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch as $item) {
			$item["class_loader"] = $this;
			$value = new config_value($item);
			$this->config[ $item["config_name"] ] = $value;
		}
	}

	public function add ($name, $value, $comment = "")
	{
		$query = "INSERT INTO " . self::table() . " (config_name, config_value, config_comment, module_name) VALUES ('" . $name . "','" . $value . "','" . $comment . "','" . $this->module . "')";
		$statement = Core::$PDO->query($query);
		if ($statement->rowCount() == 1) {
			$this->reload_config();

			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public function set ($name, $value, $comment = "")
	{
		$query = "UPDATE " . self::table() . " Set config_value = '" . $value . "' WHERE config_name = '" . $name . "'";
		$statement = Core::$PDO->query($query);
		if ($statement->rowCount() == 1) {
			$this->reload_config();

			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	private function table ()
	{
		if ($this->table != NULL) {
			$var = "{prefix}" . $this->table;
		}
		else {
			$var = self::table;
		}

		return $var;
	}

}

/**
 * @author Marcel
 *
 */
class config_value
{
	/**
	 * @param unknown $var
	 *
	 * @return NULL|string|unknown
	 */
	public static function get_Convert ($var)
	{
		/*$conv = null;
		if (is_array($var)) {
			$conv = serialize($var);
		}
		else {
			if (is_int($var)) {
				$conv = "" . $var . "";
			}
			else {
				if (is_bool($var)) {
					if ($var) {
						$conv = "true";
					}
					else {
						$conv = "false";
					}
				}
				else {
					if (is_string($var)) {
						$conv = $var;
					}
					else {
						if (is_object($var)) {
							$conv = serialize($var);
						}
					}
				}
			}
		}*/
		$conv = serialize($var);

		return $conv;
	}

	private $value;
	private $name;
	private $comment;
	private $class_loader;
	private $module_name;
	private $class_main;

	/**
	 * @param unknown $name
	 * @param unknown $wert
	 * @param string  $comment
	 * @param unknown $config_loader
	 */
	public function __construct ($args)
	{
		foreach ($args as $key => $value) {
			$key = str_replace("config_", "", $key);
			$this->{$key} = $value;
		}

	}

	/**
	 * @param string $type
	 *
	 * @return NULL|unknown
	 */
	public function get ($type = "")
	{
		$var = NULL;
		switch ($type) {
			case 'array':
				$var = unserialize($this->value);
			break;
			case 'object':
				$var = unserialize($this->value);
			break;
			case 'int':
				$var = intval($this->value);
			break;
			case 'bool':
				if ($this->value || strtolower($this->value) == "true") {
					$var = TRUE;
				}
				else {
					if (!$this->value || strtolower($this->value) == "false") {
						$var = FALSE;
					}
				}
			break;
			case 'string':
				$var = $this->value;
			break;
			default:
				$var = unserialize($this->value);
			break;
		}

		return $var;
	}

	/**
	 *
	 * $var->equals($other);
	 * This Funtion use string converted objects and look if the two objects are similar
	 *
	 * @param unknown $value
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
		return (string)$this->string;
	}

	/**
	 * @return unknown
	 */
	public function get_name ()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_commentary ()
	{
		return $this->comment;
	}

	/**
	 * @param unknown $wert
	 *
	 * @return \Iko\NULL|\Iko\unknown
	 */
	public function __get ($wert)
	{
		return $this->get($wert);
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