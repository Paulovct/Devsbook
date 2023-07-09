<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;


class AjaxController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() == false){
            header("Content-Type: application/json");
            echo json_encode([ "error" => "Não Autorizado" ]);
            exit;       
        }
    }

    
    public function like($args){
        $id = $args["id"];
        PostHandler::toggleLike($id , $this->loggedUser->id);
        exit;
    }

    public function comment(){
        $array["error"] = "";
        $id = filter_input(INPUT_POST, "id");
        $txt = filter_input(INPUT_POST, "txt");

        if($id && $txt){
            PostHandler::addComment($id , $txt , $this->loggedUser->id);
            $array["link"] = "/perfil/".$this->loggedUser->id;
            $array["avatar"] = "/media/avatars/".$this->loggedUser->avatar;
            $array["name"] = $this->loggedUser->name;
            $array["body"] = $txt;
        }

        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }

    public function addPhoto(){
        $array = ["error" => ""]; 
        if(isset($_FILES['photo']) && !empty($_FILES['photo']["tmp_name"])){
            $photo = $_FILES["photo"];

            $maxWidth = 800;
            $maxHeight = 800;

            if(in_array($photo["type"] , ["image/png" , "image/jpeg" , "image/jpg"])){
                list($widthOrig , $heightOrig) = getimagesize($photo["tmp_name"]);
                $ratio = $widthOrig / $heightOrig;


                $newWidth = $maxWidth;
                $newHeight = $maxHeight;
                $ratioMax = $maxWidth / $maxHeight;

                if($ratioMax > $ratio){
                    $newWidth = $newWidth * $ratio;
                } else {
                    $newHeight = $newHeight / $ratio;
                }

                $finalImage = imagecreatetruecolor($newWidth, $newHeight);
                switch ($photo["type"]) {
                    case 'image/jpeg':
                    case "image/jpg":
                        $image = imagecreatefromjpeg($photo["tmp_name"]);
                    break;
                    case "image/png":
                        $image = imagecreatefrompng($photo["tmp_name"]);
                    break;
                }

                imagecopyresampled(
                    $finalImage,$image,
                    0 , 0 , 0 , 0,
                    $newWidth , $newHeight,
                    $widthOrig , $heightOrig
                );

                $fileName = md5(time().rand(0 , 99999)).".jpg";

                imagejpeg($finalImage , "media/uploads/".$fileName);

                PostHandler::addPost(
                    $this->loggedUser->id,
                    "photo",
                    $fileName
                );

            }
        } else {
            $array["error"] = "Nenhuma Imagem Enviada";
        }


        header("Content-Type: application/json");
        echo json_encode($array);
        exit;
    }
}