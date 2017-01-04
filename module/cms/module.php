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
namespace iko\cms;
use iko\Event\Handler;

class template_loader extends \iko\module_loader
{
	public function __construct($modul)
	{
		parent::__construct($modul);

		Handler::add_event('cms', 'iko.cms.register.module', '\iko\cms\page', 'init_page');
	}

	protected function pre_check_PDO_Tables()
	{
		$tables = array ("templates");

		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files()
	{
		$files = array (
			"cms.class.php",
			"page.class.php",
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
			"cms.class.php",
			"page.class.php",
			"template.class.php",
			"parser.class.php",
			"lib/EmojiOne/autoload.php",
			"lib/GeSHi/geshi.php",);
		return parent::load($files);
	}
}