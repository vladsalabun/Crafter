<?php

namespace Salabun\Views;

use Salabun\CodeWriter;

/**
 *  Секції видів:
 */
class ViewSectionController
{
	public function __construct() 
	{
        $this->sm = 12;
        $this->md = 6;
        $this->lg = 6;
        $this->xl = 6;
        
        $this->items = [];
    }
   
    /**
     *  Задаю ширину всіх стопців:
     */
	public function cols($array) 
	{
        $this
            ->sm($array[0])
            ->md($array[1])
            ->lg($array[2])
            ->xl($array[3]);
        
        return $this;
    }
   
	public function sm($col) 
	{
        $this->sm = $col;
        return $this;
    }
    
	public function md($col) 
	{
        $this->md = $col;
        return $this;
    }
    
	public function lg($col) 
	{
        $this->lg = $col;
        return $this;
    }
    
	public function xl($col) 
	{
        $this->xl = $col;
        return $this;
    }
    
	public function add($item) 
	{
        $this->items[] = $item;
        return $this;
    }
    
	public function getHTML() 
	{
        // Створюю новий обєкт генератора коду:
        $this->codeWriter = new CodeWriter;
        
        $this->codeWriter
            ->line('<!--- Begin bootstrap section: --->')
            ->line('<div class="col-sm-'.$this->sm.' col-md-'.$this->md.' col-lg-'.$this->lg.' col-xl-'.$this->xl.'">')
            ->br()
            ->defaultSpaces(4);

        // Розбиваю вхідні дані по рядках додаю у секцію:    
        foreach($this->items as $item) {
            $this->codeWriter
                ->lines(explode(PHP_EOL, $item))
                ->br();
        }
        
        $this->codeWriter
            ->defaultSpaces(0)
            ->line('</div>')
            ->line('<!--- /End bootstrap section --->');
        
        return $this->codeWriter->getCode();
    }
    
    
    
}