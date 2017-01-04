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
namespace iko;
$files = array(
	"interface.php",
	"abstract.class.php",
	"class.php",
	"loader_file.class.php",
	"loader_pdo.class.php"
);
foreach ($files as $item) {
	require_once(__DIR__ . "/config/" . $item);
}

