<?php

namespace Salabun\Crafter\Model;

use Salabun\Crafter\Route\RouteController;
use Salabun\Crafter\Helpers\Str;
use Salabun\CodeWriter;

/**
 *  Контроллер для управління моделями даних проекту:
 */
class ModelController extends RouteController
{ 
    protected $relationTypes = [
        'hasOne',
        'hasMany',
        'belongsTo',
        'belongsToMany',
    ];
    
    /*
        TODO: з чого складаються моделі Laravel?
        fillable
        table
    */
    public function __construct() 
    {
        
	}
    
    /**
     *   Генерую сирці адмінських моделей:
     */
    public function getAdminModelSourceCode($entity) : string
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line($this->getCopyRights())->br();
        $sourceCode->lines([
            'namespace App\\' . $this->getAdminControllersNamespace() . 'Models;',
            '',
            'use Illuminate\Database\Eloquent\Model;',
            '',
            'class ' . $entity . ' extends Model',
            '{',
        ])->br()->defaultSpaces(4);

        $sourceCode->lines([
            '// protected $connection = "mysql";',
            'protected $table = "' . $this->getEntityTable($entity) . '";',
            '// public $timestamps = false;',
            'protected $fillable = [];',
            'protected $guarded = ["id", "size", "ext"];', // для клієнтських моделей заборонено user_id
        ])->defaultSpaces(4);

        
        // Якщо є зв'язки:
        if(count($this->getEntityRelations($entity)) > 0) {

            // Публікую звязки:
            foreach($this->getEntityRelations($entity) as $relation => $relatedModels) {
                
                foreach($relatedModels as $relatedModel) {

                    if($relation == 'hasOne') {

                        $sourceCode->br()->lines([
                            'public function ' . lcfirst($relatedModel).'() ', // Однина
                            '{',
                        ]);

                        $sourceCode->s(4)->line('return $this->hasOne("App\\' . $this->getAdminControllersNamespace() . 'Models\\'.$relatedModel.'", "id", "' . Str::toSnakeCase($relatedModel) . '_id");');
                        $sourceCode->s(0)->line('}');

                    } else if($relation == 'belongsTo') {

                        $sourceCode->br()->lines([
                            'public function ' . lcfirst($relatedModel).'() ', // Однина
                            '{',
                        ]);

                        $sourceCode->s(4)->line('return $this->belongsTo("App\\' . $this->getAdminControllersNamespace() . 'Models\\'.$relatedModel.'", "' . Str::toSnakeCase($relatedModel) . '_id", "id");');
                        $sourceCode->s(0)->line('}');
                        
                    } else if($relation == 'hasMany') {

                        $sourceCode->br()->lines([
                            'public function ' . lcfirst(Str::pluralize($relatedModel)).'() ', // Множина
                            '{',
                        ]);

                        $sourceCode->s(4)->line('return $this->hasMany("App\\' . $this->getAdminControllersNamespace() . 'Models\\'.$relatedModel.'", "' . Str::toSnakeCase($entity) . '_id", "id");');
                        $sourceCode->s(0)->line('}');
                        
                    } else if($relation == 'belongsToMany') {

                        $pivotTable = null;            
                        $checkPivotTable1 = $this->getEntityTable($relatedModel) . '_' . $this->getEntityTable($entity);            
                        $checkPivotTable2 = $this->getEntityTable($entity) . '_' . $this->getEntityTable($relatedModel);            

                        if(in_array($checkPivotTable1, $this->getTables())) {
                            $pivotTable =$checkPivotTable1;
                        } 

                        if(in_array($checkPivotTable2, $this->getTables())) {
                            $pivotTable = $checkPivotTable2;
                        } 

                        if($pivotTable != null) {

                        $sourceCode->br()->lines([
                            'public function ' . lcfirst(Str::pluralize($relatedModel)).'() ', // Множина
                            '{',
                        ]);

                        $sourceCode->s(4)->line('return $this->belongsToMany("App\\' . $this->getAdminControllersNamespace() . 'Models\\' .$relatedModel . '", "' . $pivotTable . '");');

                        $sourceCode->s(0)->line('}');

                        } else {
                            // TODO: помилка $sourceCode->s(0)->line('// bad relation belongsToMany');
                        }
                        
                        // return $this->belongsToMany('App\Models\Role', 'role_user');
                    }


                    
                }

            }

        }

        // Звязки:
        /*
        public function role() 
        {
            
        }*/


        $sourceCode->defaultSpaces(0);
        $sourceCode->line('');
        $sourceCode->line('}');
        


        return $sourceCode->getCode();
    }
    
    
    /**
     *   Генерую сирці клієнтських моделей:
     */
     public function getAppModelSourceCode($entity) : string
     {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line($this->getCopyRights())->br();
        $sourceCode->lines([
            'namespace App\\' . $this->getAppControllersNamespace() . 'Models;',
            '',
            'use Illuminate\Database\Eloquent\Model;',
        ])->br();

        return $sourceCode->getCode();
     }
    
}