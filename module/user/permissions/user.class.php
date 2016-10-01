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
use Iko\User as users;


class User extends Permissions
{
	const permissions = Permissions::user_permissions;
	const assignment = Permissions::user_assignment;

	private static $cache = array ();

	public static function get ($class, $reload = FALSE)
	{
		if ($class instanceof users) {
			$id = $class->get_ID();
		}
		else {
			if (is_int($class)) {
				$id = $class;
			}
		}
		if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
			self::$cache[ $id ] = new User($id);
		}
		else {
			return self::$cache[ $id ];
		}
	}

	private $user_class;
	private $user_id;
	private $user_groups = array ();
	private $groups = array ();


	public function __construct ($user)
	{
		if ($user instanceof users) {
			$id = $user->get_Id();
		}
		else {
			if (is_int($user)) {
				$id = $user;
			}
			else {
				if (is_string($user)) {
					throw new Exception("Permissions | User | # 0001");
				}
			}
		}
		$this->user_id = $id;
		$this->user_class = users::get($this->user_id);
		$this->load_groups();
		parent::__construct();
	}

	private function load_groups ()
	{
		$this->user_groups = $this->user_class->get_groups();
		$this->groups = Permissions::get($this->user_groups);
		foreach ($this->groups as $group) {

		}
	}
	public function add_permission ($permission)
	{
		if(!$permission instanceof values) {
			$permission = values::get($permission);
		}

		return TRUE;
	}

	protected function load_permission ()
	{

		$statement = Core::$PDO->prepare("SELECT * FROM " . self::user_permissions . " WHERE ");

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