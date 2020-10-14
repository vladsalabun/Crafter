<?php

namespace Salabun;

use Salabun\DataSets\WebRoutes;
use Salabun\DataSets\DefaultFieldsParam;
use Salabun\DB\MySQLParser;

/**
 *  Генератор крудів:
 */
class Crafter
{
	protected $project = [];
	public $entities = [];
	protected $driver = 'MySQL';
    
    /**
     *  Скомпільований проект
     *
     *  @var array
     */
	protected $compiled;
    
    public function __construct() 
	{
		$this->webRoutes = new WebRoutes;
	}
    
    /** 
     *  Проекту:
     */
    public function getProjectData()
	{
		return $this->project;
	}
    
    
	public function generateRoutes()
	{
		// TODO: 
        // взяти всі сутності
        // які потрібні веб-роути? назви
        // які потрібні апі-роути? назви
	}
    
    public function generateViews()
	{
		// TODO:
	}
    
    public function generateModels()
	{
		// TODO:
	}
    
    public function generateControllers()
	{
		// TODO:
        
        //
	}
    
    /** 
     *  Визначити драйвер бази даних:
     */
    public function driver($driver = 'MySQL')
	{
		if($driver == 'MySQL') {
            $this->driver = new MySQLParser;
        }
        return $this->driver;
	}
    
    /** 
     *  Додати сутність:
     */
    public function addEntity($entity)
	{
		if(in_array($entity, $this->entities)) {
            // TODO: помилка, сутність вже існує
            
            return $this;
        }
        
        $this->entities[] = $entity;
        $this->project['entities'][$entity] = [
            'relations' => []
        ];
        
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
     *  Додати зв'язок між сутностями:
     */
    public function addRelation($entity, $relation, $relatedEntity)
	{

        if(!in_array($entity, $this->entities) or !in_array($relatedEntity, $this->entities)) {
            // TODO: помилка, такої сутності нема
            return $this;
        }
        
		if(!$relation) {
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