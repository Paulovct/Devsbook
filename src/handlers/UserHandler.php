<?php

namespace src\handlers;

use \src\models\User;
use \src\models\UserRelation;
use \src\models\Post;



class UserHandler {
	public static function checkLogin(){
		if(!empty($_SESSION["token"])){
			$token = $_SESSION['token'];

			$data = User::select()->where("token" , $token)->one();

			if(count($data) > 0){

				$loggedUser = new User();
				$loggedUser->setId($data["id"]);
				$loggedUser->setEmail($data["email"]);
				$loggedUser->setName($data["name"]);
				$loggedUser->setAvatar($data["avatar"]);
				$loggedUser->setCover($data["cover"]);

				return $loggedUser;
			}
		}

		return false;
	}

	public static function verifyLogin($email , $password){
		$user = User::select()->where("email" , $email)->one();

		if($user){
			if(password_verify($password, $user["password"])){
				$token = md5(time().rand(0,999).time());

				User::update()
					->set("token",$token)
					->where("email" , $email)
				->execute();

				return $token;
			}
		}
		return false;
	}

	public static function verifyPassword($email , $password){
		$user = User::select()->where("email" , $email)->one();

		if($user){
			if(password_verify($password, $user["password"])){
				return true;
			} else {
				return false;
			}
		}
	}

	public static function emailExists($email){
		$user = User::select()->where("email" , $email)->one();
		return $user ? true : false;
	}
	
	public static function idExists($id){
		$user = User::select()->where("id" , $id)->one();
		return $user ? true : false;
	}

	public static function getUser($id , $full = false){
		$data = User::select()->where("id",$id)->one();
		
		if($data){
			$user = new User();
			$user->setId($data["id"]);
			$user->setEmail($data["email"]);
			$user->setName($data["name"]);
			$user->setAvatar($data["avatar"]);
			$user->setBirthDate($data["birthdate"]);
			$user->setCover($data["cover"]);
			$user->setCity($data["city"]);
			$user->setWork($data["work"]);
			

			if($full){
				$user->setFollowing = [];
				$user->setFollowers = [];
				$user->setPhotos = [];

				//followers
				$followers = UserRelation::select()->where("user_to" , $id)->get();
				foreach ($followers as $follower) {
					$userData = User::select()->where("id" , $follower["user_from"])->one();
					$newUser = new User();
					$newUser->setId($userData["id"]);
					$newUser->setName($userData["name"]);
					$newUser->setAvatar($userData["avatar"]);

					$user->followers[] = $newUser;
				}

				//following
				$following = UserRelation::select()->where("user_from" , $id)->get();
				foreach ($following as $follower) {
					$userData = User::select()->where("id" , $follower["user_to"])->one();
					$newUser = new User();
					$newUser->setId($userData["id"]);
					$newUser->setName($userData["name"]);
					$newUser->setAvatar($userData["avatar"]);

					$user->following[] = $newUser;
				}

				//photos
				$user->setPhotos(PostHandler::getPhotosFrom($id));

			}


			return $user;
		}

		return false;
	}

	public static function addUser($name , $email , $password , $birthdate){
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$token = md5(time().rand(0,999).time());

		User::insert([
			"email" => $email,
			"name" => $name,
			"password" => $hash,
			"birthdate" => $birthdate,
			"token" => $token
		])->execute();

		return $token;
	}

	public static function isFollowing($from , $to){
		$data = UserRelation::select()
			->where("user_from" , $from)
			->where("user_to" , $to)
		->one();
		if($data){
			return true;
		} else {
			return false;
		}
	}

	public static function follow($from , $to){
		UserRelation::insert([
			"user_from" =>$from,
			"user_to" => $to
		])->execute();
	}

	public static function unfollow($from , $to){
		UserRelation::delete()
			->where("user_from" , $from)
			->where("user_to" , $to)
		->execute();
	}

	public static function searchUser($search){
		$users = [];
		$data = User::select()
			->where("name" , "like" , "%".$search."%")
		->get();
	

		if($data){
			foreach($data as $user){
				$newUser = new User();
				$newUser->setId($user["id"]);
				$newUser->setName($user["name"]);
				$newUser->setAvatar($user["avatar"]);

				$users[] = $newUser;
			}
		}

		return $users;
	}

	public static function updateUser($update , $id){
		$hash = password_hash($update["password"], PASSWORD_DEFAULT);
		User::update()
			->set("name" , $update["name"])
			->set("email" , $update["email"])
			->set("password" , $hash)
			->set("work" , $update["work"])
			->set("city" , $update["city"])
			->set("birthDate" , $update["birthDate"])
			->set("avatar" , $update["avatar"])
			->set("cover" , $update["cover"])
			->where("id",$id)
		->execute();
	}
}