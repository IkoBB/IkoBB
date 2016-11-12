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
 * Date: 30.10.2016
 * Time: 23:03
 */
namespace Iko;

$function = define_post("func", "");
switch ($function) {
	case "login":
		$user = define_post("user", "");
		$pass = define_post("pass", "");
		if ($user != "" && $pass != "") {
			if (User::login($user, $pass)) {
				echo "TRUE";
			}
			else {
				echo "FALSE";
			}
		}
	break;
	case "test":
		Event\Handler::test();
	break;
}
