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
namespace Iko;

class user_permission extends permission {
	const permissions = permission::user_permissions;
	const assignment = permission::user_assignment;

	private $user_class;
	private $user_id;
	private $user_groups = array();
	private $groups = array();


	public function __construct ($user)
	{
		if($user instanceof user) {
			$id = $user->get_Id();
		}
		else if(is_int($user))
		{
			$id = $user;
		}
		else if(is_string($user)) {
			throw new Exception("Permissions | User | # 0001");
		}
		$this->user_id = $id;
		$this->user_class = user::get($this->user_id);
		$this->load_groups();
	}

	private function load_groups ()
	{
		$this->user_groups = $this->user_class->get_groups();
		$this->groups = permission::get($this->user_groups);
	}
}