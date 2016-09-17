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
namespace TestFile;

class module extends \Iko\module_loader {
	public function __construct($modul) {
		parent::__construct($modul);
	}
	protected function pre_check_PDO_Tables() {
		$tables = array("{prefix}user", "{prefix}user_groups");
		$this->check_PDO_Tables($tables);
	}
	protected function pre_check_Files() {
		$files = array("test.php", "weiteres.txt");
		$this->check_Files($files);
	}
	public function load() {
		$files = array(
				"test.php"
		);
		parent::load($files);
	}
}