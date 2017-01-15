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
 * Date: 05.11.2016
 * Time: 21:34
 */
namespace iko\user;

interface iGroup extends iOperators
{
	const table = "{prefix}usergroups";
	const id = "usergroup_id";
	const name = "usergroup_name";
	const assignment = Permissions::group_assignment;

	public function get_style ();

	public function get_rang (): int;

	public function set_rang ($rang): bool;

	public function get_parents ();

	public function get_parents_all (): array;

	public function get_childes (): array;

	public function get_childes_all (): array;

	public function get_members (): array;

	public function get_members_all (): array;


	public function reload_members ();

	public function reload_childes ();

	public function reload_parents ();

	public function reload ();

	public function add_member ($user): bool;

	public function remove_member ($user): bool;

}