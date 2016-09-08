<?php

	class Insta{


		public function __construct( $userAccount , $accessToken , $num ) {
			$userApiUrl = "https://api.instagram.com/v1/users/search?q=$userAccount&access_token=$accessToken";
			$userDataJson = json_decode(@file_get_contents($userApiUrl)) ;

			// 取得したデータの中から正しいデータを選出
			foreach ($userDataJson->data as $userData) {
				if ($userAccount == $userData->username) {
					$userId = $userData->id;
				}
			}

			//$photos = array();
			$photosApiUrl = "https://api.instagram.com/v1/users/$userId/media/recent?access_token=$accessToken&count=$num";
			$photosData = json_decode(@file_get_contents($photosApiUrl));

			$this->InstaData = $photosData;
		}


		public function getInstaData(){
			return $this->InstaData;
		}

	}
