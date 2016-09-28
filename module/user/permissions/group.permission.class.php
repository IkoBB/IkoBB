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

class group_permission extends permission {
	const permissions = permission::group_permissions;
	const assignment = permission::group_assignment;

	private $group_class;
	private $group_id;
	public function __construct ($group)
	{
		if($group instanceof group) {
			$id = $group->get_Id();
		}
		else if(is_int($group))
		{
			$id = $group;
		}
		else if(is_string($group)) {
			throw new Exception("Permissions | Group | # 0001");
		}
		$this->group_id = $id;
		$this->group_class = group::get($this->group_id);
	}
}