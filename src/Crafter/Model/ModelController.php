<?php

namespace Salabun\Crafter\Model;

use Salabun\Crafter\Route\RouteController;
use Salabun\Crafter\Entity\EntityController;
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
        ])->defaultSpaces(4);

        
        // Якщо є зв'язки:
        if(count($this->getEntityRelations($entity)) > 0) {

            // Публікую звязки:
            foreach($this->getEntityRelations($entity) as $relation => $relatedModels) {
                
                foreach($relatedModels as $relatedModel) {

                    // HasOne:
                    if($relation == 'hasOne') {

                        $sourceCode->br()->lines([
                            'public function ' . lcfirst(EntityController::pluralize($relatedModel)).'() ',
                            '{',
                        ]);

                        $sourceCode->s(4)->line('return $this->hasOne("App\Role", "' . strtolower($relatedModel) . '_id", "id");');
                        $sourceCode->s(0)->line('}');

                    } else if($relation == 'hasMany') {
                        // TODO: 
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