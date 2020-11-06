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
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "POST",');   
            $sourceCode->line('url: SERVER_URL + "/api/paragraphs",');   
            $sourceCode->line('data: {'); 

            
            $sourceCode->line('}')->defaultSpaces(0);   
            
    $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("ajax create:", response);')->defaultSpaces(0);
        $sourceCode->line('});');
/*
    
    
        id: 111111,
        text: '$flight->refresh();',
        order_id: 'Кеды',
        data: 'Кеды'

})
*/

        return $sourceCode->getCode(); 
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