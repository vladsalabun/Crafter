<?php

namespace Salabun\Crafter\Route;

use Salabun\Crafter\Controller\ControllerController;
use Salabun\Crafter\Helpers\Str;
use Salabun\CodeWriter;

/**
 *  Контроллер для управління моделями даних проекту:
 */
class RouteController extends ControllerController
{ 
    
    protected $routeMethodsTypes = [
    
        [
            'method' => 'list', // Готово!
            'type' => 'GET',
            'route_postfix' => '',
        ],
        [
            'method' => 'read',  // Готово!
            'type' => 'GET',
            'route_postfix' => '/{id}',
        ],
        [
            'method' => 'update', // Готово!
            'type' => 'PUT',
            'route_postfix' => '/{id}',
        ],
        [
            'method' => 'patch',
            'type' => 'PATCH',
            'route_postfix' => '/{id}',
        ],
        [
            'method' => 'delete', // Готово!
            'type' => 'DELETE',
            'route_postfix' => '/{id}',
        ],
        [
            'method' => 'create',  // Готово!
            'type' => 'POST',
            'route_postfix' => '',
        ],
        [
            'method' => 'bulkUpdate',  // Готово!
            'type' => 'PUT',
            'route_postfix' => '',
        ],
        [
            'method' => 'bulkPatch',
            'type' => 'PATCH',
            'route_postfix' => '',
        ],
        [
            'method' => 'bulkDelete',  // Готово!
            'type' => 'DELETE',
            'route_postfix' => '',
        ],
        [
            'method' => 'search',
            'type' => 'GET',
            'route_postfix' => '/search',
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
                
                $sourceCode->line("Route::" . strtolower($method['type']) . "('" . $this->getEntityTable($entity) . $method['route_postfix'] . "', '" . $this->getAdminControllersNamespace() . '\\'.$entity . "Controller@" . $method['method'] . "');");

            }
            
            // TODO: роути для звязків
            
            $sourceCode->br();
            
        }

        return $sourceCode->getCode();
    }
    
    
    
}