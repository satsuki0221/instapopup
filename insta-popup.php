<?php
/*
Plugin Name: Insta PopUp
Plugin URI:
Description: display a pop-up Using the API of Instagram.
Version: 1.0.0
Author:ymmo
Author URI:http://web-bloger.2-d.jp/
License: GPL2

Copyright 2016 ymmo (email : ymmo.9m@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'INSTAPOPUP__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'INSTAPOPUP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
require_once( INSTAPOPUP__PLUGIN_DIR . 'class.Insta.php' );

class InstaPOPUP {
	function __construct() {
		add_action('admin_menu', array($this, 'add_pages'));
	}
	function add_pages() {
		add_menu_page('Insta Setting','Insta Setting',  'level_8', __FILE__, array($this,'show_text_option_page'), '', 26);
	}

	function show_text_option_page() {
		if ( isset($_POST['instapopup_options'])) {
			check_admin_referer('shoptions');
			$opt = $_POST['instapopup_options'];
			update_option('instapopup_options', $opt);
			?>
			<div class="updated fade"><p><strong><?php _e('Options saved.'); ?></strong></p></div><?php
		} ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br /></div><h2>Insta Setting</h2>
				<form action="" method="post">
				<?php
					wp_nonce_field('shoptions');
					$opt = get_option('instapopup_options');
					$account = isset($opt['account']) ? $opt['account']: null;
					$token = isset($opt['token']) ? $opt['token']: null;
					$display = isset($opt['display']) ? $opt['display']: null;
				?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><label for="inputtext">User Acocunt</label></th>
							<td><input name="instapopup_options[account]" type="text" id="inputtext1" value="<?php  echo $account ?>" class="regular-text" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="inputtext">Access Token</label></th>
							<td><input name="instapopup_options[token]" type="text" id="inputtext2" value="<?php  echo $token ?>" class="regular-text" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="inputtext">Display (1〜20)</label></th>
							<td>
								<input name="instapopup_options[display]" type="text" id="inputtext" type="number" value="<?php  echo $display ?>" class="regular-text" />
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
				</form>
			</div>
		<?php
	}

	function insta_data(){

		$opt = get_option('instapopup_options');
		$Insta = new Insta( $opt['account'] , $opt['token'] , $opt['display'] );
		$instagramData = $Insta->getInstaData();

		$instaContents = [];
		$index = 0;

		foreach ($instagramData->data as $data) {
			$instaContents[$index]['url'] = $data->link;
			$instaContents[$index]['thum'] = $data->images->standard_resolution->url;



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



	function create() {
		$html = '';
		$instaData = $this->insta_data();

		foreach ($instaData as $data) {

			if ($data === reset($instaData)) {
				$html = $html . '<ul>';
			}

			$html = $html ."<li class=''>";
			$html = $html ."<a href='#inline{$data['id']}' class=''>";
			$html = $html ."<img src='{$data['thum']}' class='' />";
			$html = $html ."<p class=''>{$data['created_time']}</p>";
			$html = $html . "</a>";
			$html = $html . "</li>";

			if ($data === end($instaData)) {
				$html = $html . '</ul>';
			}
		}

		return $html;

	}
}

$instapopup = new InstaPOPUP;
