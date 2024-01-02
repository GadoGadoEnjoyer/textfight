<?php

class Blog_model{
    private $table = 'Blog';
    private $db;

    public function __construct(){
        $this->db = new Database;
    }
    
    public function getBlog($blog_id){
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $this->db->bind('id', $blog_id);
        return $this->db->resultSet();
    }
    public function addBlog($title,$author,$content,$creation){
        $this->db->query('INSERT INTO ' . $this->table . ' (title, author, content, creation) VALUES (:title, :author, :content, :creation)');
    
        // Bind parameters with their values
        $this->db->bind('title', $title);
        $this->db->bind('content', $content);
        $this->db->bind('author', $author);
        $this->db->bind('creation', $creation);
        
        // Execute the INSERT statement
        if ($this->db->execute()) {
            // Insertion was successful
            return true;
        } else {
            // Insertion failed
            return false;
        }
    }
    public function updateBlog($id, $title, $author, $content, $creation){
        $this->db->query('UPDATE ' . $this->table . ' SET title = :title, author = :author, content = :content, creation = :creation WHERE id = :id');
    
        // Bind parameters with their values
        $this->db->bind('id', $id);
        $this->db->bind('title', $title);
        $this->db->bind('content', $content);
        $this->db->bind('author', $author);
        $this->db->bind('creation', $creation);
    
        // Execute the UPDATE statement
        if ($this->db->execute()) {
            // Update was successful
            return true;
        } else {
            // Update failed
            return false;
        }
    }
    

    public function getlastid(){
        return $this->db->getlastid();
    }

    public function getBlogAll(){
        $this->db->query('SELECT * FROM ' . $this->table);
        return $this->db->resultSet();
    }
}

?>