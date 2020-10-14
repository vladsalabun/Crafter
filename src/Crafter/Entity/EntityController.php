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
            'relations' => []
        ];
        
        $this->project['entities'][$entity]['table'] = $table;
        
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
        
		if(!isset($this->project['entities'][$entity]['relations'][$relation])) {
            $this->project['entities'][$entity]['relations'][$relation] = [];
        }
        
		if(!in_array($relatedEntity, $this->project['entities'][$entity]['relations'][$relation])) {
            $this->project['entities'][$entity]['relations'][$relation][] = $relatedEntity;
        }

        return $this;
        
	}
    
}