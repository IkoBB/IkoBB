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
 * Time: 00:00
 */
namespace iko\user;

interface iOperators
{
	public function get_id (): int;

	public function get_permission_class (): Permissions;

	public function has_permission ($permission, $args, $pre);

	public function add_permission ($permission): bool;

	public function remove_permission ($permission): bool;

	public function get_name (): string;
}