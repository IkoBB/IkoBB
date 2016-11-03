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
	protected $final_load = __NAMESPACE__ . "\\User::session";
	private $files = array (
		"user.class.php",
		"group.class.php",
		"permissions" => array (
			"permissions.class.php",
			"value.class.php",
			"group.class.php",
			"user.class.php"));
	public function __construct ($module)
	{
		parent::__construct($module);
	}

	protected function pre_check_PDO_Tables ()
	{
		$tables = array (
			"users",); //Insert your SQL Tables here. It will load over the Core{prefix} Std is: iko_
		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files ()
	{
		return $this->check_Files($this->files);
	}

	public function load ($files = array ())
	{
		return parent::load($this->files);
	}
}