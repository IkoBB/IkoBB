<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * @author Marcel
 *
 */
namespace Iko;

/**
 * @author Marcel
 *
 */
class session
{
	private static $user_agent;
	private static $ip;
	private static $session_id;
	private static $user_id;
	private static $session_type;

	/**
	 *
	 */
	public static function init($phase)
	{
		switch ($phase) {
			case 0:
				session_start();
			break;
			case 1:

			break;
			default:
				NULL;
			break;
		}
	}
}