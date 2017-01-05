<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <CMS>.
 *
 * @copyright (c) 2017 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace iko\cms;

use iko\Event\Handler;
use iko\module;
use iko\config;

/**
 * Class cms
 * @package iko\cms
 */
class cms
{

	/**
	 * Constructs a page and sends the $_GET variable to a module which will handle the output
	 *
	 * @param array $args
	 */
	function __construct ($args = array ())
	{
		$config_iko = config::load("pdo", "iko");
		$template = template::get_instance();
		$template->title = $config_iko->site_name;

		if (array_key_exists('module', $args) && $args['module'] != 'debug') {
			if (module::exist($args['module'])) {
				/**
				 * Please add to the following function to your module if your module shall have an output:
				 * Handler::add_event(YOUR_MODULE_NAME, 'iko.cms.register.module', YOUR_CLASS_NAME, YOUR_OUTPUT_FUNCTION);
				 *
				 * Replace all uppercase text to your strings.
				 * You can find an example in this module. Take a look in the module.php and in the page.class.php file at the function init_page()
				 *
				 * You have to include in your class also an output function. In this output function you will have as input the $_GET variable.
				 * Please check if the input is the correct data type.
				 */
				if(Handler::isset_event_module ('iko.cms.register.module', $args['module'])) {
					Handler::event_module('iko.cms.register.module', $args['module'], $args);
				}
				else {
					$page = new page();
					$page->init_page(NULL, array ('id' => 0));
				}

			}
			else {
				$page = new page();
				$page->init_page(NULL, array ('id' => 0));
			}
		}
		elseif ($args['module'] == 'debug') {
			$parser = new parser();
			$template->sub_title = "Demo & Testing page";
			$template->content = $parser->parse("[b]Welcome to the IkoBB demo and testing page[/b]");
			$template->content .= $template->entity("TEST", array (
				"output"      => $parser->parse(\iko\define_post("text", "")),
				"code_output" => $parser->parse('[code]' . \iko\define_post("text", "") . '[/code]')), TRUE);
			echo $template;
		}
		else {
			// Default page when no module is defined
			// ToDo: Define how the default page is set and how it will be handled
			$template->sub_title = 'Default page';
			$template->content = 'Default page';
			echo $template;
		}
	}
}