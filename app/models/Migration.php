<?php 
require_once dirname(__DIR__).'/core/Controller.php';
require_once dirname(__DIR__).'/core/Database.php';


class Migration{
    private $db;
    public function __construct(){
        print_r("Migration constructor called\n");
        $this->db = new Database;
        try{

            $this->db->query('CREATE TABLE Users (
                `id` INT PRIMARY KEY AUTO_INCREMENT,
                `name` VARCHAR(50) UNIQUE,
                `password` VARCHAR(255),
                `ip_address` VARCHAR(15) UNIQUE)
            ');
            $this->db->execute();   
            print_r("User table created\n"); 
      
        }catch(PDOException $e){
            print_r($e);
        }
    }

    public function tableExist($table){
        $this->db->query('SHOW TABLES LIKE :table');
        $this->db->bind(':table', $table);
        if($this->db->rowcount() > 0){
            return true;
        }else{
            return false;
        }
    }
    
}

$migration = new Migration();