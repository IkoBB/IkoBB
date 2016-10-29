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



// Loading and checking template engine
module::request("template");
module::request("user");
$template = template::get_instance();

// Create 4 new parameters called "content", "title", "sub_title" and "username". They can be accessed with blade syntax with %% content %%, %% title %%, %% sub_title %% and %% username %%.
$template->content = parser::bbCodes("[b]Welcome to the IkoBB demo and testing page[/b]");
$template->title = "IkoBB"; // Default title will be included in the template engine later
$template->sub_title = "Demo & Testing page";
$template->username = "Administrator"; // later the real value will be included in the user/session class. If the user is logged in $template->username = user::username or something like that

// Create a new entity, in this case the sidebar, you can also include params in the array
$template->entity("sidebar",array());

// You can also combine params and entities. Just turn the return value to true which is by default false: entity("entity_name", array(), true);
$template->content .= $template->entity("TEST", array (
	"output" => parser::bbCodes(parser::emoji_to_text($_POST['text'])),
	"code_output" => parser::bbCodes('[code]' . parser::emoji_to_text($_POST['text']) . '[/code]')), true);


// Output the template
echo $template;
