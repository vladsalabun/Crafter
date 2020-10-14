<?php

namespace Salabun\Crafter\Model;

use Salabun\Crafter\Route\RouteController;

/**
 *  Контроллер для управління моделями даних проекту:
 */
class ModelController extends RouteController
{ 
    protected $relationTypes = [
        'hasOne',
        'hasMany',
        'belongsTo',
    ];
    
    /*
        TODO: з чого складаються моделі Laravel?
        fillable
        table
    */
    public function __construct() 
	{
        
	}
    
    
    
}