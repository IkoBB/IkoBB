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
namespace Iko;

class user_module_loader extends module_loader
{
	public function __construct ($module)
	{
		parent::__construct($module);
	}

	protected function pre_check_PDO_Tables ()
	{
		$tables = array ("{prefix}user");
		$this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files ()
	{
		$files = array (
			"user.class.php",
			"group.class.php",
			"permissions" => array (
				"permissions.class.php",
				"value.class.php",
				"group.class.php",
				"user.class.php"));
		$this->check_Files($files);
	}

	public function load ($files = array ())
	{
		$files = array (
			"user.class.php",
			"group.class.php",
			"permissions" => array(
				"permissions.class.php",
				"value.class.php",
				"user.class.php",
				"group.class.php"
			));
		return parent::load($files);
	}
}