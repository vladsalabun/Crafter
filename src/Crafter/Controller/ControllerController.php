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
     *   Генерую сирці головного адмінського контролера:
     */
     public function getAdminMainCrontrollerSourceCode() : string
     {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line($this->getCopyRights())->br();
        $sourceCode->lines([
            'namespace App\Http\Controllers\\' . $this->getAdminControllersNamespace() . ';',
            '',
            'use Illuminate\Foundation\Bus\DispatchesJobs;',
            'use Illuminate\Routing\Controller;',
            'use Illuminate\Foundation\Validation\ValidatesRequests;',
            'use Illuminate\Foundation\Auth\Access\AuthorizesRequests;',
            'use Illuminate\Support\Str;',
            'use Auth;',
            'use DB;',
            'use File;',
            'use Log;',
            'use Storage;',
            'use URL;',
            'use Carbon\Carbon;',
            '',
            'class AdminController extends Controller',
            '{',
            '    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;',
        ])->br();
       

            $sourceCode->defaultSpaces(4);
            $sourceCode->lines([
                'public $response = [',
                '    "code" => 200,',
                '    "status" => "success",',
                '    "message" => "",',
                '];',
                '',
                'public $countPerPage = 10;',
                '',
                '/**',
                ' *  Constructor:',
                ' */',
                'public function __construct()',
                '{',
            ])->defaultSpaces(8);

            $sourceCode->defaultSpaces(4)->lines([
                '}',
            ])->br();
        
            $sourceCode->lines([
                'public function storeFile($file, $path)',
                '{',
                '   $ext = $file->getClientOriginalExtension();',
                '   $fileName = Str::random(20) . "." . $ext;',
                '   Storage::disk("public")->putFileAs($path, $file, $fileName);',
                '',
                '   return [',
                '       "path" => $path,',   
                '       "file" => $fileName,',  
                '       "ext" => $ext,',  
                '       "size" => Storage::disk("public")->size($path . "/" . $fileName),',
                '   ];',
                '}',
            ])->br();

            $sourceCode->lines([
                'public function deleteFile($path)',
                '{',
                '   Storage::disk("public")->delete($path);',
                '}',
            ])->br();        
        
        $sourceCode->defaultSpaces(0)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode(); 
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
            'class ' . $entity . 'Controller extends AdminController',
            '{',
        ])->br();

            $sourceCode->defaultSpaces(4);
            $sourceCode->lines([
                '/**',
                ' *  Constructor:',
                ' */',
                'public function __construct(Request $request)',
                '{',
            ])->defaultSpaces(8);
               
            $sourceCode->line('$this->request = $request;');

            // Важливо! Зберігаю об'єкт для подальшої маніпуляції:
            $sourceCode->line('$this->object = null;');
    

            if($this->isFileEntity($entity)) {   
                $sourceCode->line('$this->path = "storage/' . $this->getEntityTable($entity) . '";');
            }

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
                        ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
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
            $sourceCode->defaultSpaces(4)->line($this->getAdminBeforeCreateRecordMethod($entity))->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminRecordCreateMethod($entity))->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminAfterCreateRecordMethod($entity))->br();

            // Update:
            $sourceCode->defaultSpaces(4)->line($this->getAdminBeforeRecordUpdatedMethod($entity))->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminRecordUpdateMethod($entity))->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminAfterRecordUpdatedMethod($entity))->br();

            // Delete:
            $sourceCode->defaultSpaces(4)->line($this->getAdminBeforeRecordDeletedMethod($entity))->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminRecordDeleteMethod($entity))->br();
            $sourceCode->defaultSpaces(4)->line($this->getAdminAfterRecordDeletedMethod($entity))->br();



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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
            '{',
        ]);
        

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
            - orderBy - тут можливі варіанти, як би з фронту їх задавати? як це можливо?
        */

            $sourceCode->defaultSpaces(8)->lines([
                'if($this->request->has("page")) {',
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'($id)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = ' . $entity . '::where("id", $id)->first();',
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'($id)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = ' . $entity . '::where("id", $id)->first();',
            '',
            'if($this->object != null) {',
            '    ',
            '    // TODO: validation',
            '    $this->beforeUpdateRecord();',
            '    $this->object->fill($this->request->only($this->object->getFillable()));',
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'($id)',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - delete relations (ті звязки що знаходяться нижче)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            '$this->object = ' . $entity . '::where("id", $id)->first();',
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
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
            'if($this->request->isJson()) {',
            '    // TODO: validation',
            '    $requestArray = $this->request->input();',
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
            'if($this->createRecord($this->request->toArray())) {',
            '',
            '    $this->afterCreateRecord();',
            '    $this->response["data"] = $this->object;',
            '    $this->response["message"] = "Record created.";',
            '',
            '    return response()->json($this->response, 200);',
            '}',
            '',
            '$this->response["message"] = "Internal server error. Cannot create record.";',
            'return response()->json($this->response, 500);',
        ]);

        // TODO: if($this->createRecord($this->request->toArray())) { - тут якщо не вдалось створити допис, то треба зловити помилку


        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


    /**
     *   Генерую метод створення обєкту:
     */
     public function getAdminRecordCreateMethod($entity) : string
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
            '$this->object = new ' . $entity . ';',
            '$this->object->fill(',
            '    collect($validatedData)->only(',
            '        $this->object->getFillable()',
            '    )->toArray()',
            ');',
            '',
            'try {',
            '    $this->object->save();',
            '} catch (\Throwable $th) {',
            '    return false;',
            '}',
            '',
            '$this->object->refresh();',
            'return true;',
        ]);

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }


     
    /**
     *   Генерую метод оновлення обєкту:
     */
     public function getAdminRecordUpdateMethod($entity) : string
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
            '$this->object = ' . $entity . '::where("id", $validatedData["id"])->first();',
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            - with relations (я можу пізніше вручну їх додати)
            - where->('deleted', 0) - якщо вказано в налаштуваннях, що таблиця з softDelete
        */

        $sourceCode->defaultSpaces(8)->lines([
            'if(!$this->request->isJson()) {',
            '',
            '    $this->response["status"] = "error";',
            '    $this->response["message"] = "Bad Request. Expects dataType: \"json\".";',
            '',
            '    return response()->json($this->response, 400);',
            '}',
            '',
            '// TODO: validation',
            '$requestArray = $this->request->input();',
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
            ' */',
            'public function '.$method['method'].'()',
            '{',
        ])->defaultSpaces(4); 

        /*
            TODO:
            '    $this->beforeRecordDeleted();',
            '    $this->object->delete();',
            '    $this->afterRecordDeleted();',
        */


        $sourceCode->defaultSpaces(8)->lines([
            'if(!$this->request->isJson()) {',
            '',
            '    $this->response["status"] = "error";',
            '    $this->response["message"] = "Bad Request. Expects dataType: \"json\".";',
            '',
            '    return response()->json($this->response, 400);',
            '}',
            '',
            '// TODO: validation',
            '$requestArray = $this->request->input();',
            '',
            'foreach($requestArray as $validatedData) {',
            '    $deletedId = $this->deleteRecord($validatedData);',
            '    if($deletedId) {',
            '        $this->response["data"][] = $deletedId;',
            '        $this->afterRecordDeleted();',
            '    }',
            '}',
            '',
            '$this->response["message"] = "Records deleted.";',
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
     public function getAdminRecordDeleteMethod($entity) : string
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
            '$this->object = ' . $entity . '::where("id", $validatedData["id"])->first();',
            '',
            'if($this->object == null) {',
            '    return false;',
            '}',
            '',
            '$this->beforeRecordDeleted();',
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
     public function getAdminBeforeRecordDeletedMethod($entity) : string
     {
        $sourceCode = new CodeWriter;
        

        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: beforeRecordDeleted ',
            ' */',
            'public function beforeRecordDeleted()',
            '{',
        ])->defaultSpaces(8); 


        // Якщо ця сутність відповідає за файл, то додаю метод видалення файлу перед видаленням допису:
        if($this->isFileEntity($entity)) {
            $sourceCode->lines([
                '$this->deleteFile($this->object->original);',
            ]);
        }

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує після видалення допису:
     */
     public function getAdminAfterRecordDeletedMethod($entity) : string
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
     public function getAdminBeforeCreateRecordMethod($entity) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: beforeCreateRecord() ',
            ' */',
            'public function beforeCreateRecord()',
            '{',
        ])->defaultSpaces(8); 

        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує після створення допису:
     */
     public function getAdminAfterCreateRecordMethod($entity) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: afterCreateRecord ',
            ' */',
            'public function afterCreateRecord()',
            '{',
        ])->defaultSpaces(8); 

        // Якщо ця сутність відповідає за файл, то додаю метод збереження файлу після створення допису:
        if($this->isFileEntity($entity)) {
            $sourceCode->line('if ($this->request->hasFile("file")) {')->defaultSpaces(12);

            $sourceCode->lines([
                '$savedFile = $this->storeFile($this->request->file("file"), $this->path . "/" . $this->object->user_id);',
                '',
                '$this->object->original = $this->path . "/" . $this->object->user_id . "/" . $savedFile["file"];',
                '$this->object->ext = $savedFile["ext"];',
                '$this->object->size = $savedFile["size"];',
                '$this->object->save();',
            ]);

            if($this->getSingleFileField($entity)) {
                $sourceCode->lines([
                    '',
                    '$duplicates = ' . $entity . '::where("' . $this->getSingleFileField($entity) . '", $this->object->user_id)->where("id", "!=", $this->object->id)->get();',
                    '',
                    'foreach($duplicates as $duplicate) {',
                    '    $this->deleteRecord($duplicate->toArray());',
                    '}',
                ]);
            }

            $sourceCode->defaultSpaces(8)->lines([
                '}',
            ]);

        }




        $sourceCode->defaultSpaces(4)->lines([
            '}',
        ])->br();

        return $sourceCode->getCode();
     }

    /**
     *   Генерую хук який спрацьовує перед оновленням допису:
     */
     public function getAdminBeforeRecordUpdatedMethod($entity) : string
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
     public function getAdminAfterRecordUpdatedMethod($entity) : string
     {
        $sourceCode = new CodeWriter;
        
        $sourceCode->defaultSpaces(4)->lines([
            '/**',
            ' *  Hook: afterUpdateRecord ',
            ' */',
            'public function afterUpdateRecord()',
            '{',
        ])->defaultSpaces(8); 

        // Якщо ця сутність відповідає за файл, то додаю метод збереження файлу після створення допису:
        if($this->isFileEntity($entity)) {
            $sourceCode->line('if ($this->request->hasFile("file")) {')->defaultSpaces(12);

            $sourceCode->lines([
                '$savedFile = $this->storeFile($this->request->file("file"), $this->path . "/" . $this->object->user_id);',
                '',
                '$this->deleteFile($this->object->original);',
                '',
                '$this->object->original = $this->path . "/" . $this->object->user_id . "/" . $savedFile["file"];',
                '$this->object->ext = $savedFile["ext"];',
                '$this->object->size = $savedFile["size"];',
                '$this->object->save();',
            ]);


            $sourceCode->defaultSpaces(8)->lines([
                '}',
            ]);

        }

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
            'class ' . $entity . 'Controller extends AppController',
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
                        ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
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
            ' *  Route: ' . $this->getEntityTable($entity) . $method['postfix'],
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