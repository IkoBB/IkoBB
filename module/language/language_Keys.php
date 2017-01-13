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
use iko\Event\Handler;
use iko\user\User;


class old_language_Keys extends iko\lib\multiton\cache_string
{
	const table = languageConfigs::tableTranslation;
	const name = "translation_key";
	const default = "english";
	public static $cache = array ();
	public static $cache_exist = array ();


	private $translation_key = "";
	private $set_language = "";

	protected function __construct ($name)
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
	public function __get ($value)
	{
		return $this->get_lang($value);
	}

	public function get_lang (string $lang)
	{
		if (isset($this->{$lang})) {
			return $this->{$lang};
		}

		return "";
	}

	public function load_lang ($lang)
	{
		$this->load_language = $lang;
		$this->load_counter = 0;
	}

	private $load_language = "";
	private $load_counter = 0;

	public function __toString ()
	{
		if (isset($this->load_language) && $this->set_language != "" && $this->load_counter == 0) {
			$this->load_counter++;

			return $this->{$this->set_language};
		}
		else {
			return $this->{self::default};
		}
	}

	public function get_key ()
	{
		return $this->translation_key;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function set_key (string $name): bool
	{
		if ($name != "" && $name != $this->get_key()) {
			if (User::get_session()->has_permission("iko.language.keys.set.name")) {

			}
		}

		return FALSE;
	}
}