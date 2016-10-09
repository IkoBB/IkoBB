<?php
namespace Iko;
require_once 'core/core.php';
module::request("user");
$config = config::load("pdo", "iko");
$value = new config_value(array (
		"value"       => "haha",
		"name"        => "my_settings",
		"module_name" => "iko",
		"class_main"  => $config
		//This array value is very Important to add the new entrie into the Table
	));
$value->add();
echo $config->get("my_settings")->get();
