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

class config_loader_file extends config_loader // TODO: Überarbeiten der Klasse auf Stand von loader_pdo
{
	private $file = "";

	/**
	 * config_loader_file constructor.
	 *
	 * @param $args
	 * @param $config_class
	 */
	public function __construct ($args, $config_class)
	{
		parent::__construct($config_class);
		$this->file = $args[0];
	}

	/**
	 * @throws \Exception
	 */
	protected function load_Config ()
	{
		$config = array ();
		$inc = @include $this->file;
		if ($inc === FALSE) {
			/* $this->FirstCreateConfig();
			$this->loadConfig(); */
			throw new \Exception("Die Datei ist nicht vorhanden. " . $this->file);
		}
		else {
			return $config;
		}
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
	public function add ($name, $value, $comment)
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