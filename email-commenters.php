<?php
/*
Plugin Name: Email commenters
Version: 1.1
Plugin URI: http://yoast.com/wordpress/email-commenters/
Description: Gives a simple mailto: link below a post for logged in admins, to email all commenters on a post.
Author: Joost de Valk
Author URI: http://yoast.com/
*/

function yoast_add_mailto_commenters_link( $content ) {
	if (is_single() && current_user_can('edit_users')) {
		global $wpdb, $post;
		$adminemail = get_bloginfo('admin_email');
		$query 		= "SELECT DISTINCT comment_author, comment_author_email FROM $wpdb->comments "
					 ."WHERE comment_type = '' AND comment_approved = '1' AND comment_post_ID = ".$post->ID;
		$results 	= $wpdb->get_results($query);
		
		if (count($results) == 0)  {
			return $content;
		}
		$message = rawurlencode("Hi,\n\nI'm emailing you because you commented on my post \"".get_the_title()."\" - ".get_permalink()." .\n");
		
		$output = '<div class="alignright"><p><a href="mailto:'.$adminemail.'?bcc=';
		foreach ($results as $comment) {
			if ($comment->comment_author_email != $adminemail)
				$output .= rawurlencode($comment->comment_author." <".$comment->comment_author_email.">,");
		}
		$output .= '&amp;subject=RE: '.get_the_title();
		$output .= '&amp;body='.$message;
		$output .= '"><img src="'.plugin_dir_url(__FILE__).'/email-commenters.png"/></a></p></div>';
		return $output.$content;
	}
	return $content;
}
add_filter('the_content','yoast_add_mailto_commenters_link');

function yoast_add_mailto_action_row( $actions ) {
	global $comment;
	$link = 'mailto:'
				.rawurlencode($comment->comment_author.' <'.$comment->comment_author_email.'>')
				.'?subject='.rawurlencode('RE: '.$comment->post_title)
				.'&amp;body='.rawurlencode("Hi,\n\nI'm emailing you because you commented on my post \"".$comment->post_title."\" - ".get_permalink($comment->comment_post_ID)." .\n");
	$actions['Mailto'] = '<a href="'.$link.'">E-Mail</a>';
	return $actions;
}
add_filter('comment_row_actions','yoast_add_mailto_action_row');
?>