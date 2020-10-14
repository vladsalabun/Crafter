<?php

namespace Salabun\Crafter\Error;

/**
 *  Контроллер для управління проектом:
 */
class ErrorController
{ 
    protected $project = [];
	protected $entities = [];
	protected $tables = [];
	protected $driver = 'MySQL';
    
    public function __construct() 
	{
	}
}
