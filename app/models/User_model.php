<?php 
class User_Model{
    private $table = 'Users';
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function Register($name, $password){
        try {
            $this->db->query('INSERT INTO ' . $this->table . ' (name, password, ip_address) VALUES (:name, :password, :ip_address)');
        
            // Bind parameters with their values
            $this->db->bind('name', $name);
            $this->db->bind('password', $password);
            $this->db->bind('ip_address', $_SERVER['REMOTE_ADDR']);

            $this->db->execute();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function Check_IP($ip){
        $this->db->query('SELECT ip_address FROM ' . $this->table);
        $ip_list = $this->db->resultSet();
        if(in_array($ip, $ip_list)){
            return true;
        }
        else{
            return false;
        }
    }
}

?>