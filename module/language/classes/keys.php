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

use iko\{
	Core, Event\Handler, lib\multiton\cache_string, user\User
};
use PDO;

class key extends cache_string implements iKey
{
	const table = language::table;
	const name = language::name;
	protected static $cache = array ();
	protected static $cache_exist = array ();
	public static function get ($id = 0, $reload = FALSE):key
	{
		return parent::get($id, $reload);
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 * @permission iko.language.keys.delete
	 *             Allows User to delete language keys
	 */
	public static function delete($name) {
		if(!$name instanceof key) {
			$name = self::get($name);
		}
		if(User::get_session()->has_permission("iko.language.keys.delete")) {
			$key = Core::PDO()->quote($name->get_key());
			$sql = "DELETE FROM " . self::table . " WHERE " . self::name . " = " . $key;
			$statement = Core::PDO()->exec($sql);
			if ($statement == 1) {
				unset(self::$cache[$name->get_key()], self::$cache_exist[$name->get_key()]);
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 * @permission iko.language.keys.create
	 *             Allows User to create new Keys
	 */
	public static function create(string $name) {
		if($name != "" && !self::exist($name)) {
			if(User::get_session()->has_permission("iko.language.keys.create")){
				$name = Core::PDO()->quote($name);
				$sql = "INSERT INTO " . self::table . " (" . self::name . ") VALUE('" . $name . "')";
				$statement = Core::PDO()->exec($sql);
				if ($statement == 1) {
					return true;
				}
			}
		}
		return false;
	}
	private $key;
	private $languages = array ();
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
			if (!isset($this->languages[ $lang ])) {
				$this->load_lang($lang);
			}

			return $this->languages[ $lang ] ?? "";
		}
		return "";
	}

	public function get_lang_all (): array
	{
		if (!$this->all) {
			$this->all = TRUE;
			$languages = language::get_instance()->get_languages();
			foreach ($languages as $item) {
				if (!isset($this->languages[ $item ])) {
					$this->all = FALSE;
					break;
				}
			}
			if (!$this->all) {
				$this->load_lang_all();
			}
		}
		return $this->languages;
	}

	private function load_lang_all ()
	{
		$sql = "SELECT * FROM " . self::table . " WHERE " . self::name . " = '" . $this->get_key() . "'";
		$statement = Core::PDO()->query($sql);
		$fetch_all = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch_all as $fetch) {
			foreach ($fetch as $key => $item) {
				if ($key != self::name) {
					$this->languages[ $key ] = $item;
				}
			}
		}
		$this->all = TRUE;
	}

	private function load_lang (string $lang)
	{
		if (language::get_instance()->is_supported_language($lang)) {
			$sql = "SELECT " . $lang . " FROM " . self::table . " WHERE " . self::name . " = '" . $this->get_key() . "'";
			$statement = Core::PDO()->query($sql);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			$this->languages[ $lang ] = $fetch[ $lang ];
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
						$sql_value = Core::PDO()->quote($value);
						$statement = Core::PDO()->exec("UPDATE " . self::table . " Set " . $lang . " = " . $sql_value . " WHERE " . self::name . " = '" . self::get_key() . "' ");
						if ($statement == 1) {
							$this->languages[ $lang ] = $value;

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
					$sql_name = Core::PDO()->quote($name);
					$statement = Core::PDO()->exec("UPDATE " . self::table . " Set " . self::table . " = " . $sql_name . " WHERE " . self::name . " = '" . self::get_key() . "' ");
					if ($statement == 1) {
						$this->key = $name;

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