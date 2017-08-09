<?php
    /**
    * Extension of PDO class
    *
    * This class main purpose is to add support for multiple database connections. 
    * 
    * DB::config('mysql:host=' . USERS__DB_HOST . ';dbname=' . USERS__DB_NAME, USERS__DB_USER, USERS__DB_PASS, 'users');
    * DB::config('mysql:host=' . APP__DB_HOST . ';dbname=' . APP__DB_NAME, APP__DB_USER, APP__DB_PASS, 'app');
    * DB::config_check(NULL,true); // Tests targeted connector (all if none specified)
    * $db = DB::factory('users'); // Returns the database connection specified by the parameter, or last one configured if not specified
    *
    * ORM::set_db(DB::factory('db2')); // Configuration of ORM with a PDO connector
    * 
    * @author Carlos Miranda carlos.miranda_lopez@insa-rouen.fr
    */
    class DB extends PDO
    {
        private static $_code = '__default__';
        public static $debug = NULL;
        public static $handlers = [];
        public $config = [];

        /** 
        * Constructor
        * @param string $dsn DataSource Name
        * @param string $user Database username
        * @param string $pass Database user password
        * @param array<mixed> $driver_options Driver Options
        * @return DB
        */
        public function __construct($dsn, $user = "", $pass = "", $driver_options = []) {
            parent::__construct($dsn, $user, $pass, $driver_options);
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['DBStatement', [$this]]);
        }

        /**
        * Stores a connection profil with $code key.
        *
        * If $code is empty, the profil will become the default profil
        * DB::config('mysql:host=localhost;dbname=test', 'user', 'pass', 'db');
        *
        * @param string $dsn DataSource Name
        * @param string $user Database username
        * @param string $pass Database user password
        * @param string $code Code identifier
        *
        * @return void
        */
        public static function config($dsn, $user, $pass, $code = NULL) {
            if ($code === NULL) {
                $code = self::$_code;
            }
                
            if (!isset(self::$handlers[$code])) {
                if (preg_match('/^([^:]+):(.*)$/', $dsn, $reg)) {
                    self::$handlers[$code]['config'] = [
                        'dsn' => $dsn,
                        'user' => $user,
                        'pass' => $pass,
                        'dsn_connector' => $reg[1],
                        'dsn_config' => $reg[2]
                    ];
                } else {
                    throw new Exception('[DB::Error] Invalid DSN (' . $dsn . ').');
                }
            } else {
                throw new Exception('[DB::Error] Another handler with same name already exists (' . $code . ').');
            }
        }
        
        /**
        * Tests targeted connector (all if none specified)
        *
        * @param string $code Code identifier for the profil
        * @param boolean $debug Debug status
        *
        * @return mixed Returns a boolean if we are not debugging, or an informative array otherwise
        */
        public static function config_check($code = NULL, $debug = false)
        {
            $_debug = [];
            $ok = true;

            if ($code === NULL) {
                foreach (self::$handlers as $code => $handler) {
                    $ok = $ok && self::check($code, false);
                }
            } else {
                $test = self::factory($code);
                $ok = is_object($test);
                if ($ok) {
                    $_debug['ok'] = $code;
                } else {
                    $_debug['fail'] = $code;
                }
            }

            if ($debug) {
                return $_debug;
            }

            return $ok;
        }
        
        /** 
        * Returns the connector targeted by $code
        * 
        * @param string $code Code identifier
        * @param string $charset Character set for the connector
        *
        * @return 
        */
        public static function factory($code = NULL, $charset = "utf8") {
            if ($code === NULL) {
                $code = self::$_code;
            }
            
            if (isset(self::$handlers[$code])) {
                if (isset(self::$handlers[$code]['db'][$charset]) && is_object(self::$handlers[$code]['db'][$charset])) {
                    return self::$handlers[$code]['db'][$charset];
                } else if (is_array(self::$handlers[$code]['config'])) {
                    extract(self::$handlers[$code]['config']);
                    if ($dsn_connector == 'sqlite') {
                        if (!file_exists($dsn_config)) {
                            throw new Exception('[DB::Error] Sqlite database not found (' . $dsn . ')');
                            return false;
                        }
                    }

                    try {
                        self::$handlers[$code]['db'][$charset] = new self($dsn, $user, $pass, [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "' . $charset . '"']);
                        self::$handlers[$code]['db'][$charset]->config = self::$handlers[$code]['config'];
                        return self::$handlers[$code]['db'][$charset];
                    } catch(Exception $e) {
                        throw new Exception('[DB::Error] ' . $e->getMessage() . "\n".'N° : ' . $e->getCode());
                    }
                }
            }
            throw new Exception('[DB::Error] Unable to connect with handler (' . $code . ')');
            return false;
        }

        /**
        * Checks if a table named $table exists
        *
        * @param string $table Name of the table to check
        *
        * @return boolean Whether the table exists or not
        */
        public function table_exists($table) {
            $query = $this->prepare('SHOW TABLES LIKE "' . $table . '"');
            $query->execute();
            return count($query->fetchAll()) == 1;
        }

        /**
        * Method allowing to list all columns of a table, without having to care for the database type (SQLite cannot handle request like SELECT COLUMN_NAME FROM...)
        * 
        * @param string $table_name Name of the table to list columns of
        *
        * @return array<string> An array with columns name as keys and data types as values
        */
        public function list_columns($table_name) {
            $columns = [];
            
            if ($this->config['dsn_connector'] == 'sqlite') {
                $sql = 'pragma table_info(' . $table_name . ');';
                $query = $this->prepare($sql);
                $query->execute();
                while($v = $query->fetch()) {
                    $columns[$v->name] = $v->type;
                }
                return $columns;
            } else {
                $sql = 'SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "' . $table_name . '";';
                $query = $this->prepare($sql);
                $result = $query->execute();
                $query->execute();
                while($v = $query->fetch()) {
                    $columns[$v->COLUMN_NAME] = $v->DATA_TYPE;
                }
                return $columns;
            }
        }
        
        /**
        * PDO prepare method overload
        * Throws an exception if an error occurs
        * 
        * @param string $query The SQL query
        * @param array<mixed> $driver_options Driver Options
        */
        public function prepare($query, $driver_options = [])
        {
            $statement = parent::prepare($query, $driver_options);
            if ($statement === false)
                throw new Exception('[DB::Error] prepare statement failed : ' . $query . '<br><pre>' . print_r($this->errorInfo() , 1).'</pre>');
            return $statement;
        }
        
        /**
        * PDO query method overload
        * 
        * Cette version renvoie une exception si une erreur survient
        * Et effectue un suivi via la classe Debug
        */
        public function query($query,$p1=NULL,$p2=NULL,$p3=NULL)
        {
            if ($p3!==NULL)
                $statement =  parent::query($query,$p1,$p2,$p3);
            else if ($p2!==NULL)
                $statement =  parent::query($query,$p1,$p2);
            else if ($p1!==NULL)
                $statement =  parent::query($query,$p1);
            else
                $statement =  parent::query($query);
                
            if ($statement===false)
                throw new Exception('DB::query() fail : '.$query.'<br><pre>'.print_r($this->errorInfo(),1).'</pre>');
            return $statement;
        }
        
        /**
        * Surcharge de la méthode PDO exec
        * 
        * Cette version renvoi une exception si une erreur survient
        */
        public function exec($query)
        {
            $count =  parent::exec($query);
            if ($count===false)
                throw new Exception('DB::query() fail : '.$query.'<br><pre>'.print_r($this->errorInfo(),1).'</pre>');
            return $count;
        }
    }


    /**
    * Surcharge de PDOStatement utilisé par DB
    *
    * Son principale role et de s'affranchir des problème de type d'objet retourné : PDO::FETCH_OBJ
    * et de fournir un suivi d'execution des requetes via la classe Debug
    */
    class DBStatement extends PDOStatement 
    {
        public $db;
        
        protected function __construct($db) {
            $this->db = $db;
        }
        
        /**
        * Surcharge de la méthode PDO fetch
        */
        function fetch($fetch_style=PDO::FETCH_OBJ,$cursor_orientation=PDO::FETCH_ORI_NEXT,$cursor_offset=0)
        {
            return parent::fetch($fetch_style,$cursor_orientation,$cursor_offset);
        }
        
        /**
        * Surcharge de la méthode PDO fetchAll
        */
        function fetchAll($fetch_style=PDO::FETCH_OBJ,$fetch_argument=NULL, $ctor_args=NULL)
        {
            if ($ctor_args!==NULL)
                return parent::fetchAll($fetch_style,$fetch_argument,$ctor_args);
            else if ($fetch_argument!==NULL)
                return parent::fetchAll($fetch_style,$fetch_argument);
            else
                return parent::fetchAll($fetch_style);
        }
        
        /**
        * Surcharge de la méthode PDO execute
        * 
        * Cette version renvoi une exception si une erreur survient
        * Et effectue un suivi via la classe Debug
        */
        function execute($values=NULL)
        {
            $_v = $values;
            $query = $this->queryString;
            
            if (is_object($_v))
            {
                $__v = array();
                foreach($_v as $k=>$v)
                    $__v[$k] = $v;
                $_v = $__v;
            }
            if (is_array($_v))
            {
                foreach($_v as $k=>$v)
                {
                    if (is_string($k))
                    {
                        $query = preg_replace('/:'.$k.'[^[:alnum:]_]/',$v.' ',$query);
                        unset($_v[$k]);
                    }
                }
                $query = vsprintf(str_replace('?','"%s"',str_replace('%','%%',$query)),$_v);
            }

            $success = parent::execute($values);
            
            if ($success===false)
                throw new Exception('DBStatement::execute() statement fail : <br><pre>'.$query.'</pre><br><pre>'.print_r($this->errorInfo(),1).'</pre>');
        
            return $success;
        }
    }
?>