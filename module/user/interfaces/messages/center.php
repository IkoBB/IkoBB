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
 * Time: 22:56
 */
namespace iko\user\messages;


interface iCenter
{
	/**
	 * @return array
	 *         array includes only iMessage items
	 */
	public function get_messages_all (): array;

	/**
	 * @return array
	 *         array includes only iMessage items
	 */
	public function get_sent_messages_all (): array;

	/**
	 * @return array
	 *         array includes only iMessage items
	 */
	public function get_received_messages_all (): array;

	/**
	 * @param int $id
	 *
	 * @return \iko\user\messages\iMessage
	 */
	public function get_message (int $id): iMessage;

	/**
	 * @param int $id
	 *
	 * @return \iko\user\messages\iMessage
	 */
	public function get_sent_message (int $id): iMessage;

	/**
	 * @param int $id
	 *
	 * @return \iko\user\messages\iMessage
	 */
	public function get_received_message (int $id): iMessage;

	/**
	 * @param \iko\user\messages\iMessage|int $message
	 *
	 * @return bool
	 */
	public function send_message (int $message): bool;

	/**
	 * @param \iko\user\messages\iMessage|int $message
	 *
	 * @return bool
	 */
	public function delete_message (int $message): bool;
	//public function get_class():iOperators;
}