<?php

namespace Salabun;

use Salabun\DataSets\WebRoutes;
use Salabun\DataSets\DefaultFieldsParam;
use Salabun\DB\MySQLParser;
use Salabun\Crafter\Front\FrontController;

/**
 *  Генератор крудів:
 */
class Crafter extends FrontController
{
    
    /**
     *  Скомпільований проект
     *
     *  @var array
     */
	protected $compiled;
    
    public function __construct() 
	{
		// $this->webRoutes = new WebRoutes;
	}
    
    /** 
     *  Проекту:
     */
    public function getProjectData()
	{
		return $this->project;
	}
    
    /** 
     *  Генерація адмінських роутів:
     */
	public function generateAdminRoutes()
	{
		// TODO: 
	}
    
    /** 
     *  Генерація клієнтських роутів:
     */
	public function generateAppRoutes()
	{
		// TODO: 
	}

    /** 
     *  Генерація адмінських видів:
     */
    public function generateAdminViews()
	{
		// TODO:
	}
    
    /** 
     *  Генерація клієнтських видів:
     */
    public function generateAppViews()
	{
		// TODO:
	}
    
    /** 
     *  Генерація адмінських моделей:
     */
    public function generateAdminModels()
	{
		// TODO:
	}
    
    /** 
     *  Генерація клієнтських моделей:
     */
    public function generateAppModels()
	{
		// TODO:
	}
    
    /** 
     *  Генерація адмінських контроллерів:
     */
    public function generateControllers()
	{
		// TODO:
	}
    
    /** 
     *  Генерація клієнтських контроллерів:
     */
    public function generateAppControllers()
	{
		// TODO:
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
    

    
    
}