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
namespace Iko\Permissions;

use Iko;
use Iko\Group as groups;

class Group extends Permissions
{
	const permissions = Permissions::group_permissions;
	const assignment = Permissions::group_assignment;

	private static $cache = array ();

	public static function get ($class, $reload = FALSE)
	{
		if ($class instanceof groups) {
			$id = $class->get_ID();
		}
		else {
			if (is_int($class)) {
				$id = $class;
			}
		}
		if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
			self::$cache[ $id ] = new Group($id);
		}
		else {
			return self::$cache[ $id ];
		}
	}

	private $group_class;
	private $group_id;

	public function __construct ($group)
	{
		if ($group instanceof groups) {
			$id = $group->get_Id();
		}
		else {
			if (is_int($group)) {
				$id = $group;
			}
			else {
				if (is_string($group)) {
					throw new Exception("Permissions | Group | # 0001");
				}
			}
		}
		$this->group_id = $id;
		$this->group_class = groups::get($this->group_id);
		parent::__construct();
	}

	protected function load_permission ()
	{
		$sql = "SELECT * FROM " . self::permissions . " as assi LEFT JOIN " . Permissions::table . " as perm WHERE perm.permission_id = assi.group_permission_permission_id";
		$statement = Core::$PDO->prepare($sql, array (PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
	}

	public function get_type ()
	{
		// TODO: Implement get_type() method.
	}

	public function get_class ()
	{
		// TODO: Implement get_class() method.
	}
}