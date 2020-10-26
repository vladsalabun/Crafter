<?php

namespace Salabun\Crafter\Route;

use Salabun\Crafter\Controller\ControllerController;
use Salabun\Crafter\Entity\EntityController;
use Salabun\CodeWriter;

/**
 *  Контроллер для управління моделями даних проекту:
 */
class RouteController extends ControllerController
{ 
    
    protected $routeMethodsTypes = [
    
        [
            'method' => 'list',
            'type' => 'GET',
            'route_postfix' => '',
        ],
        [
            'method' => 'create',
            'type' => 'POST',
            'postfix' => '',
        ],
        [
            'method' => 'read',
            'type' => 'GET',
            'postfix' => '/{id}',
        ],
        [
            'method' => 'update',
            'type' => 'PUT',
            'postfix' => '/{id}',
        ],
        [
            'method' => 'patch',
            'type' => 'PATCH',
            'postfix' => '/{id}',
        ],
        [
            'method' => 'delete',
            'type' => 'DELETE',
            'postfix' => '/{id}',
        ],
        [
            'method' => 'bulkUpdate',
            'type' => 'PUT',
            'postfix' => '',
        ],
        [
            'method' => 'bulkPatch',
            'type' => 'PATCH',
            'postfix' => '',
        ],
        [
            'method' => 'bulkDelete',
            'type' => 'DELETE',
            'postfix' => '',
        ],
        [
            'method' => 'search',
            'type' => 'GET',
            'postfix' => '',
        ],
    ];
/*

    
    // users/{id}/images
*/ 
        
    public function __construct() 
	{
        // TODO: авторизація потрібна в роутах?
	}

    /**
     *  Показати усі типи методів:
     */
    public function getRouteMethodsTypes() 
	{
        return $this->routeMethodsTypes;
	}
    
    
    /**
     *   Генерую сирці адмінських роутів:
     */
    public function getAdminApiSourceCode() : string
	{
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line($this->getCopyRights())->br();
        
        foreach($this->project['entities'] as $entity => $param) {
            
            $sourceCode->lines([
                '/**',
                ' *  ' . $entity . ' routes:',
                ' */',
            ]);
            
            // Роути:
            foreach($this->getRouteMethodsTypes() as $method) {
                
                $sourceCode->line("Route::" . strtolower($method['type']) . "('" . EntityController::pluralize(strtolower($entity)) . $method['postfix'] . "', '" . $this->getAdminControllersNamespace() . $entity . "Controller@" . $method['method'] . "');");

            }
            
            // TODO: роути для звязків
            
            $sourceCode->br();
            
        }

        return $sourceCode->getCode();
    }
    
    /**
     *   Згенерувати сирці адмінських роутів:
     */
    public function getAdminEntityRoutes() : string
	{      
        $sourceCode = new CodeWriter;
        
        
        
        $sourceCode->line('Some entity');
        
        return $sourceCode->getCode();
    }
    
    
    
}