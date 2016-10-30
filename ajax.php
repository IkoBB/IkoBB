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
 * Time: 23:07
 */
namespace Iko;
require_once './core/core.php';
$module = define_post("module", "");
if ($module != "" && module::exist($module)) {
	if (module::request($module, TRUE)->load_ajax() === FALSE) {
		echo "FALSE";
	}
}