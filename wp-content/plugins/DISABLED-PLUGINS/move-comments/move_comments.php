<?php
/*
Plugin Name: Move Comments
Plugin URI: http://www.dountsis.com/projects/move-comments/
Description: Allows you to move comments between posts or pages. Adds a section under <a href="edit-comments.php?page=move-comments/move_comments.php">Comments -> Move</a>.
Author: Apostolos Dountsis
Author URI: http://www.dountsis.com
Version: 1.2
*/

/*  Copyright 2007  APOSTOLOS DOUNTSIS

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

// Stop direct call to the file
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) 
{
	die('You are not allowed to call this page directly.'); 
}

include_once('move_comments_model.php');
include_once('move_comments_common.php');

if (!class_exists('move_comments')) 
{
	class move_comments
	{
		var $db;
		var $form_errors;
		
		// Constructor
		function move_comments()
		{
			// Instantiate the model object
			$this->db =& new move_comments_model();
			
			// Attach the View 
			$this->attach_view();
			
			// If a post has occured then validate the data prior to submitting them.
			if($_POST and $this->validate_form($_POST))
			{
				$this->process_post_submission($_POST);
			}
		}
	
		// Moves the comments from a post_id to another
		function process_post_submission(&$data)
		{
			if($data and is_array($data))
			{
//				move_comments_common::pre_print_r($data);
				$source_post_id = (int) $data['source_post_id'];
				$target_post_id = (int) $data['target_post_id'];
				foreach($data['move_comment_id'] as $comment_id)
				{
					$this->db->move_comment($source_post_id, $target_post_id, $comment_id);
				}
			}
			// Refresh the page
			move_comments_common::redirect();
		}
		
		// Form validation
		function validate_form(&$data)
		{
			$validate = true;
			
			if($data['target_post_id'] == 0)
			{
				$this->form_errors['target_post_id'] = 'Please select a post';
				$validate = false;
			}
			elseif($data['target_post_id'] == $data['source_post_id'])
			{
				$this->form_errors['target_post_id'] = 'You are trying to move the comment(s) to the same post/page.';
				$validate = false;
			}
			
			if(!isset($data['move_comment_id']))
			{
				$this->form_errors['movebutton'] = 'Please select at least one comment before pressing the button.';
				$validate = false;
			}
			
			return $validate;
		}
		
		// Attach the View to the Dashboard
		function attach_view()
		{
		
			// Has the user access to moderate comments?
	//		if(current_user_can('moderate_comments'))
	//		{	
				// Add Admin Menu
				add_action('admin_menu', array(&$this, 'admin_menu'));
	//		}
		}
		
		// Manage Admin Options
		function admin_menu()
		{
			global $submenu;

			// Attach the GUI under 'Comments' 
			add_submenu_page('edit-comments.php', 'move-comments', 'Move', 8, __FILE__, array(&$this, 'admin_page'));
		}	
	
		// Admin page
		function admin_page()
		{
			$html = '<div class="wrap">';
			$html .= '<h2>Move Comments</h2>';
			
			// Blurb
			$html .= "<p>A lot of your readers tend not to notice that they post a comment on the wrong page or post. Use the interface below to move comments from a post or page to another.</p>";
			
			// the form
			$html .= $this->display_form();
	
			// Debug Screen
	// 		$html .= $this->debug_section();
			
			$html .= '</div>';
			
			print($html);
		}
		
		function display_form()
		{
			$html = '<div class="tablenav">'."\n";
			$html .= $this->display_source_filter();
			$html .= '</div>'."\n";
			$html .= '<form name="move-comments" method="post" action="'.$_SERVER['PHP_SELF'].'?page='.$_REQUEST['page'].'&source_post_id='.$_GET['source_post_id'].'">'."\n";
			
			if($_GET['source_post_id'] and is_numeric($_GET['source_post_id']))
			{
				$html .= $this->display_comments($_GET['source_post_id']);
			}
			else
			{
				$html .= '<p>Select a post or page to browse its comments.</p><br />'."\n";
			}
	
			$html .= $this->display_target_post();
			$html .= '<br /><br />'."\n";;
			
			// Hidden form attribute for source_post_id
			$html .= '<input type="hidden" name="source_post_id" value="'.$_GET['source_post_id'].'">'."\n";
			
			// Submit button
			$html .= '<p class="submit"><input type="submit" value="Update Options  &raquo;">'."\n";
			if($this->form_errors['movebutton'])
			{
				$html .= '<strong style="color:red;"> <- '.$this->form_errors['movebutton'].'</strong></p>'."\n";
			}
			else
			{
				$html .= "</p>\n";
			}
			$html .= '</form>'."\n";
		
			return $html;
		}
	
		// Diplay the select control with all the posts that have comments for the source
		function display_source_filter()
		{
			$html = '';
			$id = (int)$_REQUEST['source_post_id'];
			
			// Get the posts that have comments
			$posts = $this->db->get_posts_with_comments();
			
			if(!empty($posts))
			{
//				$html = 'View comment(s) in post or page: ';
				$html .= "<select name=\"source_post_id\" onchange=\"javascript:location.href='?page=move-comments/move_comments.php&source_post_id='+this.options[this.selectedIndex].value;\">";
	
				$s = 0;
				if($id == 0)
				{
					$s = 'selected';
				}
//				$html .= '<option value="0" '.$s.'>-- Select a Post or Page --</option>';
				$html .= '<option value="0" '.$s.'>-- Move From --</option>';
				
				foreach($posts as $p)
				{
					$s = "";
					if($id == $p->id)
					{
						$s = "selected";
					}
					$html .= "<option value=\"$p->id\" $s>$p->post_title</option>";
				}
				$html .= '</select>';
				
				if($id)
				{
					// $this->db->get_post_title_by_id($id);
					$html .= " <span class=\"button-secondary action\"><a href=\"".get_permalink($id)."\" target=\"_blank\" style=\"text-decoration:none;\">Visit</a></span>";
				}
				
			}
			return $html;
		}
		
		// Render the comment for the specified $post_id
		function display_comments($post_id)
		{
			$comments = array();
			$html = '';
			
			if(is_numeric($post_id))
			{
				$comments = $this->db->get_comments_by_postid($post_id);
			}
			
			if(!empty($comments))
			{
				// List the files in the database
//				$html .= '<table id="the-list-x" width="100%" cellpadding="3" cellspacing="3">'."\n";
				$html .= '<table class="widefat fixed" cellspacing="0">'."\n";
				
				$html .= '<thead>'."\n";
				$html .= '<tr>'."\n";
	//			$html .= '<th scope="col">ID</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="">Author</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="" width="400">Comment</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="">Date</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="">Select</th>'."\n";
//				$html .= '<th scope="col"  class="manage-column" style="" colspan="3">Select</th>'."\n";
				$html .= '<tr>'."\n";
				$html .= '</thead>'."\n";

				$html .= '<tfoot>'."\n";
				$html .= '<tr>'."\n";
	//			$html .= '<th scope="col">ID</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="">Author</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="" width="400">Comment</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="">Date</th>'."\n";
				$html .= '<th scope="col" class="manage-column" style="">Select</th>'."\n";
//				$html .= '<th scope="col"  class="manage-column" style="" colspan="3">Select</th>'."\n";
				$html .= '<tr>'."\n";
				$html .= '</tfoot>'."\n";
				
				$checkbox_index = 0;
				foreach($comments as $comment)
				{
					if(move_comments_common::is_even($checkbox_index))
					{
						$row_class = "alternate";
					}
					else
					{
						$row_class = "";
					}
					$html .= "<tr id=\"$comment->comment_id\" class=\"$row_class\">\n";
	//				$html .= "<td>$comment->comment_id</td>\n";
					$html .= "<td>$comment->comment_author</td>\n";
					
					// Display a portion of the comment_content if it is too long
					$comment_body = $comment->comment_content;
					if(strlen($comment_body) > 250)
					{
						$comment_body = substr($comment->comment_content, 0, 250);
						$comment_body .= ' [&#8230;]';
					}
	
					$html .= "<td>$comment_body</td>\n";
					$html .= "<td>$comment->comment_date</td>\n";
					
					// Display the comment entry as checked if the validation fails and user had it checked upon form submission
					if($_POST["move_comment_id"] and $_POST["move_comment_id"][$checkbox_index] == $comment->comment_id)
					{
						$checked = 'checked';
					}
					else
					{
						$checked = '';
					}
	
					$html .= "<td><input type=\"checkbox\" name=\"move_comment_id[$checkbox_index]\" value=\"$comment->comment_id\" $checked /></td>\n";
					$html .= '</tr>';
					$checkbox_index++;
				}
				$html .= '</table>'."\n";
				$html .= '<br />'."\n";
			}
			else
			{
				$html .= '<p><strong>There are no comments in this post.</strong></p><br />'."\n";
			}
			
			return $html;
		}
		
		// Diplay the selection options for the destination post/page	
		function display_target_post()
		{
			$html = '';
	
			$posts = $this->db->get_all_posts();
	
			if(!empty($posts))
			{
//				$html .= 'Move comment(s) to: '."\n";
				$html .= "<select name=\"target_post_id\">\n";
	
//				$html .= '<option value="0">-- Select a Post --</option>'."\n";
				$html .= '<option value="0">-- Move To --</option>'."\n";
				
				foreach($posts as $p)
				{
					$sel = 0;
					if($_POST['target_post_id'] == $p->id)
					{
						$sel = 'selected';
					}
					$html .= "<option value=\"$p->id\" $sel>$p->post_title</option>\n";
				}
				$html .= '</select>'."\n";
			}
			
			if($this->form_errors['target_post_id'])
			{
				$html .= '<strong style="color:red;"> <- '.$this->form_errors['target_post_id'].'</strong>'."\n";
			}
			
			return $html;
		}
	}
}

// Instantiate the object
$mc =& new move_comments();
?>