<?php
/**
 *
* This file is part of IkoBB Forum and belongs to the module <TestFile>.
*
* @copyright (c) IkoBB <https://www.ikobb.de>
* @license GNU General Public License, version 3 (GPL-3.0)
*
* For full copyright and license information, please see
* the LICENSE file.
*
*/
namespace Iko;

class core_loader extends module_loader {
	public function __construct($modul) {
		parent::__construct($modul);
		echo "Test";
	}
	protected function pre_check_PDO_Tables() {
		$tables = array("{prefix}modules", "{prefix}configs"); //Insert your SQL Tables here. It will load over the Core{prefix} Std is: iko_
		$this->check_PDO_Tables($tables);
	}
	protected function pre_check_Files() {
		$files = array(); //Insert your needed Files here. It will load over the Core.
		$this->check_Files($files);
	}
	public function create_PDO_Tables($args = array(), $file = false) {
		parent::create_PDO_Tables(array("sql/configs.sql", "sql/modules.sql"), true);
	}
	public function load($files = array()) {
		$files = array(
				
		);
		parent::load($files);
	}
}
