<?php
namespace Yxin{
    class DB{
        private $db;

        public function __construct($host, $port, $dbname, $user, $password){
            $this->db = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
        }

        public function getDb(){
            return $this->db;
        }

        public function query($sql){

            $user = pg_query($this->db, $sql);
            $user = pg_fetch_all($user);
            return $user;
        }

    };

}

/**
 * global namespace
 */
namespace {
    class Fly{
        public function __construct(){
            exit('fly');
        }
    }
}


