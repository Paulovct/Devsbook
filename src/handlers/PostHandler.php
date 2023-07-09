<?php

namespace src\handlers;
use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;
use \src\models\PostLike;
use \src\models\PostComment;


class PostHandler {

	public static function addPost($idUser , $type , $body){
		$body = trim($body);
		if(!empty($idUser) && !empty($body)){

			Post::insert([
				"id_user" => $idUser,
				"type" => $type,
				"created_at" => date("Y-m-d H:i:s"),
				"body" => $body
			])->execute();

		}
	}

	public static function deletePost($postId , $userId){
		$post = Post::select()->where("id" , $postId)->one();
		if($post["id_user"] == $userId){
			if($post["type"] == "photo" && file_exists("media/uploads/".$post["body"])){
				unlink("media/uploads/".$post["body"]);
			}
			Post::delete()->where("id" , $post["id"])->execute();
		}
	}

	public static function _postListToObject($list , $loggedUserId){
		$posts = [];

		foreach ($list as $postItem) {
			// informações iniciais do post
			$newPost = new Post();
			$newPost->id = $postItem["id"];
			$newPost->type = $postItem["type"];
			$newPost->created_at = $postItem["created_at"];
			$newPost->body = $postItem["body"];
			$newPost->mine = false;

			//verificar autoria do post
			if($postItem["id_user"] == $loggedUserId){
				$newPost->mine = true;
			}

			// post
			$newUser = User::select()->where("id",$postItem["id_user"])->one();
			$newPost->user = new User();
			$newPost->user->setId($newUser["id"]);
			$newPost->user->setName($newUser["name"]);
			$newPost->user->setAvatar($newUser["avatar"]);


			// postLikes
			$likes = PostLike::select()->where("id_post" , $postItem["id"])->get();
			$myLike = PostLike::select()
				->where("id_post" , $postItem["id"])
				->where("id_user" , $loggedUserId)
			->one();

			$newPost->likeCount = count($likes);
			$newPost->liked = $myLike ? true : false;

			//comments
			$newPost->comments = PostComment::select()
				->where("id_post" , $postItem["id"])
			->get();

			foreach ($newPost->comments as $key => $comment) {
				$newPost->comments[$key]["user"] =  User::select() ->where("id" , $comment["id_user"])->one();;
			}


			$posts[] = $newPost;

		}

		return $posts;
	}

	public static function getUserFeed($id , $page , $loggedUserId){
		$perPage = 2;
		$postList = Post::select()
			->where("id_user" , $id)
			->orderBy("created_at" , "desc")
			->page($page , $perPage)
		->get();
		
		$total = Post::select()
			->where("id_user" , $id)
		->count();

		$pageCount = ceil($total / $perPage);

		$posts = self::_postListToObject($postList , $loggedUserId);

		return [
			"posts" => $posts,
			"pageCount" => $pageCount,
			"currentPage" => $page
			];
	}

	public static function getHomeFeed($idUser , $page){
		$perPage = 5;

		$userList = UserRelation::select()
			->where("user_from" , $idUser)
		->get();

		$users = [];

		foreach ($userList as $userItem) {
			$users[] = $userItem["user_to"];
		}

		$users[] = $idUser;

		$postList = Post::select()
			->where("id_user" , "in" , $users)
			->orderBy("created_at" , "desc")
			->page($page , $perPage)
		->get();
		
		$total = Post::select()
			->where("id_user" , "in" , $users)
		->count();

		$pageCount = ceil($total / $perPage);

		$posts = self::_postListToObject($postList , $idUser);

		
		return [
			"posts" => $posts,
			"pageCount" => $pageCount,
			"currentPage" => $page
			];
	}

	public static function getPhotosFrom($id){
		$photosData = Post::select()
			->where("id_user" , $id)
			->where("type" , "photo")
		->get();

		$photos = [];

		foreach ($photosData as $photo) {
			$newPost = new Post();
			$newPost->id = $photo["id"];
			$newPost->type = $photo["type"];
			$newPost->created_at = $photo["created_at"];
			$newPost->body = $photo["body"];

			$photos[] = $newPost;
		}

		return $photos;
	}

	public static function toggleLike($postId , $userId){
		$like = PostLike::select()
			->where("id_post" , $postId)
			->where("id_user" , $userId)
		->one();

		if(!$like){
			PostLike::insert([
				"id_post" => $postId ,
				"id_user" => $userId ,
				"created_at" => date("Y-m-d H:i:s")
			])->execute();
		} else {
			PostLike::delete()
				->where("id" , $like["id"])
			->execute();			
		}
	}

	public static function addComment($id , $txt , $userId){
		PostComment::insert([
			"id_post" => $id,
			"id_user" => $userId,
			"created_at" => date("Y-m-d H:i:s"),
			"body" => $txt
		])->execute();
	}


}