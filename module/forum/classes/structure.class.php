<?php
/**
 * This file is part of IkoBB Forum and belongs to the module <Forum>.
 *
 * @copyright (c) 2017 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

/**
 * Created by PhpStorm.
 * User: Jannik
 * Date: 29.01.2017
 * Time: 21:09
 */

namespace iko\forum;


use iko\Core;
use iko\Exception;
use iko\lib\multiton\cache_int;
use iko\user\User;

abstract class structure extends cache_int
{
	const table = NULL;
	const id = NULL;

	const column_name = NULL;
	const column_description = NULL;
	const column_parent = NULL;
	const column_parent_type = NULL;
	const PARENT_CATEGORY = 1;
	const PARENT_BOARD = 2;

	protected static $cache = array ();
	protected static $cache_exist = array ();


	public static function create (string $name, string $description, int $parent = NULL)
	{
		$class = get_called_class();
		$temp_class = explode("\\", $class);
		$temp_class = $temp_class[ count($temp_class) - 1 ];

		if ($name != "" && $description != "") {

			if (User::get_session()->has_permission("iko.forum." . $temp_class . ".create")) {
				if ($parent !== NULL) {
					$columns = $class::column_name . ", " . $class::column_description . ", " . $class::column_parent;
					$values = $name . "', '" . $description . "', '" . $parent;
				}
				else {
					$columns = $class::column_name . ", " . $class::column_description;
					$values = $name . "', '" . $description;
				}

				$sql = "INSERT INTO " . $class::table . " (" . $columns . ") VALUE('" . $values . "')";
				$statement = Core::PDO()->prepare($sql);
				$statement->execute();
				if ($statement == 1) {
					return TRUE;
				}
			}
		}
	}

	public static function delete ($id)
	{
		if (!$id instanceof category) {
			$id = self::get($id);
		}
		$class = get_called_class();
		$temp_class = explode("\\", $class);
		$temp_class = $temp_class[ count($temp_class) - 1 ];

		if (User::get_session()->has_permission("iko.forum." . $temp_class . ".delete")) {
			$key = Core::PDO()->quote($id->get_Id());
			$sql = "DELETE FROM " . $class::table . " WHERE " . $class::id . " = " . $key;
			$statement = Core::PDO()->exec($sql);
			if ($statement == 1) {
				unset($class::$cache[ $id->get_Id() ], $class::$cache_exist[ $id->get_Id() ]);

				return TRUE;
			}
		}

		return FALSE;
	}

	protected $id;
	protected $name;
	protected $description;
	protected $order;
	protected $parent;
	protected $parent_type;


	public function __construct (int $id)
	{
		$class = get_called_class();
		if ($class::exist($id)) {
			try {
				$statement = Core::PDO()->query("SELECT * FROM " . $class::table . " WHERE " . $class::id . " = $id");
				$fetch = $statement->fetch(\PDO::FETCH_ASSOC);
			}
			catch (Exception $exception) {
				die("Fehler:" . $exception);
			}
			if (isset($fetch)) {
				foreach ($fetch as $key => $value) {
					$temp_class = explode("\\", $class);
					$temp_class = $temp_class[ count($temp_class) - 1 ];
					$temp_key = str_replace("forum_" . $temp_class . "_", "", $key);
					$this->{$temp_key} = $value;
				}
			}
		}
	}


	public function get_child_boards ($type = self::PARENT_CATEGORY): array
	{
		$results = array ();
		$sql = "SELECT " . board::id . " FROM " . board::table . " WHERE " . board::column_parent . " = " . $this->id . " AND " . board::column_parent_type . " = " . $type;
		$statement = Core::PDO()->query($sql);
		$fetch = $statement->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($fetch as $row => $id) {

			$results[ $id[ board::id ] ] = board::get($id[ board::id ]);
		}

		return $results;
	}


	/**
	 * @return mixed
	 */
	public function get_id (): int
	{
		return (int)$this->id;
	}

	/**
	 * @return mixed
	 */
	public function get_name (): string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function get_description (): string
	{
		return $this->description;
	}

	/**
	 * @return mixed
	 */
	public function get_order (): int
	{
		return $this->order;
	}

	/**
	 * @return mixed
	 */
	public function get_parent (): Category
	{
		$parent = new category($this->parent);

		return $parent;
	}

	/**
	 * @param mixed $name
	 */
	public function set_name ($name)
	{
		$this->name = $name;
	}

	/**
	 * @param mixed $description
	 */
	public function set_description ($description)
	{
		$this->description = $description;
	}

	/**
	 * @param mixed $order
	 */
	public function set_order ($order)
	{
		$this->order = $order;
	}

	/**
	 * @param mixed $parent
	 */
	public function set_parent ($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @param mixed $parent_type
	 */
	public function set_parent_type ($parent_type)
	{
		$this->parent_type = $parent_type;
	}

}