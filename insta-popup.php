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
		add_action( 'wp_head', 'add_css_js' );
		function add_css_js(){
			echo '<link rel="stylesheet" href="'. INSTAPOPUP__PLUGIN_URL .'assets/js/lib/colorbox/colorbox.css">';
			echo '<link rel="stylesheet" href="'. INSTAPOPUP__PLUGIN_URL .'assets/css/instapopup.css">';
			echo '<script src="'. INSTAPOPUP__PLUGIN_URL .'assets/js/lib/colorbox/jquery.colorbox-min.js"></script>';
			echo '<script src="'. INSTAPOPUP__PLUGIN_URL .'assets/js/instapopup.js"></script>';
		}
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
					<table>
						<tr valign="top">
							<th scope="row"><label for="inputtext">User Acocunt</label></th>
							<td><input name="instapopup_options[account]" type="text" id="inputtext1" value="<?php  echo $account ?>" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="inputtext">Access Token</label></th>
							<td><input name="instapopup_options[token]" type="password" id="inputtext2" value="<?php  echo $token ?>" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="inputtext">Display (1〜20)</label></th>
							<td>
								<input name="instapopup_options[display]" type="text" id="inputtext3" type="number" value="<?php  echo $display ?>" />
							</td>
						</tr>
					</table>
					<p><input type="submit" name="Submit" value="変更を保存" /></p>
				</form>
			</div>
		<?php
	}

	function create() {

		$opt = get_option('instapopup_options');
		$Insta = new Insta( $opt['account'] , $opt['token'] , $opt['display'] );

		$html = '';
		$instaData = $Insta->getInstaData();

		foreach ($instaData as $data) {

			if ($data === reset($instaData)) {
				$html .= '<ul id="insta_popup" class="insta_popup_list">';
			}

			$li = <<< EOM
				<li class="insta_popup_item">
					<a href="#{$data['id']}" class="insta_popup_link">
						<img src="{$data['thum']}" class="insta_popup_img" />
						<p class="insta_popup_day">{$data['created_time']}</p>
					</a>
				</li>
EOM;
			$html .=  $li;

			if ($data === end($instaData)) {
				$html .= '</ul>';
			}
		}

		$html .= "<div style='display:none'>";
		foreach ($instaData as $data) {
			$html .= "<div id='{$data['id']}' class='insta_popup' >";

				if( $data['movie'] ){
					$html .= "<video controls style='width: 100%'>";
						$html .= "<source src='{$data['movie']}' type='video/mp4; codecs='avc1.42E01E, mp4a.40.2''>";
						$html .= "<p>動画を再生するには、videoタグをサポートしたブラウザが必要です。</p>";
					$html .= "</video>";
				}else{
					$html .= "<img src='{$data['thum']}' class='blog__img' alt='' />";
				}

				$html .= "<div class='insta_popup_head'>";
					$html .= "<p class='insta_popup_date'>{$data['created_time']}</p>";
					$html .= "<p class='insta_popup_like'>♥ {$data['like']}</p>";
				$html .= "</div>";
				$html .= "<div class='insta_popup_body'>";
					$html .= "<p class='insta_popup_caption'>{$data['caption']}</p>";
					$html .= "<ul class='insta_popup_hash_list'>";
						foreach( $data['hashTagList'] as $tag) {
							$html .= "<li class='insta_popup_hash_item'>{$tag}</li>";
						}
					$html .= "</ul>";
				$html .= "</div>";
			$html .= "</div>";
		}
		$html .= "</div>";
		return $html;
	}
}

$instapopup = new InstaPOPUP;
