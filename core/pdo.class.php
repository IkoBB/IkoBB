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

use \PDO as DB;

/**
 * @author Marcel
 *
 */
class PDO extends DB
{

	public function query ($statement, $mode = DB::ATTR_DEFAULT_FETCH_MODE, $arg3 = NULL, array $ctorargs = array ())
	{
		$statement = $this->convert($statement);
		return parent::query($statement, $mode, $arg3, $ctorargs);
	}

	public function prepare ($statement, array $driver_options = array ())
	{
		$statement = $this->convert($statement);

		return parent::prepare($statement, $driver_options);
	}

	public function exec ($statement)
	{
		$statement = $this->convert($statement);

		return parent::exec($statement);
	}

	public function convert ($statement)
	{
		$config = config::load("file", core::$corepath . "database.conf.php");

		return str_replace("{prefix}", $config->get("prefix"), $statement);
	}
}