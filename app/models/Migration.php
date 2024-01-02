<?php 

require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Database.php';

require_once '../core/Constants.php';

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
                `ip_address` VARCHAR(15))
            ');
            $this->db->execute();   
            print_r("User table created\n"); 
    
            $this->db->query('CREATE TABLE Votes (
                `id` INT PRIMARY KEY AUTO_INCREMENT,
                `creator_id` INT,
                `title` varchar(255),
                `body` text,
                `option1` varchar(255),
                `option2` varchar(255),
                `value1` INT,
                `value2` INT,
                `option1_image` varchar(255),
                `option2_image` varchar(255))
            ');
            $this->db->execute();
            print_r("Votes table created\n");
        
            $this->db->query('CREATE TABLE Users_Votes (
                `id` INT PRIMARY KEY AUTO_INCREMENT,
                `user_id` INT,
                `vote_id` INT)
            ');
            $this->db->execute();
            print_r("Users_Votes table created\n");

            $this->db->query('CREATE TABLE Comments (
                `id` INT PRIMARY KEY AUTO_INCREMENT,
                `creator_id` INT,
                `vote_id` INT,
                `body` text)
            ');
            $this->db->execute();  
            print_r("Comments table created\n");

            if($this->tableExist('Users')&&$this->tableExist('Votes')&&$this->tableExist('Users_Votes')&&$this->tableExist('Comments')){
                $this->db->query('ALTER TABLE Votes ADD FOREIGN KEY (creator_id) REFERENCES Users(id) ON DELETE CASCADE');
                $this->db->execute();
                print_r("Foreign key creator_id added to Votes table\n");

                $this->db->query('ALTER TABLE Users_Votes ADD FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE');
                $this->db->execute();
                print_r("Foreign key user_id added to Users_Votes table\n");

                $this->db->query('ALTER TABLE Users_Votes ADD FOREIGN KEY (vote_id) REFERENCES Votes(id) ON DELETE CASCADE');
                $this->db->execute();
                print_r("Foreign key vote_id added to Users_Votes table\n");

                $this->db->query('ALTER TABLE Comments ADD FOREIGN KEY (creator_id) REFERENCES Users(id) ON DELETE CASCADE');
                $this->db->execute();
                print_r("Foreign key creator_id added to Comments table\n");

                $this->db->query('ALTER TABLE Comments ADD FOREIGN KEY (vote_id) REFERENCES Votes(id) ON DELETE CASCADE');
                $this->db->execute();
                print_r("Foreign key vote_id added to Comments table\n");

            }
            
        }
        catch(PDOException $e){
            die($e->getMessage());
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