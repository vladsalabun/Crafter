<?php

namespace Salabun\Views;

use Salabun\CodeWriter;

/**
 *  Секції видів:
 */
class ViewBoxController
{
	public function __construct() 
	{
        $this->name = '';
        $this->style = 'brand-green-box';
        $this->bodies = [];
        $this->footers = [];
    }
   
    /**
     *  На вхід потрібно передати css класс боксу:
     *  string $style
     */
	public function style($style) 
	{
        $this->style = $style;
        return $this;
    }
    
    /**
     *  Назва боксу:
     *  string $body
     */
	public function name($string) 
	{
        $this->name = $string;
        return $this;
    }
    
    /**
     *  На вхід потрібно передати HTML код
     *  string $body
     */
	public function addBody($html) 
	{
        $this->bodies[] = $html;
        return $this;
    }
    
    /**
     *  На вхід потрібно передати HTML код
     *  string $body
     */
	public function addFooter($html) 
	{
        $this->footers[] = $html;
        return $this;
    }

    /**
     *  Повертає згенерований HTML боксу:
     */
	public function getHTML()
	{
        $this->CodeWriter = new CodeWriter;
        
        if(strlen($this->name) > 0) {
            $this->CodeWriter->line('<!--- '.$this->name.' box: --->');
        } else {
            $this->CodeWriter->line('<!--- box: --->');
        }

        $this->CodeWriter->line('<div class="bg-white mb-3 p-3 rounded '. $this->style .'">')->defaultSpaces(4);

        // Розбиваю вхідні дані по рядках додаю у боді:  
        foreach($this->bodies as $body) {
            
            $this->CodeWriter
                ->br()
                ->line('<!-- box body: -->')
                ->line('<div class="' . $this->style . '-body">')
                ->br()
                ->s(4)
                    ->lines(explode(PHP_EOL, $body))
                ->s(0)
                ->br()
                ->line('</div>')
                ->line('<!-- /box body -->')
                ->br();
        }
        
        // Розбиваю вхідні дані по рядках додаю у футері:  
        foreach($this->footers as $footer) {
            
            $this->CodeWriter
                ->br()
                ->line('<!-- box footer: -->')
                ->line('<div class="' . $this->style . '-body">')
                ->s(4)
                    ->lines(explode(PHP_EOL, $footer))
                ->s(0)
                ->line('</div>')
                ->line('<!-- /box footer -->')
                ->br();
        }
        
        $this->CodeWriter
            ->defaultSpaces(0)
            ->line('</div>');
            
        
        if(strlen($this->name) > 0) {
            $this->CodeWriter->line('<!--- /'.$this->name.' box --->');
        } else {
            $this->CodeWriter->line('<!--- /box --->');
        }
        
        return trim($this->CodeWriter->getCode());
    }
    
    
    
}