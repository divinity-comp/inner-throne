<?php
/**
 * Plugin Name: Fusion Builder Custom Post Types and Taxonomies
 * Plugin URI: http://www.amunet.biz
 * Description: The plugin adds custom post types and custom taxonomies functionality to the Fusion page builder used by Avada theme.
 * Version: 5.4.1
 * Author: Amunet
 * Author URI: http://www.amunet.biz
 */

# Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'cptt_activation.php');
require_once(plugin_dir_path(__FILE__) . 'includes/cptt_functions.php');


// Load shortcode elements.
function cpt_init_shortcodes() {
    require_once(plugin_dir_path(__FILE__) . 'includes/fusion-portfolio-cpt.php');
    require_once(plugin_dir_path(__FILE__) . 'includes/fusion-blog-cpt.php');
}

add_action( 'fusion_builder_shortcodes_init', 'cpt_init_shortcodes' );

//add_filter('fusion_builder_enabled_elements', 'add_new_shortcode', 10);

function add_new_shortcode($fusion_builder_enabled_elements)
{

    $fusion_builder_enabled_elements[] = 'fusion_blog_cpt';
    $fusion_builder_enabled_elements[] = 'fusion_portfolio_cpt';

    return $fusion_builder_enabled_elements;

}


class AvadaCPTTHelper
{
//returns custom post type, taxonomies and terms in one array
    public static function am_all_custom_data() {

        //Array to be returned by the function
        $all_post_types_taxonomy=array();

        // create array for post type options
        $conf_pt= array (
            'public' => true,
            '_builtin' => false
        );

        // get registered custom post types
        $custom_post_types_default = array('post'=> 'post');
        $custom_post_types_registered = get_post_types( $conf_pt, 'names', 'and');
        $custom_post_types = array_merge($custom_post_types_default, $custom_post_types_registered);


        //assign taxonomies to posts
        $args_terms = array(
            'orderby'    => 'count',
            'order' => 'DESC',
            'hide_empty' => 0,
        );

        foreach ($custom_post_types as $custom_post_type) {

            //get the list of taxonomies for the post tyep
            $taxonomy_objects = get_object_taxonomies( $custom_post_type);

            //get the list of terms object for the taxonomy
            unset($custom_taxonomies);
            $custom_taxonomies = array();
            foreach ($taxonomy_objects as $taxonomy_object) {

                $terms = get_terms($taxonomy_object, $args_terms);
                //cretae the array of taxonomies
                $custom_taxonomies[$taxonomy_object] = $terms;
            }

            //create the array of post types
            $all_post_types_taxonomy[$custom_post_type]= $custom_taxonomies;
        }
        return $all_post_types_taxonomy;
        //	return $custom_taxonomies;

    }

    //returns custom post types array
    public static function am_custom_post_type_array() {

        $all_custom_array = self::am_all_custom_data();
        $custom_post_types_formatted=array();

        foreach ($all_custom_array as $custom_post_type => $taxonomy){
            $custom_post_types_formatted[$custom_post_type] = esc_attr__($custom_post_type, 'fusion-core');
        }
        return $custom_post_types_formatted;
    }

    //returns all taxonomy array
    public static function am_all_taxonomy_array() {

        $all_custom_array = self::am_all_custom_data();
        $custom_taxonomy_formarted=array();

        $custom_taxonomy_formarted['xxx__select_taxonomy'] = esc_attr__('Select Taxonomy', 'fusion-core');
        foreach ($all_custom_array as $custom_post_type => $taxonomies){

            foreach ($taxonomies as $taxonomy => $terms) {

                $custom_taxonomy_formarted[$custom_post_type.'__'.$taxonomy] = esc_attr__($taxonomy, 'fusion-core');
            }
        }
        return $custom_taxonomy_formarted;
    }

    //returns custom taxonomy array
    public static function am_custom_taxonomy_array() {

        //build the list of built in taxonomies
        $args_tax = array(
            'public'   => true,
            '_builtin' => true
        );

        $built_in_taxonomies = get_taxonomies( $args_tax, 'names', 'and');
        $indexed_array_built_in_tax=array();
        foreach ($built_in_taxonomies as $built_in_taxonomy =>$some_value) {
            $indexed_array_built_in_tax[]=$built_in_taxonomy;
        }

        $all_custom_array = self::am_all_custom_data();
        $custom_taxonomy_formarted=array();
        $custom_taxonomy_formarted['xxx__select_taxonomy'] = esc_attr__('Select Taxonomy', 'fusion-core');
        foreach ($all_custom_array as $custom_post_type => $taxonomies){

            foreach ($taxonomies as $taxonomy => $terms) {

                //filter built_in taxonomies
                if (in_array($taxonomy, $indexed_array_built_in_tax)) {
                    //continue;
                }

                $custom_taxonomy_formarted[$custom_post_type.'__'.$taxonomy] = esc_attr__($taxonomy, 'fusion-core');
            }
        }
        return $custom_taxonomy_formarted;
    }


    //	return custom terms
    public static function am_custom_terms_array() {
        $all_custom_array = self::am_all_custom_data();

        $custom_terms_formarted=array();
        foreach ($all_custom_array as $custom_post_type => $taxonomies){

            foreach ($taxonomies as $taxonomy => $terms) {

                foreach ( $terms as $term) {
                    $custom_terms_formarted[$taxonomy.'__'.$term->slug] = esc_attr__($term->name.' ('.$term->count.')', 'fusion-core');
                }

            }

        }
        return $custom_terms_formarted;

    }

}

function fusion_cptt_scripts()
{
   wp_enqueue_script('fusion_custom_select', plugins_url('assets/js/fusion_cptt_select.js', __FILE__), array('jquery'), '1.22', true);
   wp_enqueue_style('fusion_cptt_styles', plugins_url('assets/css/fusion_cptt_styles.css', __FILE__), array(), '1.0');
}

add_action('admin_enqueue_scripts', 'fusion_cptt_scripts');

?>
