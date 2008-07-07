<?php
/*
Plugin Name: Email commenters
Version: 0.2
Plugin URI: http://yoast.com/wordpress/email-commenters/
Description: Gives a simple mailto: link below a post for logged in admins, to email all commenters on a post.
Author: Joost de Valk
Author URI: http://yoast.com/

Copyright 2008 Joost de Valk (email: joost@joostdevalk.nl)

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

function add_mailto_commenters_link($content) {
	if (is_single() && current_user_can('edit_users')) {
		global $wpdb, $post;
		$query = "SELECT DISTINCT comment_author, comment_author_email FROM $wpdb->comments WHERE comment_type = '' AND comment_approved = '1' AND comment_post_ID = ".$post->ID;
		$results = $wpdb->get_results($query);
		$content .= '<p><a href="mailto:';
		foreach ($results as $comment) {
			$email = urlencode($comment->comment_author." <".$comment->comment_author_email.">,");
			$content .= str_replace("+"," ",$email);
		}
		$content .= '">Mail the commenters on this post</a></p>';
	}
	return $content;
}

add_filter('the_content','add_mailto_commenters_link',95);	

?>
