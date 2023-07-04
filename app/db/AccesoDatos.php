<?php
class AccesoDatos
{
    private static $objAccesoDatos;
    private $objetoPDO;

    private function __construct()
    {
        try {
            // WebHosting
            $host = 'containers-us-west-197.railway.app';
            $port = 5723;
            $dbname = 'railway';
            $username = 'root';
            $password = 'HmMv6SZMiwcIt6KukPaq';
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
            $this->objetoPDO = new PDO($dsn, $username, $password);

            //Local
            // $this->objetoPDO = new PDO('mysql:host=localhost;dbname=proyectocomanda;charset=utf8;port=3306', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }        
    }

    public static function obtenerInstancia()
    {
        if (!isset(self::$objAccesoDatos)) {
            self::$objAccesoDatos = new AccesoDatos();
        }
        return self::$objAccesoDatos;
    }

    public function prepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    public function obtenerUltimoId()
    {
        return $this->objetoPDO->lastInsertId();
    }

    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }
}