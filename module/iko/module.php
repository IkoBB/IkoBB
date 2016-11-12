<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <TestFile>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace Iko;

class core_loader extends module_loader
{
	public function __construct($module)
	{
		parent::__construct($module);
	}

	protected function pre_check_PDO_Tables()
	{
		$tables = array (
			"modules",
			"configs",); //Insert your SQL Tables here. It will load over the Core{prefix} Std is: iko_
		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files()
	{
		$files = array (
			"sql" => array (
				"configs.sql",
				"modules.sql"),
			"admin.class.php",); //Insert your needed Files here. It will load over the Core.
		return $this->check_Files($files);
	}

	public function create_PDO_Tables($args = array (), $file = false)
	{
		return parent::create_PDO_Tables(array (
			"sql/configs.sql",
			"sql/modules.sql"), true);
	}

	public function pre_load ()
	{
		$files = array (
			"admin.class.php",);

		return parent::load($files);
	}
}
