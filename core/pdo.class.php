<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license GNU General Public License, version 3 (GPL-3.0)
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
use \PDO as DB;

/**
 * @author Marcel
 *
 */
class PDO extends DB {
	/**
	 * {@inheritDoc}
	 * @see PDO::query()
	 */
	public function query(string $statement) {
		$config = config::load("file", core::$corepath . "database.conf.php");
		$statement = str_replace("{prefix}", $config->get("prefix"), $statement);
		echo $statement;
		return parent::query($statement);
	}
}