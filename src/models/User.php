<?php

namespace src\models;
use \core\Model;

class User extends Model {
	public $id;	
	public $email;
	public $name;
	public $avatar;
	public $birthDate;
	public $cover;
	public $work;
	public $city;
	public $followers = [];
	public $following = [];
	public $photos = [];
	public $ageYears;

	public function setId($id){
		$this->id = $id;
	}

	public function setEmail($email){
		$this->email = $email;
	}
	public function setName($name){
		$this->name = $name;
	}

	public function setAvatar($avatar){
		$this->avatar = $avatar;
	}

	public function setBirthDate($date){
		$this->birthDate = $date;
	}

	public function setCover($cover){
		$this->cover = $cover;
	}

	public function setCity($city){
		$this->city = $city;
	}
	
	public function setWork($work){
		$this->work = $work;
	}
	
	public function setFollowers($followers){
		$this->followers = $followers;
	}
	
	public function setFollowing($following){
		$this->following = $following;
	}

	public function setPhotos($photos){
		$this->photos = $photos;
	}

}