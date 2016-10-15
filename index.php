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

// Create 3 new parameters called content, title and userprofile. They can be accessed with blade syntax with %% content %%, %% title %% and %% userprofile %%.
$template->content = parser::bbCodes("[b]Welcome to the IkoBB demo and testing page[/b]");
$template->title = "IkoBB";
$template->sub_title = "Demo & Testing page";
$template->username = "N8boy";

$template->entity("TEST", array (
	"output" => parser::bbCodes(parser::emoji_to_text($_POST['text'])),
	"code_output" => parser::bbCodes('[code]' . parser::emoji_to_text($_POST['text']) . '[/code]')));


// Output the template
echo $template;
