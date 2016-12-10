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

use Iko\user\permissions\User as users;
use Iko\user\permissions\Group as groups;
use Iko\user\permissions\Value as Value;

abstract class Permissions
{
	const table = "{prefix}permissions";
	const name = "permission_name";

	const group_assignment = "{prefix}group_assignment";
	const group_permissions = "{prefix}group_permissions";

	const user_assignment = "{prefix}user_assignment";
	const user_permissions = "{prefix}user_permissions";

	public static function get ($class)
	{
		$array = array ();
		if (!is_array($class)) {

			$class = array ($class);
		}
		if (is_array($class)) {
			foreach ($class as $value) {
				if ($value instanceof User) {
					array_push($array, self::get_user($value));
				}
				else {
					if ($value instanceof Group) {
						array_push($array, self::get_group($value));
					}
					else {
						if (is_int($class)) {
							throw new Exception("Wieso Null");
						}
					}
				}
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
	 * @param \Iko\Permissions\Value $value
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

	/**
	 * @param $permission
	 *
	 * @return bool
	 */
	public function has_permission ($permission)
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
	public function get_Permissions ()
	{
		return (array)$this->permissions;
	}

	abstract public function add_permission ($permission);

	abstract public function get_class ();

}