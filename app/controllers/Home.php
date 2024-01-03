<?php
class Home extends Controller{
    public function index($room = 0){
        $data = [];
        $data['room'] = $room;
        $data['text'] = "Welcome to the chatroom!";
        return $this->view('Home/index',$data);
    }
}