<?php
session_start();
class Home extends Controller{
    public function index($room = 0){
        if(!isset($_SESSION['name'])){
            header('Location: '.BASEURL.'/Home/login');
        }
        $data = [];
        $data['room'] = $room;
        $data['text'] = "Listen to all the young children from the GDR (East Germany) Boys and girls, all friends of the USSR I want to speak and talk about the organization That should educate and raise our generation Here we're all so free (in the FDJ) Here we're all so German (in the FDJ)";
        $data['name'] = $_SESSION['name'];
        return $this->view('Home/index',$data);
    }
    public function register(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $name = $_POST['name'];
            $password = $_POST['password'];
            $ip_address = $_POST['ip_address'];
            $user = $this->model('User_model');
            if($user->Register($name, $password, $ip_address)){
                header('Location: '.BASEURL.'/Home/login');
            }
            else{
                echo "Something went wrong";
            
            };
        }
        else{
            return $this->view('Home/register');
        }
    }
    public function login(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $name = $_POST['name'];
            $password = $_POST['password'];
            $user = $this->model('User_model');
            if($user->Login($name, $password)){
                $_SESSION['name'] = $name;
                header('Location: '.BASEURL.'/');
            }
            else{
                echo "Something went wrong";
            
            };
        }
        else{
            return $this->view('Home/login');
        }
    }
    public function logout(){
        session_destroy();
        header('Location: '.BASEURL.'/Home/login');
    }
}