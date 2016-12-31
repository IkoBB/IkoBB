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
namespace iko\user;

class module_loader extends \iko\module_loader
{
	protected $final_load = __NAMESPACE__ . "\\User::init";
	private $files = array (
		"interfaces" => array ("*",),
		"classes"    => array (
			"operators.php",
			"user.php",
			"group.php",
			"user_profile_fields" => array ("main.php"),
			"permissions"         => array (
				"permissions.php",),
			"*",),);
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

	public function pre_load ()
	{
		return parent::load($this->files);
	}
}