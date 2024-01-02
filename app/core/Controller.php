<?php 

class Controller{
    
    public function view($view,$data = []){
        require_once dirname(__DIR__,1).'/views/'.$view.'.php';
    }

    public function model($model){
        require_once dirname(__DIR__,1).'/models/'.$model.'.php';
        return new $model;
    }
}