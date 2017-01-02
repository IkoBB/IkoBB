<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.12.2016
 * Time: 14:55
 */

namespace iko\language;

use iko\Core;
use iko;


class language_Keys extends iko\lib\multiton\cache_string
{
	const table = languageConfigs::tableTranslation;
	const name = "translation_key";

	public static $cache = array ();
	public static $cache_exist = array ();



	private $translation_key = "";
	private $default_language = "english";
	private $set_language = "";

	public function __construct ($name)
	{
		if (is_string($name) && self::exist($name)) {
			$sql = "SELECT * FROM " . self::table . " WHERE " . self::name . " = '" . $name . "'";
			$statement = Core::$PDO->query($sql);
			$fetch = $statement->fetch(iko\PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	/* Aufruf = Tatsächlicher aufruf
	 * $class->variable = $class->__get("variable");
	 * $class->german = $class->__get("german");
	 * $class->english = $class->__get("english");
	 * $class->unk = $class->__get("unk");
	 * $class->get("english") oder $class->english = $class->__get("english");
	 */
	public function __get ($value)
	{
		// TODO: Implement __toString() method.
		if (isset($this->{$value})) {
			return $this->{$value};
		}
		else {
			return "";
		}

	}

	public function set_lang ($lang)
	{
		$this->set_language = $lang;
		$this->set_counter = 0;
	}

	private $set_counter = 0;

	public function __toString ()
	{
		if (isset($this->set_language) && $this->set_language != "" && $this->set_counter == 0) {
			$this->set_counter++;

			return $this->{$this->set_language};
		}
		else {
			return $this->{$this->default_language};
		}
	}

	public function get_key ()
	{
		return $this->translation_key;
	}
	#public function
}