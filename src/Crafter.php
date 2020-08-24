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
    
}