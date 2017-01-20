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
 * Date: 28.09.2016
 * Time: 19:47
 */
namespace iko\user;

use iko\{
	Core, PDO
};

class Group extends operators implements iGroup //Todo: Complete
{
	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($id = 0, $reload = FALSE): iGroup
	{
		return parent::get($id, $reload);
	}

	private $style;
	private $rang;
	private $parents = NULL;
	private $parents_all = NULL;
	private $childes = NULL;
	private $childes_all = NULL;
	private $members = NULL;
	private $members_all = NULL;

	/**
	 * group constructor.
	 *
	 * @param $group_id
	 */
	protected function __construct ($group_id)
	{
		if (self::exist($group_id)) {
			$statement = Core::PDO()->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = " . $group_id);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("usergroup_", "", $key);
				$this->{$temp_key} = $value;
			}
			$this->id = intval($fetch["usergroup_id"]);
		}
	}

	/**
	 *
	 */
	private function load_parents ()
	{
		$this->parents = array ();
		$this->parents_all = array ();
		$sql = "SELECT * FROM " . self::assignment . " WHERE child_group_id = " . $this->get_id();
		$statement = Core::PDO()->query($sql);
		if ($statement !== FALSE) {
			foreach ($statement->fetchAll() as $row) {
				$parent = self::get($row["parent_group_id"]);
				if (array_search($parent, $this->parents, TRUE) === FALSE) {
					array_push($this->parents, $parent);
				}
				if (array_search($parent, $this->parents_all, TRUE) === FALSE) {
					array_push($this->parents_all, $parent);
				}
				foreach ($parent->get_parents_all() as $item) {
					if (array_search($item, $this->parents_all, TRUE) === FALSE) {
						array_push($this->parents_all, $item);
					}
				}
			}
		}
	}

	private function load_childes ()
	{
		$this->childes = array ();
		$this->childes_all = array ();
		$sql = "SELECT * FROM " . self::assignment . " WHERE parent_group_id = " . $this->get_id();
		$statement = Core::PDO()->query($sql);
		if ($statement !== FALSE) {
			foreach ($statement->fetchAll() as $row) {
				$child = self::get($row["child_group_id"]);
				if (array_search($child, $this->childes, TRUE) === FALSE) {
					array_push($this->childes, $child);
				}
				if (array_search($child, $this->childes_all, TRUE) === FALSE) {
					array_push($this->childes_all, $child);
				}
				foreach ($child->get_childes_all() as $item) {
					if (array_search($item, $this->childes_all, TRUE) === FALSE) {
						array_push($this->childes_all, $item);
					}
				}
			}
		}
	}

	private function load_members ()
	{
		$this->members = array ();
		$this->members_all = array ();
		$statement = Core::PDO()->query("SELECT " . User::id . " FROM " . Permissions::user_assignment . " WHERE " . self::id . " = " . $this->get_id() . "");
		$fetch_all = $statement->fetchAll();
		if ($statement !== FALSE) {
			foreach ($fetch_all as $item) {
				$user = User::get($item[ User::id ]);
				if (array_search($user, $this->members, TRUE) === FALSE) {
					array_push($this->members, $user);
				}
				if (array_search($user, $this->members_all, TRUE) === FALSE) {
					array_push($this->members_all, $user);
				}
			}
		}
		foreach ($this->get_childes_all() as $item) {
			foreach ($item->get_members_all() as $value) {
				if (array_search($value, $this->members_all, TRUE) === FALSE) {
					array_push($this->members_all, $value);
				}
			}
		}
	}

	public function reload_members ()
	{
		$this->load_members();
	}

	public function reload_childes ()
	{
		$this->load_childes();
	}

	public function reload_parents ()
	{
		$this->load_parents();
	}

	public function reload ()
	{
		if ($this->members !== NULL && $this->members_all !== NULL) {
			$this->reload_members();
		}
		if ($this->parents !== NULL && $this->parents_all !== NULL) {
			$this->reload_parents();
		}
		if ($this->childes !== NULL && $this->childes_all !== NULL) {
			$this->reload_childes();
		}
	}

	/*
	 * Parent | Child
	 * Gast   | Member
	 * Member | VIP
	 * Member | Moderator
	 * Test   | Moderator
	 *
	 */
	/**
	 * @return mixed
	 */
	public function get_id (): int
	{
		return intval($this->id);
	}

	public function get_name (): string
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function get_rang (): int
	{
		return intval($this->rang);
	}

	/**
	 * @param mixed $group_rang
	 *
	 * @return bool
	 */
	public function set_rang ($group_rang): bool
	{
		$this->rang = $group_rang;

		return FALSE;
	}

	public function get_parents (): array
	{
		if ($this->parents === NULL && $this->parents_all === NULL) {
			$this->load_parents();
		}

		return $this->parents;
	}

	public function get_parents_all (): array
	{
		if ($this->parents === NULL && $this->parents_all === NULL) {
			$this->load_parents();
		}

		return $this->parents;
	}

	public function get_childes (): array
	{
		if ($this->childes === NULL && $this->childes_all === NULL) {
			$this->load_childes();
		}

		return $this->childes;
	}

	public function get_childes_all (): array
	{
		if ($this->childes === NULL && $this->childes_all === NULL) {
			$this->load_childes();
		}

		return $this->childes_all;
	}

	public function get_style ()
	{
		return $this->style;
	}

	public function get_members (): array
	{
		if ($this->members === NULL && $this->members_all === NULL) {
			$this->load_members();
		}

		return $this->members;
	}

	public function get_members_all (): array
	{
		if ($this->members === NULL && $this->members_all === NULL) {
			$this->load_members();
		}

		return $this->members_all;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __get ($value)
	{
		$func = 'get_' . $value;
		if (is_callable(get_called_class(), $func)) {
			return $this->{$func}();
		}
		else {
			return NULL;
		}
	}

	/**
	 * @param $name
	 * @param $values
	 */
	public function __set ($name, $values)
	{
		$func = "set_" . $name;
		if (is_callable(get_called_class(), $func)) {
			$this->{$func}($values);
		}
	}

	/**
	 * @param $user
	 *
	 * @return bool
	 *
	 * @see \iko\user\User::add_group();
	 */
	public function add_member ($user): bool
	{
		if (!$user instanceof User) {
			$user = User::get($user);
		}

		return $user->add_group($this);
	}

	/**
	 * @param $user
	 *
	 * @return bool
	 *
	 * @see \iko\user\User::remove_group();
	 */
	public function remove_member ($user): bool
	{
		if (!$user instanceof User) {
			$user = User::get($user);
		}

		return $user->remove_group($this);
	}

	/*public function get_Displayname ()
	{
		return $this->style;
	}*/
}