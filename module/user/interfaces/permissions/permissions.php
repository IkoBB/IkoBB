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
 * Time: 00:27
 */
namespace iko\user;

interface iPermissions
{
	const table = "{prefix}permissions";
	const name = "permission_name";

	const group_assignment = "{prefix}group_assignment";
	const group_permissions = "{prefix}group_permissions";

	const user_assignment = "{prefix}user_assignment";
	const user_permissions = "{prefix}user_permissions";

	public function has_permission ($permission): bool;

	public function get_permissions (): array;

	public function add_permission ($permission): bool;

	public function remove_permission ($permission): bool;
}