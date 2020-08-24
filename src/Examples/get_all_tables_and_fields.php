<?php

namespace Salabun;

use Salabun\LaravelCrudGenerator;

/**
 *   Отримати усі таблиці та поля з вказаної бази даних:
 */
 
    header('Content-Type: application/json');
     
    $laravel = new LaravelCrudGenerator();
    $connection = $laravel
                    ->driver('MySQL')
                    ->host('localhost')
                    ->database('wallet_or_life')
                    ->login('root')
                    ->password(''); 

    $database = $connection->connect()->getTables()->getFields();

    echo json_encode($database);