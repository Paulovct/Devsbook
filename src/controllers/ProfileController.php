<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        if(UserHandler::checkLogin() == false){
            $this->redirect("/login");       
        }
    }

    public function index($args = []) {
        $page = filter_input(INPUT_GET, "page");

        //detectando usuario
        $id = $this->loggedUser->id;
        if(!empty($args["id"])){
            $id = $args["id"];
        }
        $user = UserHandler::getUser($id , true);
        if(!$user){
            $this->redirect("/");
        }

        $dateFrom = new \DateTime($user->birthDate);
        $dateTo = new \DateTime("today");
        $user->ageYears= $dateFrom->diff($dateTo)->y;

        //user feed
        $feed = PostHandler::getUserFeed($id , $page , $this->loggedUser->id);


        //follow verify
        $isFollowing = false;
        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id , $user->id);
        }


        $this->render("profile" ,[
            "loggedUser" => $this->loggedUser,
            "user" => $user,
            "feed" => $feed,
            "following" => $isFollowing,
        ]);
    }

    public function follow($args){
        $to = intval($args["id"]);

        $exists = UserHandler::idExists($to);

        if($exists){

            if(UserHandler::isFollowing($this->loggedUser->id , $to)){
                UserHandler::unfollow($this->loggedUser->id , $to);
            } else {
                UserHandler::follow($this->loggedUser->id , $to);
            }

        }

        $this->redirect("/perfil/".$to);

    }

    public function friends($args = []){
        //detectando usuario
        $id = $this->loggedUser->id;
        if(!empty($args["id"])){
            $id = $args["id"];
        }
        $user = UserHandler::getUser($id , true);
        if(!$user){
            $this->redirect("/");
        }

        $dateFrom = new \DateTime($user->birthDate);
        $dateTo = new \DateTime("today");
        $user->ageYears= $dateFrom->diff($dateTo)->y;
        
        $isFollowing = false;
        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id , $user->id);
        }

        $this->render("profile_friends" , [
            "loggedUser" => $this->loggedUser,
            "user" => $user ,
            "following" => $isFollowing,

        ]);   
    }
    public function photos($args = []){
        //detectando usuario
        $id = $this->loggedUser->id;
        if(!empty($args["id"])){
            $id = $args["id"];
        }
        $user = UserHandler::getUser($id , true);
        if(!$user){
            $this->redirect("/");
        }

        $dateFrom = new \DateTime($user->birthDate);
        $dateTo = new \DateTime("today");
        $user->ageYears= $dateFrom->diff($dateTo)->y;
        
        $isFollowing = false;
        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id , $user->id);
        }

        $this->render("profile_photos" , [
            "loggedUser" => $this->loggedUser,
            "user" => $user ,
            "following" => $isFollowing,
            
        ]);   
    }

    public function config(){
        $flash = "";
        if(!empty($_SESSION['flash'])){
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = "";
        }
        $user = UserHandler::getUser($this->loggedUser->id , true);
        $this->render("config" , ["loggedUser" => $user , "flash" => $flash]);
    }

    public function configAction(){
        $name = filter_input(INPUT_POST, "name");
        $email = filter_input(INPUT_POST, "email" , FILTER_VALIDATE_EMAIL);
        $birthDate = filter_input(INPUT_POST, "birthdate");
        $password = filter_input(INPUT_POST, "password");
        $newPassword = filter_input(INPUT_POST, "newpassword");
        $newPasswordConfirm = filter_input(INPUT_POST, "newpasswordconfirm");
        $work = filter_input(INPUT_POST, "work");
        $city = filter_input(INPUT_POST, "city");

        $updateFields = [];

        $updateFields["city"] = $this->loggedUser->city;
        $updateFields["work"] = $this->loggedUser->work;
        $updateFields["avatar"] = $this->loggedUser->avatar;
        $updateFields["cover"] = $this->loggedUser->cover;
        
        //name
        if(empty($name)){
            $_SESSION['flash'] = "Nome Obrigatorio.";
            $this->redirect("/config");
        }
        $updateFields["name"] = $name;


        //email
        if(empty($email)){
            $_SESSION['flash'] = "E-mail Obrigatorio.";
            $this->redirect("/config");   
        }
        $exixts = UserHandler::emailExists($email);
        if($exixts && $email != $this->loggedUser->email){
            $_SESSION['flash'] = "E-mail Inválido.";
            $this->redirect("/config");   
        }
        $updateFields["email"] = $email;


        //password
        if(empty($password)){
            $_SESSION['flash'] = "Senha Atual Obrigatoria.";
            $this->redirect("/config");
        }
        $validatePass = UserHandler::verifyPassword($this->loggedUser->email , $password);
        if(!$validatePass){
            $_SESSION['flash'] = "Senha Atual Incorreta.";
            $this->redirect("/config");
        }
        $updateFields["password"] = $password;

        //birthdate
        $birthDate = explode("/" , $birthDate);
        if(count($birthDate) > 3){
            $_SESSION['flash'] = "Data de Nascimento inválida.";
            $this->redirect("/config");
        }
        $birthDate = $birthDate[2]."-".$birthDate[1]."-".$birthDate[0];
        if(!strtotime($birthDate)){
            $_SESSION['flash'] = "Data de Nascimento inválida.";
            $this->redirect("/config");
        }
        $updateFields["birthDate"] = $birthDate;


        //newPassword
        if($newPassword && $newPasswordConfirm ){
            if($newPassword === $newPasswordConfirm){
                $updateFields["password"] = $newPassword;
            } else {
                $_SESSION['flash'] = "As Senha Não Batem.";
                $this->redirect("/config");
            }
        }

        
        //city
        if(!empty($city)){
            $updateFields["city"] = $city;
        }

        if(!empty($work)){
            $updateFields["work"] = $work;
        }

        //avatar
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']["tmp_name"])){
            $newAvatar = $_FILES['avatar'];

            if(in_array($newAvatar["type"], ["image/jpeg" , "image/png" , "image/jpg"])){
                $avatarName = $this->cutImage($newAvatar , 200 , 200 , "media/avatars");
                $updateFields["avatar"] = $avatarName;
            }
        }


        //cover
        if(isset($_FILES['cover']) && !empty($_FILES['cover']["tmp_name"])){
            $newCover = $_FILES['cover'];

            if(in_array($newCover["type"], ["image/jpeg" , "image/png" , "image/jpg"])){
                $coverName = $this->cutImage($newCover , 850 , 310 , "media/covers");
                $updateFields["cover"] = $coverName;
            }
        }


        UserHandler::updateUser($updateFields , $this->loggedUser->id); 
        $this->redirect("/config");      
    }

    private function cutImage($file , $w , $h , $folder){
        list($widthOrig , $heightOrig) = getimagesize($file["tmp_name"]);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h){
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x / 2 : $x;
        $y = $y < 0 ? $y / 2 : $y;
        
        $finalImage = imagecreatetruecolor($w, $h);
        switch ($file["type"]) {
            case 'image/jpeg':
            case "image/jpg":
                $image = imagecreatefromjpeg($file["tmp_name"]);
            break;
            case "image/png":
                $image = imagecreatefrompng($file["tmp_name"]);
            break;
        }

        imagecopyresampled(
            $finalImage,$image,
            $x , $y , 0 , 0,
            $newWidth , $newHeight,
            $widthOrig , $heightOrig
        );

        $fileName = md5(time().rand(0 , 99999)).".jpg";

        imagejpeg($finalImage , $folder."/".$fileName);


        return $fileName;
    }
} 