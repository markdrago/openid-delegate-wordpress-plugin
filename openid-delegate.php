<?php
/*
Plugin Name: OpenID Delegation
Plugin URI: http://eran.sandler.co.il/openid-delegate-wordpress-plugin/
Description: Adds OpenID delegation to a WordPress blog allowing users to authenticate/signin/signup to OpenID supported sites with their blog's URL.
Version: 0.1
Author: Eran Sandler
Author URI: http://eran.sandler.co.il
*/

/*
License: GPLv2
Compatibility: All

What it does:
Adds support for OpenID delegation for WordPress blogs.

Installation:
Put the openid-delegate.php file in your /wp-content/plugins/ directory
and activate through the administration panel.

Copyright (C) Eran Sandler (http://eran.sandler.co.il/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* 
  Changelog

  * Sun Jan 7 2007 - v0.1
    - Initial release
*/

function openiddelegate_add_pages() {
	add_options_page('OpenID Delegation Options', 'OpenID Delegation', 8, __FILE__, 'openiddelegate_options_page');
}

function openiddelegate_update_option($optionName, $optionValue) {
	if (!empty($optionValue)) {
		update_option($optionName, $optionValue);
	}
	else {
		delete_option($optionName);
	}
}

function openiddelegate_options_page() {	
	if (isset($_POST['info_update'])) {
		$xrdsurl = $_POST['openiddelegate_xrdsurl'];
		$openidserverurl = $_POST['openiddelegate_serverurl'];
		$openiddelegateurl = $_POST['openiddelegate_delegateurl'];

		openiddelegate_update_option('openiddelegate_xrdsurl', $xrdsurl);
		openiddelegate_update_option('openiddelegate_serverurl', $openidserverurl);
		openiddelegate_update_option('openiddelegate_delegateurl', $openiddelegateurl);

		$msg_desc = "Configuration saved";
	}

	$xrdsurl = get_option('openiddelegate_xrdsurl');
	$openidserverurl = get_option('openiddelegate_serverurl');
	$openiddelegateurl = get_option('openiddelegate_delegateurl');

	if (!empty($msg_desc)) {
		_e('<div id="message" class="updated fade"><p>' . $msg_desc . '</div>');
	}

	_e('
	<div class="wrap">
		<h2>OpenID Delegation</h2>
		<p>The options in this page will allow you to set the necessary parameters to enable <a href="http://openid.net">OpenID</a> delegation on your blog. Doing so will enable you to sign in or sign up to OpenID enabled site using your blog\'s Url and your OpenID account password.</p>
<p>If you can\'t (or don\'t want to) run your own OpenID server, you can set an account up at <a href="http://myopenid.com">myOpenID</a> - a free OpenID server - and set the delegation in your blog to it.</p>
<p>For example:<br />If you open an account at <a href="http://www.myopenid.com">myOpenID</a> named "john" you\'ll need to enter the following values:</p>
<p>
	<ul>
		<li>OpenID Server Url: <b>http://john.myopenid.com</b></li>
		<li>OpenID Delegate Server Url: <b>http://john.myopenid.com</b></li>
		<li>OpenID XRDS Url: <b>http://john.myopenid.com/xrds</b></li>
	</ul>
</p>
		<form name="formopeniddelegate" method="post" action="' . $_SERVER['REQUEST_URI'] . '">
			<fieldset class="options">
				<table class="optiontable">
					<tbody>
					<tr valign="top">
						<th scope"row">OpenID Server Url:</th>
						<td>
							<input type="text" name="openiddelegate_serverurl" value="' . $openidserverurl . '" size="50"/>
						</td>
					</tr>						
					<tr valign="top">
						<th scope="row">OpenID Delegate Server Url:</th>
						<td>
							<input type="text" name="openiddelegate_delegateurl" value="' . $openiddelegateurl . '" size="50" />
						</td>
					</tr valign="top">
					<tr>
						<th scope="row">OpenID XRDS Url:</th>
						<td>
							<input type="text" name="openiddelegate_xrdsurl" value="' . $xrdsurl . '" size="50" />
						</td>
					</tr>
					</tbody>
				</table>
				<p></p>
			</fieldset>
			<p class="submit">
				<input type="submit" name="info_update" value="Update Options &raquo;" />
			</p>
		</form>
	</div>

	');

}

function openiddelegate_add_meta_tags() {
	global $posts;
	global $post; 

	$id = $post->ID;
	$show_on_front = get_option('show_on_front');
	$page_on_front = get_option('page_on_front');

	if (($show_on_front == 'page' && $page_on_front == $id) ||
	    ($show_on_front != 'page' && is_home())) {
		$xrdsurl = get_option('openiddelegate_xrdsurl');
		$openidserverurl = get_option('openiddelegate_serverurl');
		$openiddelegateurl = get_option('openiddelegate_delegateurl');

		if (!empty($xrdsurl)) {		
			$xrds = '<meta http-equiv="X-XRDS-Location" content="' . $xrdsurl . '" />';
			echo $xrds;
		}

		if (!empty($openidserverurl)) {
			$openidserver = '<link rel="openid.server" href="' . $openidserverurl . '" />';
			echo $openidserver;
		}

		if (!empty($openiddelegateurl)) {
			$openiddelegate = '<link rel="openid.delegate" href="' . $openiddelegateurl . '" />';
			echo $openiddelegate;
		}
	}
}

add_action('admin_menu', 'openiddelegate_add_pages');

add_action('wp_head', 'openiddelegate_add_meta_tags');
?>
