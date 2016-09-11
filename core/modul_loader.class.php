<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace Iko;

abstract class modul_loader {
	private $class_modul;
	
	public function __construct($modul) {
		$this->class_modul = $modul;
		$this->pre_check_Files();
		$this->pre_check_PDO_Tables();
	}
	abstract protected function pre_check_PDO_Tables();
	abstract protected function pre_check_Files();
	public function check_PDO_Tables($tables = array()) {
		$result = true;
		if(is_string($tables)) {
			$tables = array($tables);
		}
		foreach($tables as $var) {
			$query = "SELECT 1 FROM " . $var . " WHERE 1;";
			$sql = Core::$PDO->query($query);
			echo "<br>";
			var_dump($sql);
			if($sql === false)
				throw new \Exception("Code #1234");
		}
	}
	public function check_Files($files = array()) {
		$result = true;
		if(is_string($files)) {
			$files = array($files);
		}
		if(is_array($files)) {
			foreach($files as $var) {
				$filename = $this->class_modul->get_path() . $var;
				if(!file_exists($filename)) {
					throw new \Exception("Code #1235 " . $filename);
				}
			}
		}
	}
	public function load($files = array()) {
		if(is_string($files)) {
			$files = array($files);
		}
		if(is_array($files)) {
			foreach($files as $var) {
				$filename = $this->class_modul->get_path() . $var;
				if(!file_exists($filename)) {
					throw new \Exception("Code #1236 " . $filename);
				}
				else {
					$include = @include($filename);
					if($include === false) {
						throw new \Exception("Code #1236 " . $filename);
					}
				}
			}
		}
	}
}