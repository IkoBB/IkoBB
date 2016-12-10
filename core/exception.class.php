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
namespace iko;

use \Exception as Exi;

class Exception extends Exi
{
	public function __construct($message = "", $code = 0, $previous = null)
	{
		if (class_exists("Iko\\log", FALSE)) {
			//Module | Type | Code | Msg | Else
			$exploder = explode(" | ", $message);
			if (count($exploder) > 3) {
				$module = $exploder[0];
				$type = $exploder[1];
				$msg_code = $exploder[2];
				$msg = $exploder[3];
				$extra = "Exception";
				log::add($module, $type, $msg_code, $msg, $extra);
			}
		}
		parent::__construct($message, $code, $previous);
	}
}