<?php

namespace Salabun\Views;

use Salabun\CodeWriter;
use Salabun\Views\Forms\Hidden;

/**
 *  Секції видів:
 */
class ViewFormController
{
	public function __construct() 
	{
        // $this->CodeWriter = new CodeWriter;
    }
    
	public static function hidden() 
	{
        return new Hidden;
    }
    
    
}