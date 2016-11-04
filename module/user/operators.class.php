<?php
/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 04.11.2016
 * Time: 22:00
 */
namespace Iko;

abstract class operators
{
	protected $permission_class = NULL;

	public function get_permission_class ()
	{
		if ($this->permission_class == NULL) {
			$this->permission_class = Permissions::get($this);
		}

		return $this->permission_class;
	}

	public function has_permission ($permission)
	{
		return $this->get_permission_class()->has_permission($permission);
	}
}