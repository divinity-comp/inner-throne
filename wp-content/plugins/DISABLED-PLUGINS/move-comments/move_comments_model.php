<?php
/*
 * move_comments_model.php
 *
 * Move Comments - WordPress Plugin
 * The Model class
 *
 * Author: Apostolos Dountsis
 */
 
include_once('move_comments_common.php');

class move_comments_model
{
	function get_all_posts()
	{
		global $wpdb;
		$data = array();

		$data = $wpdb->get_results("select id, post_title
					from $wpdb->posts
					where post_status = 'publish'
					order by id desc");
		return $data;
	}

	function get_posts_with_comments()
	{
		global $wpdb;
		$data = array();
		
		$data = $wpdb->get_results("select id, post_title
					from $wpdb->posts
					where comment_count > 0
					and post_status = 'publish'
					order by id desc");
		return $data;
	}
	
	function get_post_title_by_id($id)
	{
		global $wpdb;
		$data = array();
		if(is_numeric($id))
		{		
			$data = $wpdb->get_var("select post_title
						from $wpdb->posts
						where id = $id
						and post_status = 'publish'");
		}
		return $data;
	}
	
	function get_comments_by_postid($id)
	{
		global $wpdb;
		$data = array();

		if(is_numeric($id))
		{		
			$data = $wpdb->get_results("select comment_id, comment_author, 
						comment_date, comment_content
						from $wpdb->comments
						where comment_post_id = $id
						order by comment_id desc");
		}
		return $data;
	}
	
	function move_comment($source_post_id, $target_post_id, $comment_id)
	{
		global $wpdb;
		
		// update the comment_post_id to $target_post_id
		$sql[] = "update {$wpdb->comments}
				set comment_post_id = $target_post_id
				where comment_id = $comment_id";

		//Decrement the comment_count in the $source_post_id
		$sql[] = "update {$wpdb->posts}
				set comment_count = comment_count-1
				where id = $source_post_id
				and post_status = 'publish'";
				
		// Increment the comment_count in the $target_post_id
		$sql[] = "update {$wpdb->posts}
				set comment_count = comment_count+1
				where id = $target_post_id
				and post_status = 'publish'";
		
		foreach($sql as $query)
		{
			$wpdb->query($query);
		}
	}
	
}
?>