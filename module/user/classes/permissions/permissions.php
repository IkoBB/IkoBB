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
 * Time: 21:17
 */
namespace iko\user;

use iko\Core;
use iko\Event\Handler;
use iko\log;
use Iko\user\permissions\User as users;
use Iko\user\permissions\Group as groups;
use Iko\user\permissions\Value as Value;

abstract class Permissions implements iPermissions
{
	public static function get ($class)
	{
		if ($class instanceof User) {
			return self::get_user($class);
		}
		else {
			if ($class instanceof Group) {
				return self::get_group($class);
			}
			else {
				if (is_int($class)) {
					throw new Exception("Wieso Null");
				}
			}
		}

		return NULL;
	}

	public static function gets ($class)
	{
		$array = array ();
		if (!is_array($class)) {

			$class = array ($class);
		}
		if (is_array($class)) {
			foreach ($class as $value) {
				array_push($array, self::get($value));
			}
		}
		if (count($array)) {
			return $array;
		}

		return array ();
	}


	public static function get_user ($class)
	{
		return users::get($class);
	}

	public static function get_group ($class)
	{
		$group = groups::get($class);

		return $group;
	}

	private static function get_value ($name) //
	{
		return Value::get($name);
	}

	private static function has ($permission, $class)
	{
		// TODO: Add has_permission static function for simple resolution.
	}


	protected function __construct ()
	{
		$this->load_permission();
	}

	abstract protected function load_permission ();


	protected $permissions = array ();

	/**
	 * @param mixed
	 *
	 * @return bool
	 */
	protected function add_permission_value ($value)
	{
		if (!$value instanceof Value && is_string($value)) {
			$value = Value::get($value);
		}
		if ($value instanceof Value) {
			if (array_search($value, $this->permissions, TRUE) === FALSE) {
				array_push($this->permissions, $value);

				return TRUE;
			}
			else {
				return TRUE;
			}
		}

		return FALSE;
	}

	protected function remove_permission_value ($value)
	{
		if (!$value instanceof Value && is_string($value)) {
			$value = Value::get($value);
		}
		if ($value instanceof Value) {
			if (array_search($value, $this->permissions, TRUE) !== FALSE) {
				$key = array_search($value, $this->permissions, TRUE);
				unset($this->permissions[ $key ]);
				if (!isset($this->permissions[ $key ])) {
					return TRUE;
				}
			}
			else {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param $permission
	 *
	 * @return bool
	 */
	public function has_permission ($permission): bool
	{
		$result = FALSE;
		if (!$permission instanceof Value) {
			$permission = Value::get($permission);
		}
		if ($permission instanceof Value) {
			if (array_search($permission, $this->permissions, TRUE) !== FALSE) {
				$result = TRUE;
			}
			if ($result == FALSE) {
				$part = explode(".", $permission->get_name());
				if (count($part) > 1) {
					for ($i = (count($part) - 1); $i >= -1; $i--) {
						$sec_part = "";
						if ($i >= 0) {
							for ($x = 0; $x <= $i; $x++) {
								$sec_part .= $part[ $x ] . ".";
								if ($x == $i) {
									$sec_part .= "*";
								}
							}
						}
						else {
							$sec_part = "*";
						}
						$sec_permission = Value::get($sec_part);
						if (array_search($sec_permission, $this->permissions, TRUE) !== FALSE) {
							$result = TRUE;
							break;
						}
					}
				}
			}
		}

		/*
		 *  iko.admin.user.edit.username - Spezifisch
			iko.admin.user.edit.*		- Kategorie
			iko.admin.user		- Spezifisch
			iko.admin           - Spezifisch
			iko.admin.* - Kategorie
			*

		 */

		return $result;
	}

	public function __invoke ($values)
	{
		return $this->has_permission($values);
	}

	/**
	 * @return array //Contains Class Value.
	 */
	public function get_permissions (): array
	{
		return (array)$this->permissions;
	}

	/**
	 * @param $permission
	 * @param $func
	 *
	 * @return bool
	 *
	 * @permission iko.user.permissions.add
	 * @permission iko.user.permissions.remove
	 *              User need $permission itself to remove or add it to someone/some group
	 */
	private function change_permission ($permission, $func)
	{
		$func = str_replace("_permission", "", $func);
		if ($this instanceof users) {
			$handler_value = "iko.user.permissions.user.";
		}
		else if ($this instanceof groups) {
			$handler_value = "iko.user.permissions.group.";
		}
		$handler_value .= $func;
		if (Handler::event($handler_value, $this->get_class()->get_id(),
				User::get_session()->get_id()) || Handler::event("iko.user.permissions." . $func,
				$this->get_class()->get_id(), users::get_session()->get_id())
		) {
			if (!$permission instanceof Value) {
				$permission = Value::get($permission);
			}
			if ($permission instanceof Value) {
				if (Handler::event($permission->get_name(), $this->get_class()->get_id(),
					User::get_session()->get_id())
				) {

					if (($func == "add" && !$this->get_class()->has_permission($permission)) || ($func == "remove" && $this->get_class()->has_permission($permission))) {
						$class = get_called_class();
						if ($func == "add") {
							$sql = "INSERT INTO " . $class::permissions . " (" . $class::id . "," . Permissions::name . ") VALUE('" . $this->get_class()->get_id() . "', '" . $permission->get_name() . "')";
						}
						else if ($func == "remove") {
							$sql = "DELETE FROM " . $class::permissions . " WHERE " . $class::id . " = " . $this->get_class()->get_id() . " AND " . Permissions::name . " = '" . $permission->get_name() . "'";
						}
						$statement = Core::$PDO->exec($sql);
						if ($statement > 0) {
							if ($this->{$func . "_permission_value"}($permission)) {
								Log::add("user", "info", "200",
									"User(" . User::get_session()->get_id() . ") " . $func . " Permission \"" . $permission->get_name() . "\" to " . get_class($this->get_class()) . "(" . $this->get_class()->get_id() . ")",
									array (
										"permission_name"             => $permission->get_name(),
										get_class($this->get_class()) => $this->get_class()->get_id(),
										"Session User"                => User::get_session()->get_id()));

								return TRUE;
							}
						}
					}
				}
			}
		}

		return FALSE;
	}

	/**
	 * @param $permission
	 *
	 * @return bool
	 *
	 * @see \iko\user\Permissions::change_permission();
	 * @see \iko\user\Permissions::change_permission();
	 */
	public function add_permission ($permission): bool
	{
		return $this->change_permission($permission, __FUNCTION__);
	}

	/**
	 * @param $permission
	 *
	 * @return bool
	 *
	 * @see \iko\user\Permissions::change_permission();
	 * @see \iko\user\Permissions::change_permission();
	 */
	public function remove_permission ($permission): bool
	{
		return $this->change_permission($permission, __FUNCTION__);
	}

	abstract public function get_class (): operators;

}