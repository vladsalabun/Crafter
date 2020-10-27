<?php

namespace Salabun\Crafter\Project;

use Salabun\Crafter\Error\ErrorController;

/*
    Наслідування:
    - ComposerController
    - CopyRightController
    - ErrorController
    - ProjectController
    - ControllerController
    - RouteController
    - ModelController
    - EntityController
    - Crafter

*/

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
    
    /** 
     *  Дізнатись сутності проекту:
     */
    public function getProject()
	{
		return $this->project;
	}
    
    
}

