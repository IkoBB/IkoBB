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
namespace Iko;

class permission
{
	const table = "{prefix}permissions";

	const group_assignment = "{prefix}group_assignment";
	const group_permissions = "{prefix}group_permissions";

	const user_assignment = "{prefix}user_assignment";
	const user_permissions = "{prefix}user_permissions";


	private static $users = array ();
	private static $groups = array ();

	public static function get ($class)
	{
		$array = array();
		if(!is_array($class)) {
			$class = array($class);
		}
		if(is_array($class)) {
			foreach($class as $value) {
				if ($value instanceof user) {
					array_push($array, self::get_user($value));
				}
				else {
					if ($value instanceof group) {
						array_push($array, self::get_group($value));
					}
					else {
						if (is_int($class)) {
							return NULL;
						}
					}
				}
			}
		}

	}

	public static function get_user ($class)
	{

	}

	public static function get_group ($class)
	{

	}

	private static function get_permission_var ($class)
	{

	}


	protected $permissions = array ();

	protected function __construct() {
		$this->load_permission();
	}
	protected function load_permission() {

	}
	public function has_permission ($permission)
	{
		return TRUE;
	}

	public function add_permission ($permission)
	{
		return TRUE;
	}
}