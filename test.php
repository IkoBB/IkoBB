<?php
namespace Iko;
require_once 'core/core.php';

$user = module::request("user", TRUE);
var_dump($user->get_status());
var_dump($user);
print_r($user);
var_dump($user->is_load());
