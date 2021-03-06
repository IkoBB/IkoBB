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
namespace example;

class module extends \Iko\module_loader
{
	public function __construct ($module)
	{
		//$this->handler = array($this->get_module()->get_name() , "EVENT_NAME","CLASS WITH NAMESPACE", "FUNCTION", "INSTANCE", "IS_A_STATIC_FUNCTION", "INITALOVER_STATIC");
		parent::__construct($module);
	}

	protected function pre_check_PDO_Tables ()
	{
		$tables = array ( //Insert your SQL Tables here. It will load over the Core{prefix} Std is: iko_
		                  "example",
		                  // For: iko_example
		                  "{prefix}examples",
		                  //For: iko_examples
		                  "{!prefix}myprefix_example"
		                  // For myprefix_example
		);

		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files ()
	{
		$files = array (
			"example.php",
			"example.txt"); //Insert your needed Files here. It will load over the Core.
		return $this->check_Files($files);
	}

	public function pre_load ()
	{
		$files = array (
			"test.php");

		return parent::load($files);
	}
}