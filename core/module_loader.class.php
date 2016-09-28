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
namespace Iko;

abstract class module_loader
{
	private $class_module;
	private $checked = false;

	public function __construct($module)
	{
		$this->class_module = $module;
	}

	public function check()
	{
		if ($this->pre_check_Files() && $this->pre_check_PDO_Tables()) {
			$this->checked = true;
		}

		return $this->is_Checked();
	}

	public function is_Checked()
	{
		return $this->checked;
	}

	abstract protected function pre_check_PDO_Tables();

	abstract protected function pre_check_Files();

	public function check_PDO_Tables($tables = array ())
	{
		$result = true;
		if (is_string($tables)) {
			$tables = array ($tables);
		}
		foreach ($tables as $var) {
			$query = "SELECT 1 FROM " . $var . " WHERE 1;";
			$sql = Core::$PDO->query($query);
			echo "<br>";
			var_dump($sql);
			if ($sql === false) {
				$result = false;
			}
		}

		return $result;
	}

	public function check_Files($files = array ())
	{
		function check_files_exist($array,$prefix) {
			$result = true;
			foreach ($array as $key => $value) {
				if(is_array($value)) {
					if(!check_files_exist($value, $prefix .$key . "/")) {
						$result = false;
					}
				}
				else {
					$filename = $prefix . $value;
					if(!file_exists($filename)) {
						$result = false;
					}
				}
			}
			return $result;
		}
		$result = true;
		if (is_string($files)) {
			$files = array ($files);
		}
		if (is_array($files)) {
			check_files_exist($files, $this->class_module->get_path());
		}

		return $result;
	}

	public function load($files = array ())
	{
		function load_file($array, $prefix) {
			foreach ($array as $key => $var) {
				if(is_array($var)) {
					load_file($var, $prefix . $key . "/");
				}
				else {
					$filename = $prefix . $var;
					if (!file_exists($filename)) {
						throw new \Exception("Code #1236 " . $filename);
					}
					else {
						$include = @include($filename);
						if ($include === FALSE) {
							throw new \Exception("Code #1236 " . $filename);
						}
					}
				}
			}
		}
		if (is_string($files)) {
			$files = array ($files);
		}
		if (is_array($files)) {
			load_file($files, $this->class_module->get_path());
			return true;
		}

		return false;
	}

	public function create_PDO_Tables($args = array (), $file = false)
	{
		$files = $args;
		if ($file) {
			$args = array ();
			$mode = "r";
			if (is_string($files)) {
				$files = array ($files);
			}
			foreach ($files as $var) {
				$filename = $this->class_module->get_path() . $var;
				if (!file_exists($filename)) {
					throw new \Exception("Code #1236 " . $filename);
				}
				$handle = fopen($filename, $mode);
				$string = "";
				while ($read = fgets($handle)) {
					$string .= $read;
				}
				fclose($handle);
				array_push($args, $string);
			}
		}
		if (is_string($args)) {
			$args = array ($args);
		}
		foreach ($args as $var) {
			$var = str_replace('`', '', $var);
			echo $var;
			if (strpos($var, "create") !== false || strpos($var, "CREATE") !== false) {
				$state = Core::$PDO->query($var);
				if ($state === false) {
					throw new \Exception(Core::$PDO->errorInfo());
				}
			}
		}
	}
}