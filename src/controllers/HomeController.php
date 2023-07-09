<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class HomeController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() == false){
            $this->redirect("/login");       
        }
    }

    public function index() {

        $page = filter_input(INPUT_GET, "page");


        $feed = PostHandler::getHomeFeed(
            $this->loggedUser->id,
            $page
        );

        $user = UserHandler::getUser($this->loggedUser->id , true); 

        $this->render("home", [
            "loggedUser" => $this->loggedUser,
            "feed" => $feed,
            "user" => $user,
            "data" => $feed
        ]);
    }
}