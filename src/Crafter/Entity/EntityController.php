<?php

namespace Salabun\Crafter\Entity;

use Salabun\Crafter\Model\ModelController;

/**
 *  Контроллер для управління сутностями:
 */
class EntityController extends ModelController
{ 
    public function __construct() 
	{

	}
    

    
    /** 
     *  Додати сутність:
     */
    public function addEntity($entity, $table = null)
	{
        if(in_array($entity, $this->entities)) {
            // TODO: помилка, сутність вже існує
            return $this;
        }
        
        if($table == null) {
            // TODO: якщо таблиці не вказана, то ?
        }
        
        $this->entities[] = $entity;
        $this->tables[] = $table;
        
        $this->project['entities'][$entity] = [
            'relations' => [],
            'is_personal_data' => false,
            'is_file' => false,
            'single_file_by_field' => false,
            'table' => $table
        ];
        
        return $this;
	}

    /** 
     *  Додати зв'язок між сутностями:
     */
    public function addRelation($entity, $relation, $relatedEntity)
	{

        if(!in_array($entity, $this->entities) or !in_array($relatedEntity, $this->entities)) {
            // TODO: помилка, такої сутності нема
            return $this;
        }
        
		if(!in_array($relation, $this->relationTypes)) {
            // TODO: помилка, такого стосунку не існує
            return $this;
        }
        
        // Додаю тип звязку, якщо його ще немає:
		if(!isset($this->project['entities'][$entity]['relations'][$relation])) {
            $this->project['entities'][$entity]['relations'][$relation] = [];
        }
        
        // Такий звязок вже встановлено, то пропускаю:
		if(in_array($relatedEntity, $this->project['entities'][$entity]['relations'][$relation])) {
            return $this;
        }

        $this->project['entities'][$entity]['relations'][$relation][] = $relatedEntity;

        if($relation == 'belongsToMany') {
            $this->addRelation($relatedEntity, $relation, $entity);
        }

        return $this;
        
	}
    
    /** 
     *  Додати захист персональних даних для сутності.
     *  Діє лише для клієнтських контроллерів, бо адмін має доступ до всіх даних.
     */
    public function setAsPersonalData($entity)
	{
        
        if(!in_array($entity, $this->entities)) {
            // TODO: помилка, такої сутності нема
            return $this;
        }

        $this->project['entities'][$entity]['is_personal_data'] = true;

        return $this;
	}
    
    /** 
     *  Дізнатись сутності проекту:
     */
    public function getEntities()
    {
        return $this->entities;
    }
     
    /** 
     *  Дізнатись таблиці проекту:
     */
    public function getTables()
    {
        return $this->tables;
    }

    /** 
     *  Дізнатись таблицю сутності:
     */
    public function getEntityTable($entity)
    {
        return $this->project['entities'][$entity]['table'];
    }

    /** 
     *  Дізнатись зв'язки сутності:
     */
    public function getEntityRelations($entity)
    {
        return $this->project['entities'][$entity]['relations'];
    }

    /** 
     *  Дізнатись чи ця сутність відповідає за файл:
     */
    public function isFileEntity($entity)
    {
        return $this->project['entities'][$entity]['is_file'];
    }
      
    /** 
     *  Позначити, що ця сутність відповідає за файл:
     */
    public function setAsFileEntity($entity, $singleField = null)
    {

        $this->project['entities'][$entity]['is_file'] = true;

        // По цьому полю буде перевірятись чи такий файл існує, наприклад user_id
        // Якщо вже такий user_id у файла є, то він буде видалений а на його місце запишеться новий:
        if($singleField) {
            $this->project['entities'][$entity]['single_file_by_field'] = $singleField;
        }
        return true;
    }

    /** 
     *  Дізнатись по якому полю визначати унікальний файл:
     */
     public function getSingleFileField($entity)
     {
         return $this->project['entities'][$entity]['single_file_by_field'];
     }

}