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
namespace Example;

class module extends \Iko\module_loader {
	public function __construct($module) {
		parent::__construct($module);
	}
	protected function pre_check_PDO_Tables() {
		$tables = array("example", "{prefix}example"); //Insert your SQL Tables here. It will load over the Core{prefix} Std is: iko_
		$this->check_PDO_Tables($tables);
	}
	protected function pre_check_Files() {
		$files = array("example.php", "example.txt"); //Insert your needed Files here. It will load over the Core.
		$this->check_Files($files);
	}
	public function load($files = array()) {
		$files = array(
				"test.php"
		);
		parent::load($files);
	}
}