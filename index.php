<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace Iko;

require_once 'core/core.php';
$class = new module("iko");


// Loading and checking template engine
$template_loader = new module("template");
$template_loader->check();
$template_loader->load();
$template = template::get_instance();

// Create 3 new parameters called content, title and userprofile. They can be accessed with blade syntax with %% content %%, %% title %% and %% userprofile %%.
$template->content = parser::bbCodes(":drool:[noparse][b]Test[/b][/noparse] [code] Test
Neue Zeile <br /> [ol=I][*] Test
[*]2. Test
[/ol][/code] :) :D [ol=I][*] Test
[*]2. Test
[/ol]

:O :D :'( <3 :sob:  :bread: :flag_de: :flushed: ");
$template->title = "Index Test title";
$template->userprofile = "This is a user profile. It is only visible if 1=1";


// Output the template
echo $template;
