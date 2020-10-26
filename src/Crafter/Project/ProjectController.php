<?php

namespace Salabun\Crafter\Project;

use Salabun\Crafter\Error\ErrorController;

/**
 *  Контроллер для управління проектом:
 */
class ProjectController extends ErrorController
{ 
    protected $projectName = null;
    protected $projectDescription = null;
    protected $projectData = [];
    
    public function __construct() 
	{
	}
}

/*
    Наслідування:
    - CopyRightController
    - ErrorController
    - ProjectController
    - ControllerController
    - RouteController
    - ModelController
    - EntityController
    - Crafter

*/