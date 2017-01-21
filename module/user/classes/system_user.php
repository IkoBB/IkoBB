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
 * Date: 15.01.2017
 * Time: 12:38
 */
namespace iko\user;

use iko\lib\singleton\instance;

class system_user extends instance implements iOperators
{
	protected static $instance = NULL;

	public static function get_instance ($args = NULL): iUser
	{
		return parent::get_instance($args);
	}

	protected $id = 0;
	protected $name = "System";
	protected $group = NULL;

	private function __construct ()
	{
		/** @noinspection PhpUnusedLocalVariableInspection */
		$group = array (
			(new class implements iGroup
			{
				public function get_style ()
				{
					return "";
				}

				public function get_rang (): int
				{
					return 0;
				}

				public function set_rang ($rang): bool
				{
					return FALSE;
				}

				public function get_parents ()
				{
					return array ();
				}

				public function get_parents_all (): array
				{
					return array ();
				}

				public function get_childes (): array
				{
					return array ();
				}

				public function get_childes_all (): array
				{
					return array ();
				}

				public function get_members (): array
				{
					return array ();
				}

				public function get_members_all (): array
				{
					return array ();
				}

				public function reload_members ()
				{

				}

				public function reload_childes ()
				{

				}

				public function reload_parents ()
				{

				}

				public function reload ()
				{

				}

				public function add_member ($user): bool
				{
					return FALSE;
				}

				public function remove_member ($user): bool
				{
					return FALSE;
				}

				public function get_id (): int
				{
					return 0;
				}

				public function get_permission_class (): Permissions
				{
					return NULL;
				}

				public function has_permission ($permission, $args, $pre)
				{
					return FALSE;
				}

				public function add_permission ($permission): bool
				{
					return FALSE;
				}

				public function remove_permission ($permission): bool
				{
					return FALSE;
				}

				public function get_name (): string
				{
					return "System";
				}
			}));
	}

	public function get_id (): int
	{
		return 0;
	}

	public function get_permission_class (): Permissions
	{
		return null;
	}

	public function has_permission ($permission, $args, $pre)
	{
		return FALSE;
	}

	public function add_permission ($permission): bool
	{
		return FALSE;
	}

	public function remove_permission ($permission): bool
	{
		return FALSE;
	}

	public function get_name (): string
	{
		return $this->name;
	}
}