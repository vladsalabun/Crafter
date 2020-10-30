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

/*
    TODO: 
    - Помістити унаслідувати адмінські і клієнтські контроллери, бо тут вже забагато їх
    - Зробити ендпоінти для меню (розрахунок кількості дописів з кешем, щоб фоном перераховувалось) 
    - Перенести в свій голвний контроллер ($this->response) який наслідує основний контроллер 
*/




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


            // Create:
            $sourceCode->defaultSpaces(4)->line($this->getAdminBeforeCreateRecordMethod())->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminRecordCreateMethod())->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminAfterCreateRecordMethod())->br();

            // Update:
            $sourceCode->defaultSpaces(4)->line($this->getAdminBeforeRecordUpdatedMethod())->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminRecordUpdateMethod())->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminAfterRecordUpdatedMethod())->br();

            // Delete:
            $sourceCode->defaultSpaces(4)->line($this->getAdminBeforeRecordDeletedMethod())->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminRecordDeleteMethod())->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminAfterRecordDeletedMethod())->br();



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
            '    ',
            '    // TODO: validation',
            '    $this->beforeUpdateRecord();',
            '    $this->object->fill($request->only($this->object->getFillable()));',
            '    $this->object->save();',
            '    $this->afterUpdateRecord();',
            '    $this->response["data"] = $this->object;',
            '    $this->response["message"] = "Record updated.";',
            '    ',
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
     *   Генерую метод delete:
     */
     public function getAdminMethodDelete($entity, $method) : string
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
            - delete relations (ті звязки що знаходяться нижче)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = Paragraphs::where("id", $id)->first();',
            '',
            'if($this->object != null) {',
            '    $this->beforeRecordDeleted();',
            '    $this->object->delete();',
            '    $this->afterRecordDeleted();',
            '    $this->response["message"] = "Record deleted.";',
            '    ',
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
     *   Генерую метод create:
     */
     public function getAdminMethodCreate($entity, $method) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'(Request $request)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - validation - як валідувати для масових запитів?
            - try-catch
            - single/bulk
        */

        $sourceCode->defaultSpaces(8)->lines([
            'if($request->isJson()) {',
            '    // TODO: validation',
            '    $requestArray = $request->input();',
            '',
            '    foreach($requestArray as $validatedData) {',
            '        $this->beforeCreateRecord();',
            '        $this->createRecord($validatedData);',
            '        $this->afterCreateRecord();',
            '        $this->response["data"][] = $this->object;',
            '    }',
            '',
            '    $this->response["message"] = "Records created.";',
            '',
            '    return response()->json($this->response, 200);',
            '}',
            '',
            '// TODO: validation',
            '$this->beforeCreateRecord();',
            '$this->createRecord($request->toArray());',
            '$this->afterCreateRecord();',
            '$this->response["data"] = $this->object;',
            '$this->response["message"] = "Record created.";',
            '',
            'return response()->json($this->response, 200);',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


    /**
     *   Генерую метод створення обєкту:
     */
     public function getAdminRecordCreateMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Create record:',
            ' */',
            'public function createRecord($validatedData)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = new Paragraphs;',
            '$this->object->fill(',
            '    collect($validatedData)->only(',
            '        $this->object->getFillable()',
            '    )->toArray()',
            ');',
            '',
            '$this->object->save();',
            '$this->object->refresh();',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


     
    /**
     *   Генерую метод оновлення обєкту:
     */
     public function getAdminRecordUpdateMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Update record:',
            ' */',
            'public function updateRecord($validatedData)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = Paragraphs::where("id", $validatedData["id"])->first();',
            '',
            'if($this->object == null) {',
            '    return false;',
            '}',
            '',
            '$this->object->fill(',
            '    collect($validatedData)->only(',
            '        $this->object->getFillable()',
            '    )->toArray()',
            ');',
            '',
            '$this->object->save();',
            '$this->object->refresh();',
            '' ,   
            'return true;',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую метод bulk update:
     */
     public function getAdminMethodBulkUpdate($entity, $method) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'(Request $request)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            'if(!$request->isJson()) {',
            '',
            '    $this->response["status"] = "error";',
            '    $this->response["message"] = "Bad Request. Expects dataType: \"json\".";',
            '',
            '    return response()->json($this->response, 400);',
            '}',
            '',
            '// TODO: validation',
            '$requestArray = $request->input();',
            '',
            'foreach($requestArray as $validatedData) {',
            '    if($this->updateRecord($validatedData)) {',
            '        $this->beforeUpdateRecord();',
            '        $this->response["data"][] = $this->object;',
            '        $this->afterUpdateRecord();',
            '    }',
            '}',
            '',
            '$this->response["message"] = "Records updated.";',
            '',
            'return response()->json($this->response, 200);',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


    /**
     *   Генерую метод bulk delete:
     */
     public function getAdminMethodBulkDelete($entity, $method) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Method: ' . $method['type'],
            ' *  Route: ' . Str::pluralize(strtolower($entity)) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'(Request $request)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            '    $this->beforeRecordDeleted();',
            '    $this->object->delete();',
            '    $this->afterRecordDeleted();',
        */


        $sourceCode->defaultSpaces(8)->lines([
            'if(!$request->isJson()) {',
            '',
            '    $this->response["status"] = "error";',
            '    $this->response["message"] = "Bad Request. Expects dataType: \"json\".";',
            '',
            '    return response()->json($this->response, 400);',
            '}',
            '',
            '// TODO: validation',
            '$requestArray = $request->input();',
            '',
            'foreach($requestArray as $validatedData) {',
            '    $deletedId = $this->deleteRecord($validatedData)',
            '    if($id) {',
            '        $this->beforeRecordDeleted();',
            '        $this->response["data"][] = $deletedId;',
            '        $this->afterRecordDeleted();',
            '    }',
            '}',
            '',
            '$this->response["message"] = "Records updated.";',
            '',
            'return response()->json($this->response, 200);',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


    /**
     *   Генерую метод видалення обєкту:
     */
     public function getAdminRecordDeleteMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Delete record:',
            ' */',
            'public function deleteRecord($validatedData)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = Paragraphs::where("id", $validatedData["id"])->first();',
            '',
            'if($this->object == null) {',
            '    return false;',
            '}',
            '',
            '$deletedId = $this->object->id;',
            '$this->object->delete();',
            '' ,   
            'return $deletedId;',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


    /**
     *   Генерую хук який спрацьовує перед видаленням допису:
     */
     public function getAdminBeforeRecordDeletedMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: beforeRecordDeleted ',
            ' */',
            'public function beforeRecordDeleted()',
            '{',
        ])->defaultSpaces(4); 

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує після видалення допису:
     */
     public function getAdminAfterRecordDeletedMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: afterRecordDeleted ',
            ' */',
            'public function afterRecordDeleted()',
            '{',
        ])->defaultSpaces(4); 

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


    /**
     *   Генерую хук який спрацьовує перед створенням допису:
     */
     public function getAdminBeforeCreateRecordMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: beforeCreateRecord() ',
            ' */',
            'public function beforeCreateRecord()',
            '{',
        ])->defaultSpaces(4); 

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує після створення допису:
     */
     public function getAdminAfterCreateRecordMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: afterCreateRecord ',
            ' */',
            'public function afterCreateRecord()',
            '{',
        ])->defaultSpaces(4); 

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує перед оновленням допису:
     */
     public function getAdminBeforeRecordUpdatedMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: beforeUpdateRecord ',
            ' */',
            'public function beforeUpdateRecord()',
            '{',
        ])->defaultSpaces(4); 

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує після видалення допису:
     */
     public function getAdminAfterRecordUpdatedMethod() : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: afterUpdateRecord ',
            ' */',
            'public function afterUpdateRecord()',
            '{',
        ])->defaultSpaces(4); 

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