<?php
namespace Iko;

use \Exception as Exi;

class Exception extends Exi
{
	public function __construct($message = "", $code = 0, $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}