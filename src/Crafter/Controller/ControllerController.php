<?php

namespace Salabun\Crafter\Controller;

use Salabun\Crafter\Project\ProjectController;

/**
 *  Контроллер для управління контролерами проекту:
 */
class ControllerController extends ProjectController
{ 

    protected $appControllersNamespace = 'AppApi\\';
    protected $adminControllersNamespace = 'AdminApi\\';
 
/*
    protected $routeMethodsTypes = [
        'create'        => 'POST', //single/bulk
        'read'          => 'GET',
        'update'        => 'PUT', // оновлення цілого обєкту, а якщо не передадуть всі поля? //single/bulk
        'delete'        => 'DELETE',
        'bulkCreate'    => 'POST', //single/bulk
        'list'          => 'GET',
        'bulkUpdate'    => 'PUT', // оновлення цілого обєкту, а якщо не передадуть всі поля? //single/bulk
        'bulkDelete'    => 'DELETE',
        
        // Оновлення частини обєкту:
        'patch'         => 'PATCH',
        'bulkPatch'     => 'PATCH',
        
    ];
*/
 
 
    public function getAppControllersNamespace() 
	{
        return $this->appControllersNamespace;
    }
    
    public function getAdminControllersNamespace() 
	{
        return $this->adminControllersNamespace;
    }
 
    public function setAppControllersNamespace($string) 
	{
        $this->appControllersNamespace = $string;
        return $this;
    }
    
    public function setAdminControllersNamespace($string) 
	{
        $this->adminControllersNamespace = $string;
        return $this;
    }
 
}