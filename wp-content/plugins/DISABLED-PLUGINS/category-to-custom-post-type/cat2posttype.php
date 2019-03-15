<?php
/*
Plugin Name: Category to Custom Post Type
Plugin URI:
Description: Allows you to move categories to custom post types.
Version: 0.1.5
Author: Erik Mitchell
Author URI: http://www.millerdesignworks.com
License: GPL2
Text Domain: ctpt
*/

class Cat2PostType {

	public $version='0.1.5';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action('admin_menu',array($this,'menu_page'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
	}

	/**
	 * admin_scripts_styles function.
	 *
	 * @access public
	 * @param mixed $hook
	 * @return void
	 */
	public function admin_scripts_styles($hook) {
		if ($hook!='tools_page_cat2posttype')
			return false;

		wp_enqueue_script('ctpt-admin-cats-script',plugins_url('js/cats.js',__File__), array('jquery'),	$this->version);

		wp_enqueue_style('ctpt-admin-style',plugins_url('css/style.css', __FILE__));
	}

	/**
	 * menu_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function menu_page() {
		add_management_page('Cat 2 Post Type','Cat 2 Post Type','manage_options','cat2posttype',array($this,'admin_page'));
	}

	/**
	 * admin_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_page() {
		global $wpdb;

		$html=null;
		$message=null;
		$args=array(
			'public'   => true,
			'_builtin' => false
		);
		$tax=get_taxonomies($args);
		$wp_dd_args=array(
			'hide_empty' => 0,
			'hierarchical' => 1,
			'show_option_none'=>'Select Category',
			'echo' => 0,
			'orderby' => 'NAME'
		);
		$post_args=array(
			'public' => true,
			'_builtin' => false,
		);
		$post_output='objects';
		$post_types=get_post_types($post_args,$post_output);

		$messages=$this->proccess_category();
		?>

		<div class="wrap">
			<h1>Category 2 Post Type</h1>

			<?php $this->output_admin_messages($messages); ?>

			<form id="update-form" class="" name="update-form" action="" method="post">
				<input type="hidden" name="c2p_run" value="1" />
				<?php wp_nonce_field('run_script','cat_2_post_type'); ?>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="cat">Category</label></th>
							<td>
								<?php echo wp_dropdown_categories($wp_dd_args); ?>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="custom_post_type">Custom Post Type</label></th>
							<td>
								<select name="custom_post_type" id="custom_post_type" class="postform">
									<option value="0">Select Post Type</option>
									<?php foreach ($post_types as $type) : ?>
										<option value="<?php echo $type->name; ?>"><?php echo $type->label; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="tax">Custom Taxonomy</label></th>
							<td>
								<select name="tax" id="tax" class="postform">
									<option value="0">Select Taxonomy</option>
									<?php foreach ($tax as $t) : ?>
										<option value="<?php echo $t; ?>"><?php echo $t; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="new_cat">New Category Parent</label></th>
							<td>
								<?php
								// create various dropdowns for each taxonomy, jQuery will handle the displaying of each //
								foreach ($tax as $t) :
									$wp_new_dd_args=array(
										'name' => 'new_cat',
										'hide_empty' => 0,
										'hierarchical' => 1,
										'taxonomy' => $t,
										'show_option_none'=>'Select Category',
									);
									?>
									<div id="<?php echo $t; ?>" class="new-cat-dd" style="display:none">
										<?php wp_dropdown_categories($wp_new_dd_args); ?>
										<span class="description">(optional)</span>
									</div>
								<?php	endforeach;	?>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="move_children">Move Children</label></th>
							<td>
								<label title="yes"><input type="radio" name="move_children" value="1" />Yes</label><br />
								<label title="no"><input type="radio" name="move_children" value="0" checked="checked" />No</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="delete_old">Delete Category</label></th>
							<td>
								<label title="yes"><input type="radio" name="delete_old" value="1" />Yes</label><br />
								<label title="no"><input type="radio" name="delete_old" value="0" checked="checked" />No</label>
							</td>
						</tr>

					</tbody>
				</table>

				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
			</form>

		</div>
		<?php
	}

	/**
	 * proccess_category function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function proccess_category() {
		global $wpdb,$post;

		$match_custom_fields=0;
		$messages=array(
			'error' => array(),
			'success' => array()
		);
		$parent=0;
		$meta_fields_map=false;
		$posts_count=0;

		extract($_POST);

		// check nonce //
		if (!isset($_POST['cat_2_post_type']) || !wp_verify_nonce($_POST['cat_2_post_type'],'run_script'))
			return false;

		// check category //
		if ($cat==-1) :
			$messages['error'][]='Category not found.';
			return $messages;
		endif;

		// check for custom post type //
		if (!$custom_post_type) :
			$messages['error'][]='Custom Post Type not found.';
			return $messages;
		endif;

		$main_cat=get_term_by('id',$cat,'category'); // get primary category id

		// check our new category (parent), then set our array and new cat id //
		if ($new_cat!=-1)
			$parent=$new_cat;

		$new_cat=array(
			'cat_name' => $main_cat->name,
			'category_description' => $main_cat->description,
			'category_nicename' => $custom_post_type."-".$main_cat->slug,
			'category_parent' => $parent,
			'taxonomy' => $tax,
		);
		$new_cat_id = wp_insert_category($new_cat);

		// update posts to custom post type //
		$args=array(
			'posts_per_page' => -1,
			'category' => $cat,
			'post_status' => 'all',
		);
		$posts=get_posts($args);

		// if no posts are found //
		if (!count($posts)) :
			$messages['error'][]='No posts to move.';
			return $messages;
		endif;

		// update our posts //
		foreach ($posts as $post) :
			setup_postdata($post);

			$update_posts=$wpdb->update($wpdb->posts,array('post_type'=>$custom_post_type),array('id'=>$post->ID)); // update in db

			if ($update_posts)
				$posts_count++;
		endforeach;

		do_action('c2p_after_posts_moved',$posts);

		// update term taxonomies //
		$this->update_term_taxonomies();

		// update our message //
		$messages['updated'][]='Category moved. '.$posts_count.' posts were created in '.$custom_post_type.'.';

		// update child taxonomies //
		if ($move_children)
			$this->update_child_taxonomies($cat, $custom_post_type, $tax, $delete_old);

		// delete category //
		if ($delete_old) :
			wp_delete_category($cat);
			$messages['updated'][]='Old categories removed.';
		endif;

		return $messages;
	}

	/**
	 * output_admin_messages function.
	 *
	 * @access public
	 * @param mixed $messages (default: aray())
	 * @return void
	 */
	public function output_admin_messages($messages=array()) {
		if (empty($messages))
			return false;

		foreach ($messages as $type => $msgs) :
			if (count($msgs)) :
				foreach ($msgs as $message) :
					echo '<div class="'.$type.'"><p>'.$message.'</p></div>';
				endforeach;
			endif;
		endforeach;
	}

	/**
	 * update_term_taxonomies function.
	 *
	 * @access protected
	 * @param int $cat (default: 0)
	 * @param int $new_cat_id (default: 0)
	 * @return void
	 */
	protected function update_term_taxonomies($cat=0, $new_cat_id=0) {
		global $wpdb;

		if (!$cat)
			return false;

		$term_taxonomy_details=$wpdb->get_row("SELECT term_taxonomy_id,count FROM {$wpdb->term_taxonomy} WHERE term_id={$cat}");
		$new_term_taxonomy_id=$wpdb->get_row("SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id={$new_cat_id}");

		$term_taxonomy_details_count_arr=array(
			'count' => $term_taxonomy_details->count
		);
		$update_count=$wpdb->update($wpdb->term_taxonomy, $term_taxonomy_details_count_arr, array('term_id' => $new_cat_id));

		$new_term_taxonomy_id_arr=array(
			'term_taxonomy_id' => $new_term_taxonomy_id->term_taxonomy_id
		);
		$update_tax=$wpdb->update($wpdb->term_relationships, $new_term_taxonomy_id_arr, array('term_taxonomy_id' => $term_taxonomy_details->term_taxonomy_id));

		return true;
	}

	/**
	 * update_child_taxonomies function.
	 *
	 * @access protected
	 * @param int $cat (default: 0)
	 * @param string $custom_post_type (default: 'post')
	 * @param string $tax (default: '')
	 * @param int $delete_old (default: 0)
	 * @return void
	 */
	protected function update_child_taxonomies($cat=0, $custom_post_type='post', $tax='', $delete_old=0) {
		global $wpdb;

		if (!$cat)
			return false;

		$child_args=array(
			'child_of' => $cat,
			'hide_empty' => false,
		);
		$categories=get_categories($child_args);

		foreach ($categories as $cat) :
			$new_sub_cat=array(
				'cat_name' => $cat->name,
				'category_description' => $cat->description,
				'category_nicename' => $custom_post_type.'-'.$cat->slug, // MIGHT WANT TO MAKE PARENT //
				'category_parent' => $new_cat_id,
				'taxonomy' => $tax,
			);
			$new_sub_cat_id = wp_insert_category($new_sub_cat);

			// update term taxonomies //
			$this->update_term_taxonomies($cat, $new_sub_cat_id);

			// delete old category //
			if ($delete_old)
				wp_delete_category($cat->cat_ID);

		endforeach;
	}

}

new Cat2PostType();
?>
