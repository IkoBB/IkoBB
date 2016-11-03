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

namespace Iko;

class language_loader extends module_loader
{
    public function __construct($modul)
    {
        parent::__construct($modul);
    }

    protected function pre_check_PDO_Tables()
    {

        //Add Tables like User and choosen language assignment and more
        $tables = array(
            "language",
            "translation",
            "language_assignment"
        );

        return $this->check_PDO_Tables($tables);
    }

    protected function pre_check_Files()
    {
        $files = array(// TODO: Add Files
        );

        return $this->check_Files($files);
    }

    public function load($files = array())
    {
        $files = array(//TODO: Add files to load
        );
        return parent::load($files);
    }
}