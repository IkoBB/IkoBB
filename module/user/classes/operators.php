<?php
/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 04.11.2016
 * Time: 22:00
 */
namespace iko\user;

use iko\lib\multiton\cache_int;

abstract class operators extends cache_int
{
	protected $id;

	public function get_id (): int
	{
		return $this->id;
	}
	protected $permission_class = NULL;

	public function get_permission_class (): Permissions
	{
		if ($this->permission_class == NULL) {
			$this->permission_class = Permissions::get($this);
		}
		return $this->permission_class;
	}

	public function has_permission ($permission, $args = NULL, $pre = NULL)
	{
		return $this->get_permission_class()->has_permission($permission);
	}

	public function add_permission ($permission)
	{
		return $this->get_permission_class()->add_permission($permission);
	}

	public function remove_permission ($permission)
	{
		return $this->get_permission_class()->remove_permission($permission);
	}
}