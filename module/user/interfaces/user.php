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
 * Time: 20:04
 */
namespace iko\user;

interface iUser
{

	/**
	 * @return integer
	 */
	public function get_id (): int;

	/**
	 * @return string
	 */
	public function get_name (): string;

	/**
	 * @return array|Group
	 */
	public function get_groups ();

	/**
	 * @return string as Date Format
	 */
	public function get_joined_Date (): string;

	/**
	 * @return integer
	 */
	public function get_joined_Time (): int;

	/**
	 * @return string as Date Format
	 */
	public function get_last_login_Date (): string;

	/**
	 * @return integer
	 */
	public function get_last_login_Time (): int;

	/**
	 * @param $pass
	 *
	 * @return string
	 *
	 * The salt is for more security
	 */
	public function salt ($pass): string;

	/**
	 * @param string $old
	 * @param string $new
	 * @param string $sec
	 *
	 * @return bool
	 */
	public function change_password ($old, $new, $sec): bool;

	/**
	 * @return integer
	 */
	public function get_language (): int;

	/**
	 * @return integer
	 */
	public function get_template (): int;

	/**
	 * @param $name
	 *
	 * @return bool
	 * @permission iko.user.set.user_name
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_name ($name): bool;

	/**
	 * @param $mail
	 *
	 * @return bool
	 * @permission iko.user.set.user_email
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_email ($mail): bool;

	/**
	 * @param $template
	 *
	 * @return bool
	 * @permission iko.user.set.user_template
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_template ($template): bool;

	/**
	 * @param $language
	 *
	 * @return bool
	 * @permission iko.user.set.user_language
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_language ($language): bool;

}
