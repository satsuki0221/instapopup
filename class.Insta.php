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

		protected function initialization(){

			$instaData = $this->InstaData;
			$instaContents = [];
			$index = 0;

			foreach ($instaData->data as $data) {

				$instaContents[$index]['url'] = $data->link;
				$instaContents[$index]['thum'] = $data->images->standard_resolution->url;

				if( $data->type == 'video' ){
					$instaContents[$index]["movie"] = $data->videos->standard_resolution->url;
				}else{
					$instaContents[$index]["movie"] = false;
				}

				if( $data->caption === null ){

					$instaContents[$index]['created_time'] =  date ('F d,Y', $data->created_time);
					$instaContents[$index]["caption"] = '';
					$instaContents[$index]["hashTagList"] = '';

				}else{

					$captionText = $data->caption->text;

					$hashTagArray = [];
					$hashLength = intval( substr_count($data->caption->text, "#") );
					$matches = preg_match_all('/(#[a-zA-Z0-9\x81-\x9f\xe0-\xfc\x40-\x7e\x80-\xfc]+)/', $captionText, $hashTags);
					$captionTextNew = str_replace($hashTags[0],'',$captionText);

					$instaContents[$index]['created_time'] =  date ('F d,Y', $data->caption->created_time);
					$instaContents[$index]["caption"] = $captionTextNew;
					$instaContents[$index]["hashTagList"] = $hashTags[0];


				}

				$instaContents[$index]['id'] = $data->id;
				$instaContents[$index]["like"] = $data->likes->count;

				$index++;
			}

			return $instaContents;
		}

		public function apiData(){
			return $this->InstaData;
		}

		public function getInstaData(){
			return  $this->initialization();
		}

	}
