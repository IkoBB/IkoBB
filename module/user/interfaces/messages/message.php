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
 * Time: 22:58
 */

namespace iko\user\messages;


use iko\user\iOperators;

interface iMessage
{
	/*
	 * Conversations are like chats only with the difference a new subject will open a new chat
	 * Like TangoBB
	 */
	/**
	 * @return \iko\user\iOperators
	 */
	public function get_sender (): iOperators;

	/**
	 * @return array
	 *         array includes only iOperators items
	 */
	public function get_receiver (): array;

	public function get_text (): string;

	public function get_subject (): string;

	public function get_send_date (): string;

	public function get_send_time (): int;

	public function get_attachment ();
}