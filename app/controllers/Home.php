<?php
class Home extends Controller{
    public function index($room = 0){
        return $this->view('Home/index',$room);
    }
}