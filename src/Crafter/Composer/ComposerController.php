<?php

namespace Salabun\Crafter\Composer;


use Salabun\CodeWriter;

/**
 *  Контроллер для управління пакетами проекту:
 */
class ComposerController
{ 
    // TODO
    /*
        https://github.com/trntv/probe
        https://github.com/jolicode/JoliNotif
    */


    public $packagesLibrary = [
        [
            "vendor" => "arcanedev/log-viewer",
            "version" => "~7.0"
        ],
        [
            "vendor" => "fideloper/proxy",
            "version" => "^4.2"
        ],
        [
            "vendor" => "fruitcake/laravel-cors",
            "version" => "^1.0"
        ],
        [
            "vendor" => "guzzlehttp/guzzle",
            "version" => "^6.3"
        ],
        [
            "vendor" => "intervention/image",
            "version" => "^2.5"
        ],
        [
            "vendor" => "laravel/framework",
            "version" => "^7.0"
        ],
        [
            "vendor" => "laravel/passport",
            "version" => "^9.3"
        ],
        [
            "vendor" => "laravel/tinker",
            "version" => "^2.0"
        ],
        [
            "vendor" => "league/csv",
            "version" => "^9.0"
        ],
        [
            "vendor" => "nelexa/zip",
            "version" => "^3.1"
        ],
        [
            "vendor" => "salabun/telegram-bot-notifier",
            "version" => "^1.06"
        ],
        [
            "vendor" => "spatie/laravel-image-optimizer",
            "version" => "^1.6"
        ],
        [
            "vendor" => "darkaonline/l5-swagger",
            "version" => "^8.0"
        ],  
        [
            "vendor" => "laravel/socialite",
            "version" => "^5.0"
        ],
        [
            "vendor" => "socialiteproviders/apple",
            "version" => "^3.0"
        ],
        [
            "vendor" => "socialiteproviders/facebook",
            "version" => "^1.0"
        ],
        [
            "vendor" => "socialiteproviders/github",
            "version" => "^1.0"
        ],
        [
            "vendor" => "socialiteproviders/google",
            "version" => "^3.1"
        ],
        [
            "vendor" => "socialiteproviders/vkontakte",
            "version" => "^4.1"
        ],
        
    ];
    
    /*
    Команди:
        ^4.2
        ~7.0
    */
}