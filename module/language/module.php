<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Language>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace iko\language;

class loader extends \iko\module_loader
{
    public function __construct($modul)
    {
        parent::__construct($modul);
    }

    protected function pre_check_PDO_Tables()
    {

        //Add Tables like User and choosen language assignment and more
        $tables = array(
	        "translation",
	        "users",
        );
	    

	    return $this->check_PDO_Tables($tables);
    }

    protected function pre_check_Files()
    {
        $files = array(// TODO: Add Files
                       "language.class.php",
                       "languageConfigs.class.php",
        );

        return $this->check_Files($files);
    }

	public function pre_load ()
    {
        $files = array(//TODO: Add files to load
                       "language_keys.php",
                       "languageConfigs.class.php",
                       "language.class.php",
        );
        return parent::load($files);
    }
}