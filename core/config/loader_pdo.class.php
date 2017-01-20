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

class config_loader_pdo extends config_loader
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
	}

	protected function load_Config ()
	{
		$config = array ();
		$query = "SELECT * FROM " . $this->table();
		if ($this->module != "") {
			$query .= " WHERE module_name = '" . $this->module . "'";
		}
		$statement = Core::PDO()->query($query);
		$fetch = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch as $item) {
			try {
				$config[ $item["config_name"] ] = unserialize($item["config_value"]);
			}
			catch (\Exception $ex) {
				$config[ $item["config_name"] ] = $item["config_value"];
			}
		}
		return $config;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 * @param string $comment
	 *
	 * @return bool
	 */
	public function add ($name, $value, $comment)
	{
		if (!isset($this->get_config_class()->{$name})) {
			if ($comment != "") {
				$value = serialize($value);
				$query = "INSERT INTO " . self::table() . " (config_name, config_value, config_comment, module_name) VALUES ('" . $name . "','" . $value . "','" . $comment . "','" . $this->module . "')";
				$statement = Core::PDO()->exec($query);
				if ($statement == 1) {
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
			return $this->config_class->set($name, $value, $comment);
		}
	}

	public function set ($name, $value, $comment = "")
	{
		$query = "UPDATE " . self::table() . " Set config_value = '" . serialize($value) . "'";
		if ($comment != "") {
			$query .= ", config_comment = '" . $comment . "'";
		}
		$query .= " WHERE config_name = '" . $name . "'";
		$statement = Core::PDO()->query($query);
		if ($statement->rowCount() == 1) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	private function table ()
	{
		$var = $this->table;
		if (strpos($var, "{!prefix}") === FALSE) {
			if (strpos($var, "{prefix}") === FALSE) {
				$var = "{prefix}" . $var;
			}
		}
		else {
			$var = str_replace("{!prefix}", '', $var);
		}

		return $var;
	}

}
