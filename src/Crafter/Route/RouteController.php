<?php

namespace Salabun\Crafter\Route;

use Salabun\Crafter\Project\ProjectController;

/**
 *  Контроллер для управління моделями даних проекту:
 */
class RouteController extends ProjectController
{ 
    protected $apiControllersNamespace = 'Api\\';
    protected $webControllersNamespace = '';
    
    protected $routeMethodsTypes = [
        'read'          => 'GET',
        'list'          => 'GET',
        'create'        => 'POST',
        'update'        => 'PUT',
        'delete'        => 'DELETE'
    ];
        
    /*
        TODO: з чого складаються роути Laravel?
        - веб/апі
        - авторизація
    */
    public function __construct() 
	{
        
	}

    /**
     *  Усі типи методів:
     */
    public function getRouteMethodsTypes() 
	{
        return $this->routeMethodsTypes;
	}
    
   

    /**
     *   Згенерувати роути з сутностей:
     */
    public function getRoutesFast() 
	{
       $array = [];
       
       // Беру усі сутності проекту:
       foreach($this->getEntities() as $entity) {
           
            // Генерую роути для сутності:
            foreach($this->getRouteMethodsTypes() as $controllerMethod => $routeMethod) {
                
                // TODO: snake_case
                if($controllerMethod == 'read') {
                    $route = $entity . '/{' . $entity . '_id}';
                } else {
                    $route = $entity;
                }
                
                $array['api'][$entity][] = 
                     'Route::' . strtolower($routeMethod) 
                    . '("' . $entity . '", "' . $this->apiControllersNamespace . $route . 'Controller@' . $controllerMethod . '");';

                $array['web'][$entity][] = 
                     'Route::' . strtolower($routeMethod) 
                    . '("' . $entity . '", "' . $this->webControllersNamespace . $route . 'Controller@' . $controllerMethod . '");';
      
      
            } // кінець геренації роутів для сутності
            
        }
        
       // Беру усі сутності проекту:
       foreach($this->getEntities() as $entity) {
           
            // Генерую роути для стосунків сутності:
            foreach($this->getRouteMethodsTypes() as $controllerMethod => $routeMethod) {
                
                // TODO: генерація роутів для стосунків з іншими моделями
                if(isset($this->project['entities'][$entity]['relations']['hasMany'])) {
                    
                    if(count($this->project['entities'][$entity]['relations']['hasMany']) > 0) {
                        
                        foreach($this->project['entities'][$entity]['relations']['hasMany'] as $relatedEntity) {

                            // TODO: snake_case
                            if($controllerMethod == 'read') {
                                $route = $entity . '/{' . $entity . '_id}/' . $relatedEntity . '/{' . $relatedEntity . '_id}';
                            } else {
                                $route = $entity . '/{' . $entity . '_id}/' . $relatedEntity;
                            }
                            
                            $array['api'][$entity][] = 
                                 'Route::' . strtolower($routeMethod) 
                                . '("' . $route . '", "' . $this->apiControllersNamespace . 'Controller@' . $controllerMethod . '");';
                    
                        }              
      
              
                    } // кінець геренації роутів для стосунків сутності
            
                }
                
            }
            
        }
        
        
        return $array;
        
	}
    
}