<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;


class PostController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() == false){
            $this->redirect("/login");       
        }
    }

    public function new() {
        $body = filter_input(INPUT_POST, "body");

        if($body){
        	PostHandler::addPost(
        		$this->loggedUser->id,
        		"text",
        	 	$body
        	);
        }

        $this->redirect("/");
    }

    public function delete($args){
        $id = $args["id"];

        if($id){
            PostHandler::deletePost($id , $this->loggedUser->id);
        }

        $this->redirect("/");
    }

    
}