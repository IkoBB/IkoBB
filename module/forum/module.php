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

class forum_module_loader extends \Iko\module_loader // TODO: Create Module
{
	public function __construct ($module)
	{
		parent::__construct($module);
	}

	protected function pre_check_PDO_Tables ()
	{
		$tables = array ();

		return $this->check_PDO_Tables($tables);
	}

	protected function pre_check_Files ()
	{
		$files = array ();

		return $this->check_Files($files);
	}

	public function pre_load ()
	{
		$files = array ();

		return parent::load($files);
	}
}