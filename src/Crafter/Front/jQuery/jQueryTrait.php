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

    public function getAjaxCreateSourceCode($entity) : string
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "POST",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '",');   
            $sourceCode->line('data: {')->defaultSpaces(8); 
                $sourceCode->line('// title: "title"')->defaultSpaces(4); 
            $sourceCode->line('}')->defaultSpaces(0);   
            
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("ajax create:", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode(); 
    }
    
    public function getAjaxCreateFileSourceCode($entity) : string
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('var formData = new FormData();');   
        $sourceCode->line('formData.append("file", $("#file").prop("files")[0]);')->br();   

        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "POST",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '",');   
            $sourceCode->line('cache: false,');   
            $sourceCode->line('contentType: false, // important');   
            $sourceCode->line('processData: false, // important');   
            $sourceCode->line('data: formData')->defaultSpaces(0);
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("file upload:", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode(); 
    }

    public function getAjaxUpdateFileSourceCode($entity) : string
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('var formData = new FormData();');   
        $sourceCode->line('formData.append("file", $("#file").prop("files")[0]);');   
        $sourceCode->line('formData.append("_method", "PUT"); // !Important')->br();   

        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "POST",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '",');   
            $sourceCode->line('cache: false,');   
            $sourceCode->line('contentType: false, // important');   
            $sourceCode->line('processData: false, // important');   
            $sourceCode->line('data: formData')->defaultSpaces(0);
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("file upload:", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode(); 
    }

    public function getAjaxReadSourceCode($entity) : string 
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "GET",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '/" + id,');   
            $sourceCode->line('data: {')->defaultSpaces(8); 
                $sourceCode->line('// title: "title"')->defaultSpaces(4); 
            $sourceCode->line('}')->defaultSpaces(0);   
            
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("read", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode();
    }
    
    public function getAjaxUpdateSourceCode($entity) : string 
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "PUT",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '/" + id,');   
            $sourceCode->line('data: {')->defaultSpaces(8); 
                $sourceCode->line('// title: "title"')->defaultSpaces(4); 
            $sourceCode->line('}')->defaultSpaces(0);   
            
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("update:", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode();
    }
   
    public function getAjaxDeleteSourceCode($entity) : string 
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "DELETE",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '/" + id,')->defaultSpaces(0);              
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("deleted:", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode();
    }
  
    public function getAjaxBulkDeleteSourceCode($entity) : string 
    {
        $sourceCode = new CodeWriter;

        // На початку кожного файлу додаю свої копірайти:
        $sourceCode->line('$.ajax({')->defaultSpaces(4);   
            $sourceCode->line('type: "DELETE",');   
            $sourceCode->line('url: SERVER_URL + "/api/' . $this->getEntityTable($entity) . '",');   
            $sourceCode->line('dataType: "json",');     
            $sourceCode->line('contentType: "application/json",');     
            $sourceCode->line('data: JSON.stringify([')->defaultSpaces(8); 
                $sourceCode->line('// { id: 50},')->defaultSpaces(8); 
                $sourceCode->line('// { id: 50},')->defaultSpaces(4); 
            $sourceCode->line('])')->defaultSpaces(0);   
            
        $sourceCode->line('}).done (function (response) {')->defaultSpaces(4);
            $sourceCode->line('console.log("bulk deleted:", response);')->defaultSpaces(0);
        $sourceCode->line('});');

        return $sourceCode->getCode(); 
    }
    
}