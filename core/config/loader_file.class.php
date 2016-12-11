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
		$config_string = "";
		//Definiert den zu Eintragenden Index
		$name_temp = $name;
		//Sollte dieser ein String sein muss dieser entsprechend erweitert werden
		if (is_string($name)) {
			$name_temp = '"' . $name . '"';
		}
		$value = var_export($value, TRUE);
		if ($this->get_config_class()->config[ $name ] != $value) {
			$file_content = $this->get_file_content();
			$file_config = $file_content[0];
			$file_comments = $file_content[1];
			$search_para = 'config[' . $name_temp . '] = ';
			foreach ($file_config as $key => $item) {
				$next = FALSE;
				if (strpos($item, $search_para) !== FALSE) {
					$next = TRUE;
					$item = $search_para . $value;
				}
				$item_string = '$' . $item . ';' . PHP_EOL;
				if (isset($file_comments[ $key ]) && !$next) {
					$item_string .= $this->comment_string_generator($file_comments[ $key ]);
				}
				else if ($next && $this->comment_content($comment) != $this->comment_content($file_comments[ $key ])) {
					$item_string .= $this->comment_string_generator($comment);
				}
				else if ($next && $this->comment_content($comment) == $this->comment_content($file_comments[ $key ])) {
					$item_string .= $this->comment_string_generator($file_comments[ $key ]);
				}
				$config_string .= $item_string;
			}
			$config_string = '<?php' . PHP_EOL . $config_string;
		}

		return $this->recreate_file($config_string);
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_loader::add()
	 */
	public function add ($name, $value, $comment)
	{
		$value = var_export($value, TRUE) . ";";
		$string = '$config[';
		if (is_string($name)) {
			$string .= '"' . $name . '"';
		}
		else {
			$string .= $name;
		}
		$string .= '] = ' . $value . PHP_EOL;
		$string .= $this->comment_string_generator($comment);
		$file_content = $this->get_file_content();
		$file_config = $file_content[0];
		$file_comments = $file_content[1];
		$config_string = "";
		foreach ($file_config as $key => $item) {
			$item_string = '$' . $item . ';' . PHP_EOL;
			if (isset($file_comments[ $key ])) {
				$item_string .= $this->comment_string_generator($file_comments[ $key ]);
			}
			$config_string .= $item_string;
		}
		$config_string = '<?php' . PHP_EOL . $config_string . PHP_EOL . $string;
		var_dump($config_string);

		return $this->recreate_file($config_string);
	}

	private function comment_string_generator ($string)
	{
		$suffix = '/* -->';
		$prefix = '<-- */';
		$string = $this->comment_content($string);

		return $suffix . PHP_EOL . $string . PHP_EOL . $prefix . PHP_EOL;
	}

	private function comment_content ($string)
	{
		$search = array (
			'/* -->',
			'<-- */',
			'/* ',
			' */');
		$replace = array (
			"",
			"",
			"",
			"");
		$string = str_replace($search, $replace, $string);

		return trim($string);
	}

	private function get_file_content ()
	{
		$read = file_get_contents($this->file);
		$read = str_replace(array (
			'<?php',
			'?>'), array (
			"",
			""), $read);
		$read = explode('$', $read);
		unset($read[0]);
		$file_config = array ();
		$file_comments = array ();
		foreach ($read as $item) {
			$var = explode(';', $item);
			array_push($file_config, $var[0]);
			$key = array_search($var[0], $file_config);
			if ($key !== FALSE) {
				$file_comments[ $key ] = $var[1];
			}
		}
		print_r($file_config);
		print_r($file_comments);

		return array (
			$file_config,
			$file_comments);
	}

	private function recreate_file ($config_string)
	{
		var_dump($config_string);
		if ($config_string != "" && strpos($config_string, '<?php') !== FALSE) {
			$delete = unlink($this->file);
			if ($delete === TRUE) {
				$handle = fopen($this->file, "x");
				$write = fwrite($handle, $config_string);
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