<?php

namespace Salabun\DataSets;

/**
 *  Дані веб-роутів:
 */
class WebRoutes
{
	public function __construct() 
	{
		$this->crudPrefix = 'CrudWeb';
        
        $this->webRoutes = [
        
            // GET:
            [
                'type' =>'get',
                'name' =>'',
                'method' => 'list', 
            ],
            [
                'type' =>'get',
                'name' =>'/add',
                'method' => 'add', 
            ],
            [
                'type' =>'get',
                'name' =>'/edit/{id}',
                'method' => 'read', 
            ],
            
            // POST:
            [
                'type' =>'post',
                'name' =>'/add',
                'method' => 'create', 
            ],
            [
                'type' =>'post',
                'name' =>'/edit',
                'method' => 'update', 
            ],
        ];
        
    }

}