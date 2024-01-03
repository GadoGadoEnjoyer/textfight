<?php 

class User_Model{
    private $table = 'Users';
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function Register($name, $password, $ip_address){
        try {
            $this->db->query('INSERT INTO ' . $this->table . ' (name, password, ip_address) VALUES (:name, :password, :ip_address)');
        
            // Bind parameters with their values
            $this->db->bind('name', $name);
            $this->db->bind('password', $password);
            $this->db->bind('ip_address', $ip_address);

            if(!$this->db->execute()){
                //IDK WHY ITS LIKE THIS DONT ASK
                return true;
            }
            else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function Login($name, $password){
        try {
            $this->db->query('SELECT * FROM ' . $this->table . ' WHERE name = :name AND password = :password');
            $this->db->bind('name', $name);
            $this->db->bind('password', $password);
            $this->db->execute();
            $result = $this->db->resultSingle();
            if($result){
                return true;
            }
            else{
                return false;
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}

?>