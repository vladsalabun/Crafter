<?php

namespace Salabun\Crafter\CopyRight;

use Salabun\Crafter\Composer\ComposerController;
use Salabun\CodeWriter;

/**
 *  Контроллер для управління проектом:
 */
class CopyRightController extends ComposerController
{ 
    protected $crafterVersion = 1;
    protected $authorName = 'Vlad Salabun';
    protected $authorEmail = 'vlad@salabun.com';
    protected $authorWebSite = 'https://salabun.com';
    protected $authorTelegram = 'https://t.me/vlad_salabun ';
    
    
    public function __construct() 
	{
	}
    
    /**
     *   Генерую копірайти:
     */
    public function getCopyRights() : string
	{
        $sourceCode = new CodeWriter;
        
        $sourceCode->lines(
            [
                '<?php', 
                '/*', 
                '|--------------------------------------------------------------------------', 
                '|  Programmer: ' . $this->authorName,
                '|  e-mail: ' . $this->authorEmail,
                '|  telegram: ' . $this->authorTelegram,
                '|  web-site: ' . $this->authorWebSite,
                '|--------------------------------------------------------------------------',
                ' */',
            ]
        );
        
        return $sourceCode->getCode();
    }
    
}
