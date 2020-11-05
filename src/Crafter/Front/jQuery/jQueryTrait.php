<?php

namespace Salabun\Crafter\Front\jQuery;

use Salabun\Crafter\Helpers\Str;
use Salabun\CodeWriter;

/**
 *  Контроллер для генерації запитів jQuery:
 */
trait jQueryTrait
{ 
    public $jQuery = 3;

    public function getAjaxCreateSourceCode() 
    {
        // TODO:
        var_dump($this->getEntities());
        return 222;
    }
    
    public function getAjaxReadSourceCode() 
    {
        // TODO:
    }
    
    public function getAjaxUpdateSourceCode() 
    {
        // TODO:
    }
   
    public function getAjaxDeleteSourceCode() 
    {
        // TODO:
    }
  
    public function getAjaxBulkDeleteSourceCode() 
    {
        // TODO:
    }
    
}