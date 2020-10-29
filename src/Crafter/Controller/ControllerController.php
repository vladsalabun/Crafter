<?php

namespace Salabun\Crafter\Controller;

use Salabun\Crafter\Project\ProjectController;
use Salabun\Crafter\Helpers\Str;
use Salabun\CodeWriter;

/**
 *  Контроллер для управління контролерами проекту:
 */
class ControllerController extends ProjectController
{ 

    /**
     *  Префікси папок:
     */
    protected $appControllersNamespace = 'AppApi';
    protected $adminControllersNamespace = 'AdminApi';
 
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
            '',
            'use App\\' . $this->getAdminControllersNamespace() . 'Models\\'.$entity.';',
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
            $sourceCode->line('$this->response = [')->defaultSpaces(12);
                
                $sourceCode->lines([
                    '"code" => 200,',
                    '"status" => "success",',
                    '"message" => "",'
                ])->defaultSpaces(8);

            $sourceCode->line('];');

            // Важливо! Зберігаю об'єкт для подальшої маніпуляції:
            $sourceCode->line('$this->object = null;');
    
            $sourceCode->defaultSpaces(4)->lines([
                '}',
            ])->br();
        
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
                        ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
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
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'(Request $request)',
            '{',
        ]);
        

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
            - orderBy - тут можливі варіанти, як би з фронту їх задавати? як це можливо?
        */

            $sourceCode->defaultSpaces(8)->lines([
                'if($request->has("page")) {',
                '    $objects = '.$entity.'::paginate($this->countPerPage);',
                '    $this->response["data"] = $objects->items();',
                '    $this->response["total"] = $objects->total();',
                '    $this->response["current_page"] = $objects->currentPage();',
                '    $this->response["last_page"] = $objects->lastPage();',
                '    $this->response["per_page"] = $objects->perPage();',
                '    $this->response["total"] = $objects->total();',
                '',
                '} else {',
                '    $objects = '.$entity.'::all();',
                '    $this->response["data"] = $objects;',
                '}',
                '',
                'return response()->json($this->response, 200);',
            ]);
        
        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
    }

    /**
     *   Генерую метод read:
     */
     public function getAdminMethodRead($entity, $method) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'($id, Request $request)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = Paragraphs::where("id", $id)->first();',
            '',
            'if($this->object != null) {',
            '    $this->response["data"] = $this->object;',
            '    return response()->json($this->response, 200);',
            '}',
            '',
            '$this->response["status"] = "error";',
            '$this->response["message"] = "Record not found.";',
            '',
            'return response()->json($this->response, 404);',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }
    
    /**
     *   Генерую метод update:
     */
     public function getAdminMethodUpdate($entity, $method) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'($id, Request $request)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = Paragraphs::where("id", $id)->first();',
            '',
            'if($this->object != null) {',
            '    $this->response["data"] = $this->object;',
            '    $this->object->fill($request->all());',
            '    $this->object->save();',
            '    $this->response["message"] = "Record updated.";',
            '    return response()->json($this->response, 200);',
            '}',
            '',
            '$this->response["status"] = "error";',
            '$this->response["message"] = "Record not found.";',
            '',
            'return response()->json($this->response, 404);',
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
            'namespace App\Http\Controllers\\' . $this->getAppControllersNamespace() . ';',
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
            '',
            'use App\\' . $this->getAppControllersNamespace() . 'Models\\'.$entity.';',
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
            ])->br();
        
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
                        ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
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
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
            '{',
        ]);
        
            // Захищаю персональні дані:
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