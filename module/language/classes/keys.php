<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <User>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 11.01.2017
 * Time: 23:30
 */
namespace iko\language;

use iko\Core;
use iko\Event\Handler;
use iko\lib\multiton\cache_string;
use iko\user\User;
use PDO;

class key extends cache_string implements iKey
{
	const table = language::table;
	const name = language::name;
	protected static $cache = array ();
	protected static $cache_exist = array ();

	private $key;
	private $langs = array ();
	private $all = FALSE;

	protected function __construct (string $name)
	{
		if (self::exist($name)) {
			$this->key = $name;
		}
	}

	public function get_key ()
	{
		return $this->key;
	}

	public function get_lang (string $lang = "")
	{
		if ($lang == "") {
			$lang = language::get_instance()->get_current();
		}
		if ($lang != "") {
			if (!isset($this->langs[ $lang ])) {
				$this->load_lang($lang);
			}

			return $this->langs[ $lang ] ?? "";
		}
	}

	public function get_lang_all (): array
	{
		if (!$this->all) {
			$this->all = TRUE;
			$languages = language::get_instance()->get_languages();
			foreach ($languages as $item) {
				if (!isset($this->langs[ $item ])) {
					$this->all = FALSE;
					break;
				}
			}
			if (!$this->all) {
				$this->load_lang_all();
			}
		}
		if ($this->all) {
			return $this->langs;
		}
	}

	private function load_lang_all ()
	{
		$sql = "SELECT * FROM " . self::table . " WHERE " . self::name . " = '" . $this->get_key() . "'";
		$statement = Core::$PDO->query($sql);
		$fetch_all = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch_all as $fetch) {
			foreach ($fetch as $key => $item) {
				if ($key != self::name) {
					$this->langs[ $key ] = $item;
				}
			}
		}
		$this->all = TRUE;
	}

	private function load_lang (string $lang)
	{
		if (language::get_instance()->is_supported_language($lang)) {
			$sql = "SELECT " . $lang . " FROM " . self::table . " WHERE " . self::name . " = '" . $this->get_key() . "'";
			$statement = Core::$PDO->query($sql);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			$this->langs[ $lang ] = $fetch[ $lang ];
		}
	}

	public function __get ($name)
	{
		return $this->get_lang($name);
	}

	public function __set ($name, $value)
	{
		$this->set_lang($name, $value);
	}

	/**
	 * @param $lang
	 * @param $value
	 *
	 * @return bool
	 * @permission iko.language.keys.set.lang
	 *
	 */
	public function set_lang ($lang, $value)
	{
		if (language::get_instance()->is_supported_language($lang)) {
			if ($value != "" && $value != $this->get_lang($lang)) {
				if (User::get_session()->has_permission("iko.language.keys.set.lang")) {
					if (Handler::event("iko.language.keys.set.lang", $this, NULL, TRUE)) {
						$sql_value = Core::$PDO->quote($value);
						$statement = Core::$PDO->exec("UPDATE " . self::table . " Set " . $lang . " = " . $sql_value . " WHERE " . self::name . " = '" . self::get_key() . "' ");
						if ($statement == 1) {
							$this->langs[ $lang ] = $value;

							return TRUE;
						}
					}
				}
			}
		}

		return FALSE;
	}

	public function set_key ($name)
	{
		if ($name != "" && $name != $this->get_key()) {
			if (User::get_session()->has_permission("iko.language.keys.set.name")) {
				if (Handler::event("iko.language.keys.set.name", $name)) {
					$sql_name = Core::$PDO->quote($name);
					$statement = Core::$PDO->exec("UPDATE " . self::table . " Set " . self::table . " = " . $sql_name . " WHERE " . self::name . " = '" . self::get_key() . "' ");
					if ($statement == 1) {
						$this->langs[ $lang ] = $value;

						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}
	public function __toString ()
	{
		return $this->get_lang();
	}
}