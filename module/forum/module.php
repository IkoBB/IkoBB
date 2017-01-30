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
namespace iko\forum;

use iko\Event\Handler;

class forum_module_loader extends \iko\module_loader // TODO: Create Module
{
	public function __construct ($module)
	{
		parent::__construct($module);
		Handler::add_event('forum', 'iko.cms.register.module', '\iko\forum\cms', 'init_page');
	}

	protected function pre_check_PDO_Tables ()
	{
		$tables = array ();

		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files ()
	{
		$files = array (
			"classes" => array (
				"structure.class.php",
				"board.class.php",
				"category.class.php",
				"cms.class.php",
			),
		);

		return $this->check_Files($files);
	}

	public function pre_load ()
	{
		$files = array (
			"classes" => array (
				"structure.class.php",
				"board.class.php",
				"category.class.php",
				"cms.class.php",

			),
		);

		return parent::load($files);
	}
}