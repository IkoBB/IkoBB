<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.12.2016
 * Time: 14:55
 */

namespace iko\language;


class language_Keys
{
	const table = languageConfigs::tableTranslation;
	const name = "translation_key";

	private static $cache = array ();
	private static $cache_exist = array ();

	/*
	 * For string && int = return class
	 * For array = return array
	 */
	public static function get ($ids = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_array($ids) || is_string($ids)) {
			if (is_array($ids)) {
				self::exist($ids);
			}
			if (is_string($ids)) {
				$ids = array ($ids);
			}
			$array = array ();
			foreach ($ids as $id) {
				if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						self::$cache[ $id ] = new $class($id);
						array_push($array, self::$cache[ $id ]);
					}
				}
				else {
					array_push($array, self::$cache[ $id ]);
				}
			}
			if (count($array) == 1) {
				return $array[0];
			}
			else {
				return $array;
			}
		}

		return NULL;
	}

	public static function search ($args = array (), $or = FALSE, $suffix = "") // TODO: Complete Function for Searching after single and Mutliple user
	{
		$sql = "SELECT " . self::name . " FROM " . self::table . "";
		$equal = ($or) ? "OR" : "AND";
		if (count($args) > 0) {
			$i = count($args);
			$string = " WHERE";
			foreach ($args as $key => $var) {
				if (is_array($var)) {
					foreach ($var as $operator => $value) {
						$string .= ' ' . $key . " " . $operator . " '" . $value . "'";
					}
				}
				else {
					$string .= ' ' . $key . " = '" . $var . "'";
				}
				if ($i > 1) {
					$string .= " " . $equal;
				}
			}
			$sql .= $string;
		}
		$sql .= " " . $suffix;
		$ids = array ();
		$statement = Core::$PDO->query($sql);
		if ($statement !== FALSE) {
			$fetch_all = $statement->fetchAll();
			foreach ($fetch_all as $fetch) {
				array_push($ids, $fetch[ self::name ]);
			}
			$user_array = self::get($ids);

			return $user_array;
		}
		else {
			return NULL;
		}
	}

	/**
	 * @param int  $ids
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	public static function exist ($ids = 0, $reload = FALSE)
	{
		if ($ids !== NULL) {
			$statement = Core::$PDO->prepare("SELECT " . self::name . " FROM " . self::table . " WHERE " . self::name . " = :ids");
			if (is_string($ids) || is_int($ids)) {
				if (!isset(self::$cache_exist[ $ids ]) || $reload) {
					$statement->bindParam(':ids', $ids);
					$statement->execute();

					if ($statement->rowCount() > 0) {
						self::$cache_exist[ $ids ] = TRUE;

						return TRUE;
					}
					else {
						self::$cache_exist[ $ids ] = FALSE;

						return FALSE;
					}
				}

				return self::$cache_exist[ $ids ];
			}
			else {
				if (is_array($ids)) {

					foreach ($ids as $id) {
						if (!isset(self::$cache_exist[ $id ]) || $reload) {
							$statement->bindParam(':ids', $id);
							$statement->execute();
							if ($statement->rowCount() > 0) {
								self::$cache_exist[ $id ] = TRUE;
							}
							else {
								self::$cache_exist[ $id ] = FALSE;
							}
						}
					}

					return TRUE;
				}
				else {
					return FALSE;
				}
			}
		}
		else {
			return FALSE;
		}
	}

	private $translation_key = "";
	private $default_language = "english";
	private $set_language = "";

	public function __construct ($name)
	{
		if (is_string($name) && self::exist($name)) {
			$sql = "SELECT * FROM " . self::table . " WHERE " . self::name . " = '" . $name . "'";
			$statement = Core::$PDO->query($sql);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
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