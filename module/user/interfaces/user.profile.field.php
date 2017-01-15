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
 * Time: 01:04
 */
namespace iko\user\profile;

use iko\user\iUser;
use iko\user\iUser_profile;

interface iField
{
	const table = iUser_profile::fields;
	const id = "user_field_id";
	const name = "user_field_name";

	public function get_id (): int;

	public function get_name (): string;

	public function get_owner (): iUser;

	public function get_display (): string;

	public function get_options ();

	public function set_name (string $value): bool;
}