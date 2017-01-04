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
 * Created by PhpStorm.
 * User: Marcel
 * Date: 11.11.2016
 * Time: 20:34
 */
namespace iko;
$files = scandir(__DIR__ . "/event/");
unset($files[0], $files[1]);
foreach ($files as $item) {
	require_once(__DIR__ . "/event/" . $item);
}