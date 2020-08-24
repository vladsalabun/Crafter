<?php

namespace Salabun\Views\Forms;

use Salabun\CodeWriter;
use Salabun\DataSets\DefaultFormsParam;

/**
 *  Приховане поле:
 */
class Hidden
{
	public function __construct() 
	{
        $this->defaultParams = DefaultFormsParam::hidden();
        $this->version = 4;
        $this->prefix = 'control_panel';
        $this->templatesFolder = 'templates';
        $this->params = [];
    }

    /**
     *  Версія форм:
     */
	public function version($string) 
	{
        $this->version = $string;
        return $this;
    }
    
	public function prefix($string) 
	{
        $this->prefix = $string;
        return $this;
    }
    
	public function templatesFolder($string) 
	{
        $this->templatesFolder = $string;
        return $this;
    }
    
    /**
     *  Параметри поля.
     *  Допустимі додаю, зайві відхиляю.
     */
	public function params($array) 
	{
        
        foreach($this->defaultParams['required'] as $requiredParam) {
            if(array_key_exists($requiredParam, $array)) {
                $this->params[$requiredParam] = $array[$requiredParam];
            } else {
                $this->params[$requiredParam] = $this->$requiredParam();
            }
        }
        
        foreach($this->defaultParams['optional'] as $requiredParam) {
            if(array_key_exists($requiredParam, $array)) {
                $this->params[$requiredParam] = $array[$requiredParam];
            }
        }
        
        //$this->validate();
        
        return $this;
    }
    
    public function validate() 
	{
        foreach($this->defaultParams['required'] as $requiredParam) {
            if($this->params[$requiredParam] == null) {
                $this->params[$requiredParam] = $this->$requiredParam();
            }
        }
        //return $this;
    }
    
    public function id() 
	{
        if($this->params['name'] != null) {
            return 'column_' . $this->params['name'];
        } else {
            return 'column_' . rand(1111, 9999);
        }
    }

    public function name() 
	{
        if($this->params['name'] == null) {
            return 'field_' . rand(1111, 9999);
        }
    }
    
    public function value() 
	{
        if($this->params['value'] == null) {
            return '';
        }
    }
    
    public function getHTML() 
	{
        $this->codeWriter = new CodeWriter;
        
        $this
            ->codeWriter
            ->lines([
                '<!---- hidden field: '.$this->params['name'].' ---->',
                '<input type="hidden" name="'.$this->params['name'].'" value="'.$this->params['value'].'" id="'.$this->params['id'].'">',
                '<!---- /hidden field: '.$this->params['name'].' ---->',
             ]);

        return trim($this->codeWriter->getCode());
    }
    
    public function getBlade() 
	{
        $this->codeWriter = new CodeWriter;

        $this
            ->codeWriter
            ->line('@include("'.$this->prefix.'.'.$this->templatesFolder.'.v'.$this->version.'.hidden", $param = [')
            ->s(4)
            ->lines([
                '"name" => "'.$this->params['name'].'",',
                '"value" => '.$this->params['value'].',',
                '"id" => "'.$this->params['id'].'",',
             ])
             ->s(0)
             ->line('])');

        return trim($this->codeWriter->getCode());
    }
    
}