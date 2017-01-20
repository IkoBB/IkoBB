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
 * Time: 21:29
 */
namespace iko\user\permissions;

use PDO;
use iko\{
	Core, user\Group as groups, user\iGroup, user\iOperators, user\operators, user\Permissions
};

class Group extends Permissions
{
	const id = groups::id;
	const permissions = Permissions::group_permissions;
	const assignment = Permissions::group_assignment;

	private static $cache = array ();

	public static function get ($class, $reload = FALSE): Permissions
	{
		if ($class instanceof groups) {
			$id = intval($class->get_id());
		}
		if (is_int($class)) {
			$id = $class;
		}
		if (is_string($class)) {
			$id = intval($class);
		}
		if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
			self::$cache[ $id ] = new Group($id);
			return self::$cache[ $id ];
		}
		else {
			return self::$cache[ $id ];
		}
	}

	private $group_class;
	private $group_id;
	private $group_parent_perm = array();

	public function __construct ($group)
	{
		$id = 0;
		if ($group instanceof groups) {
			$id = $group->get_id();
		}
		else {
			if (is_int($group)) {
				$id = $group;
			}
			if (is_string($group)) {
				$id = intval($group);
			}
		}
		$this->group_id = $id;
		$this->group_class = groups::get($this->group_id);
		parent::__construct();
	}

	protected function load_permission ()
	{
		$sql = "SELECT * FROM " . self::permissions . " WHERE " . groups::id . " = " . $this->get_class()->get_Id();
		$statement = Core::PDO()->query($sql);
		if ($statement !== FALSE) {
			foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $fetch) {
				$this->add_permission_value($fetch["permission_name"]);
			}
		}
		$parents = $this->get_class()->get_parents_all();
		foreach ($parents as $item) {
			$perm = self::get($item);
			$permissions = $perm->get_permissions();
			foreach ($permissions as $value) {
				if ($this->add_permission_value($value)) {
					if (array_search($value, $this->group_parent_perm, TRUE) === FALSE) {
						array_push($this->group_parent_perm, $value);
					}
				}
			}
		}
	}

	public function get_class (): iGroup
	{
		return $this->group_class;
	}
}