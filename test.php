<?php
namespace Iko;
use Iko\Permissions\Group as PGroup;
use Iko\Permissions\User as PUser;
require_once 'core/core.php';
module::request("user");
$puser = User::get("MatPlayTV");
$permuser = PUser::get($puser);
print_r($permuser);
