<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace iko;

require_once 'core/core.php';


use iko\cms\parser;

$parser = new parser();

// Loading and checking template engine
$template = cms\template::get_instance();

// Create 4 new parameters called "content", "title", "sub_title" and "username". They can be accessed with blade syntax with %% content %%, %% title %%, %% sub_title %% and %% username %%.
$template->content = $parser->parse("[b]Welcome to the IkoBB demo and testing page[/b]");
$template->title = "IkoBB"; // Default title will be included in the template engine later
$template->sub_title = "Demo & Testing page";
$template->username = "Administrator"; // later the real value will be included in the user/session class. If the user is logged in $template->username = user::username or something like that

// Create a new entity, in this case the sidebar, you can also include params in the array
$template->entity("sidebar", array ()); // §§ sidebar §§

// You can also combine params and entities. Just turn the return value to true which is by default false: entity("entity_name", array(), true);

$template->content .= $template->entity("TEST", array (
	"output"      => $parser->parse(define_post("text", "")),
	"code_output" => $parser->parse('[code]' . define_post("text", "") . '[/code]')), TRUE);


//$parser->add_BBCode('[b]','/\[b\](.*?)\[\/b\]/uis','<b>$1</b>');
//$template->content = $parser->bbCodes("[code][b]Welcome to the IkoBB demo and testing page[/b][/code]");


// Output the template
echo $template;
