<?php

namespace Salabun\Crafter\Controller;

use Salabun\Crafter\Project\ProjectController;
use Salabun\Crafter\Entity\EntityController;
use Salabun\CodeWriter;

/**
 *  Контроллер для управління контролерами проекту:
 */
class ControllerController extends ProjectController
{ 

    protected $appControllersNamespace = 'AppApi';
    protected $adminControllersNamespace = 'AdminApi';
 
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
 
    /**
     *   Генерую сирці адмінського контролера:
     */
    public function getAdminCrontrollerSourceCode($entity) : string
	{
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line($this->getCopyRights())->br();
        $sourceCode->lines([
            'namespace App\Http\Controllers\\' . $this->getAdminControllersNamespace() . ';',
            '',
            'use App\Http\Controllers\Controller;',
            'use Illuminate\Http\Request;',
            'use Illuminate\Support\Str;',
            'use Illuminate\Support\Facades\Input;',
            'use Auth;',
            'use DB;',
            'use File;',
            'use Log;',
            'use Storage;',
            'use URL;',
            'use Validator;',
            'use Carbon\Carbon;',
        ])->br();
        
        $sourceCode->lines([
            'class ' . $entity . 'Controller extends Controller',
            '{',
        ])->br();

            $sourceCode->defaultSpaces(4);
            $sourceCode->lines([
                '/**',
                ' *  Constructor:',
                ' */',
                'public function __construct()',
                '{',
            ])->defaultSpaces(8);
               
            $sourceCode->line('$this->countPerPage = 10;');
                
            $sourceCode->defaultSpaces(4)->lines([
                '}',
            ]);
        
            // Методи контролера:
            foreach($this->getRouteMethodsTypes() as $method) {

                // Назва методу, який буду генерувати:
                $methodName = 'getAdminMethod' . ucfirst($method['method']);
                
                // Якщо метод існує:
                if(method_exists($this, $methodName)) {
                    $sourceCode->line($this->$methodName($entity, $method))->br();
                } else {
                    
                    $sourceCode->lines([
                        '/**',
                        ' *  Method: ' . $method['type'],
                        ' *  Route: ' . EntityController::pluralize(strtolower($entity)) . $method['postfix'],
                        ' */',
                        'public function '.$method['method'].'()',
                        '{',
                    ])->defaultSpaces(8);
                    
                        // Якщо метод не існує:
                        $sourceCode->line('return response()->json(["Not implemented"], 501);');
                    
                    $sourceCode->defaultSpaces(4)->lines([
                        '}',
                    ])->br();
                    
                }

            }

        $sourceCode->defaultSpaces(0)->br()
        ->line('}');
        
        return $sourceCode->getCode();
    }
    
    /**
     *   Генерую метод list:
     */
    public function getAdminMethodList($entity, $method) : string
	{
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . EntityController::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
            '{',
        ]);
        
            $sourceCode->defaultSpaces(8)->lines([
                '$objects = '.$entity.'::orderBy("id", "desc")->paginate($this->countPerPage);',
                'return response()->json($objects, 200);',
            ]);
        
        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
    }


    
    
    /**
     *   Генерую сирці клієнтського контролера:
     */
    public function getAppCrontrollerSourceCode($entity) : string
	{
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line($this->getCopyRights())->br();
        $sourceCode->lines([
            'namespace App\Http\Controllers\\' . $this->getAdminControllersNamespace() . ';',
            '',
            'use App\Http\Controllers\Controller;',
            'use Illuminate\Http\Request;',
            'use Illuminate\Support\Str;',
            'use Illuminate\Support\Facades\Input;',
            'use Auth;',
            'use DB;',
            'use File;',
            'use Log;',
            'use Storage;',
            'use URL;',
            'use Validator;',
            'use Carbon\Carbon;',
        ])->br();
        
        $sourceCode->lines([
            'class ' . $entity . 'Controller extends Controller',
            '{',
        ])->br();

            $sourceCode->defaultSpaces(4);
            $sourceCode->lines([
                '/**',
                ' *  Constructor:',
                ' */',
                'public function __construct()',
                '{',
            ])->defaultSpaces(8);
               
            $sourceCode->line('$this->countPerPage = 10;');
                
            $sourceCode->defaultSpaces(4)->lines([
                '}',
            ]);
        
            // Методи контролера:
            foreach($this->getRouteMethodsTypes() as $method) {

                // Назва методу, який буду генерувати:
                $methodName = 'getAppMethod' . ucfirst($method['method']);
                
                // Якщо метод існує:
                if(method_exists($this, $methodName)) {
                    $sourceCode->line($this->$methodName($entity, $method))->br();
                } else {
                    
                    $sourceCode->lines([
                        '/**',
                        ' *  Method: ' . $method['type'],
                        ' *  Route: ' . EntityController::pluralize(strtolower($entity)) . $method['postfix'],
                        ' */',
                        'public function '.$method['method'].'()',
                        '{',
                    ])->defaultSpaces(8);
                    
                        // Якщо метод не існує:
                        $sourceCode->line('return response()->json(["Not implemented"], 501);');
                    
                    $sourceCode->defaultSpaces(4)->lines([
                        '}',
                    ])->br();
                    
                }

            }

        $sourceCode->defaultSpaces(0)->br()
        ->line('}');
        
        return $sourceCode->getCode();
    }
    
    /**
     *   Генерую метод list:
     */
    public function getAppMethodList($entity, $method) : string
	{
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . EntityController::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
            '{',
        ]);
        
        var_dump($entity, $this->project['entities'][$entity]['is_personal_data']);
            if($this->project['entities'][$entity]['is_personal_data'] == true) {
                $personalData = 'where("user_id", Auth::user()->id)->';
            } else {
                $personalData = '';
            }
        
            $sourceCode->defaultSpaces(8)->lines([
                '$objects = '.$entity.'::'.$personalData.'orderBy("id", "desc")->paginate($this->countPerPage);',
                'return response()->json($objects, 200);',
            ]);
        
        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
    }
    
    
    
    
    
    
}