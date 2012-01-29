<?php
/*
Plugin Name: Email commenters
Version: 1.2
Plugin URI: http://yoast.com/wordpress/email-commenters/
Description: Gives a simple mailto: link below in the toolbar to email all the commenters on a post.
Author: Joost de Valk
Author URI: http://yoast.com/
*/

function yoast_add_mailto_action_row( $actions ) {
	global $comment;
	
	if ( $comment->comment_type != '' )
		return $actions;
		
	$post = get_post( $comment->comment_post_id );
	
	$name = explode( " ", $comment->comment_author );
	$firstname = $name[0];
	
	$link = 'mailto:'
				.rawurlencode( $comment->comment_author.' <'.$comment->comment_author_email.'>' )
				.'?subject='.rawurlencode('RE: '.$comment->post_title)
				.'&amp;body='.rawurlencode("Hi ".$firstname.",\n\nI'm emailing you because you commented on my post \"".$post->post_title."\" - ".get_permalink($comment->comment_post_ID)." .\n");
	$actions['Mailto'] = '<a href="'.$link.'">E-Mail</a>';
	return $actions;
}
add_filter( 'comment_row_actions','yoast_add_mailto_action_row' );

function yoast_admin_bar_comment_link() {
	global $wp_admin_bar, $wpdb, $post;

	if ( is_singular() && current_user_can('edit_users') && $post->comment_count > 0 ) {
		
		$adminemail = get_bloginfo('admin_email');
		$query 		= "SELECT DISTINCT comment_author_email, comment_author FROM $wpdb->comments "
					 ."WHERE comment_type = '' AND comment_approved = '1' AND comment_post_ID = ".$post->ID;
		$results 	= $wpdb->get_results($query);
		
		if ( count($results) == 0 ) 
			return;

		$message = rawurlencode("Hi,\n\nI'm emailing you because you commented on my post \"".get_the_title()."\" - ".get_permalink()." .\n");
		
		$url = 'mailto:'.$adminemail.'?bcc=';
		foreach ($results as $comment) {
			if ($comment->comment_author_email != $adminemail)
				$url .= rawurlencode($comment->comment_author." <".$comment->comment_author_email.">,");
		}
		$url .= '&amp;subject=RE: '.get_the_title();
		$url .= '&amp;body='.$message;
		
		$wp_admin_bar->add_menu( array( 'id' => 'email-commenters', 'title' => '<span style="margin-top:5px;position:relative;float:left;width:16px;height:16px;background-image:url('.plugin_dir_url(__FILE__).'/email-commenters.png)"></span>', 'href' => $url ) );
	}
}
add_action( 'admin_bar_menu', 'yoast_admin_bar_comment_link', 65 );