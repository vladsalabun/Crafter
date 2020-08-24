<?php

namespace Salabun\DB;

/**
 *  Парсер таблиць і полів MYSQL:
 */
class MySQLParser
{
	protected $host = '127.0.0.1';
	protected $database = 'workout';
	protected $login = 'root';
	protected $password = null;
	protected $connect = null;    

    public function __construct() 
	{
    }

	public function host($host) 
	{
        $this->host = $host;
        return $this;
    }
    
	public function database($database) 
	{
        $this->database = $database;
        return $this;
    }
    
	public function password($password) 
	{
        $this->password = $password;
        return $this;  
    }
    
	public function login($login) 
	{
        $this->login = $login;
        return $this;  
    }

    /**
     *  Підключення до бази даних MYSQL:
     */
	public function connect() 
	{
		$connect = mysqli_connect($this->host, $this->login, $this->password, $this->database);
        
        if($connect) {
            $this->connect = $connect;
            return $this;
        } else {
            return $this;
        }
    }
    
    /**
     *  Отримати назви всіх таблиці:
     */
	public function getTables() 
	{
        $this->tables = [];
        
        $result = mysqli_query($this->connect, "SHOW TABLES");
        
        while($row = mysqli_fetch_array($result)) {
            $this->tables += [$row[0] => []];
        }
        
        return $this;
    }
    
    /**
     *  Отримати назви і параметри полів таблиць:
     */
	public function getFields() 
	{
        if(count($this->tables) > 0) {
            
            foreach($this->tables as $table => $fields) {
                
                $fields = [];
               
                $result = mysqli_query($this->connect, "SHOW COLUMNS FROM " . $table);
               
                if (!$result) {
                    return [
                        'status' => 500,
                        'message' => 'Сталась помилка під чат виконання методу getFields() у класі MySQLParser.'
                    ];
                }
                
                // Якщо є поля:
                if (mysqli_num_rows($result) > 0) {
                    
                    // Зберігаю масиви полів для кожної таблиці:
                    while ($row = mysqli_fetch_assoc($result)) {
                        $fields[] = $row;
                    }
                    
                    $this->tables[$table] = $fields;
                    
                } else {
                    // Якщо полів немає, то масив буде порожнім:
                    $this->tables[$table] = [];
                }
                
            }
            
            return $this;
            
        } else {
            return [
                'status' => 500,
                'message' => 'Немає таблиць. Спершу слід отримати таблиці з допомогою методу getTables()'
            ];
        }        
    }

    
}