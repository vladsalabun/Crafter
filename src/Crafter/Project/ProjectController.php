<?php

namespace Salabun\Crafter\Project;

use Salabun\Crafter\Error\ErrorController;

/**
 *  Контроллер для управління проектом:
 */
class ProjectController extends ErrorController
{ 
    protected $project = [];
	protected $entities = [];
	protected $tables = [];
	protected $driver = 'MySQL';
    
    public function __construct() 
	{
	}
}

/*
    Наслідування:
    - ErrorController
    - ProjectController
    - RouteController
    - ModelController
    - EntityController
    - Crafter

*/