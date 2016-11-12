<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Template>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace Iko;

class template_loader extends \Iko\module_loader
{
	public function __construct($modul)
	{
		parent::__construct($modul);
	}

	protected function pre_check_PDO_Tables()
	{
		$tables = array ("templates");

		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files()
	{
		$files = array (
			"template.class.php",
			"parser.class.php",
			"lib" => array (
				"EmojiOne" => array (
					"autoload.php",
					"src" => array (
						"Client.php",
						"ClientInterface.php",
						"Emojione.php",
						"Ruleset.php",
						"RulesetInterface.php")),
				"GeSHi"    => array (
					"geshi.php")));

		return $this->check_Files($files);
	}

	public function pre_load ()
	{
		$files = array (
			"template.class.php",
			"parser.class.php",
			"lib/EmojiOne/autoload.php",
			"lib/GeSHi/geshi.php",);
		return parent::load($files);
	}
}