<?php

namespace Salabun\DataSets;

/**
 *  Параметри полів по замочуванню:
 */
class DefaultFieldsParam
{
	public function __construct() 
	{
        /**
         *  Це перелік типів форм, які я можу генерувати:
         */
        $this->formTypes = 
        [
            'hidden' => [
                // Обовязкові параметри:
                'reqiured' => [
                    'value',
                    'name',
                    'id'
                ],
                // Додаткові параметри:
                'optional' => [
                    'old_value',
                ]
            ],
            'text',
            'number',
            'textarea',
            'radio',
            'select',
            'checkbox',
            'youtube_video',
            'datepicker',
            'tempus_datetimepicker',
            'file',
            'files',
            'image',
            'images',
        ];
        
        $this->formParams = [];
        
        
    }
    
	public static function test() 
	{
        echo 'test';
    }

}