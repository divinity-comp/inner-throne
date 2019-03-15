<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Get Gallery id
 *
 * @return int
 */
function gallery_img_get_gallery_id()
{
    if (isset($_GET['page']) && $_GET['page'] == 'galleries_huge_it_gallery') {
        if (isset($_GET["id"])) {
            $id = intval($_GET["id"]);
        } else {
            $id = 0;
        }
    }

    return $id;
}

/**
 * Get $_GET['task']
 *
 * @return string
 */
function gallery_img_get_gallery_task()
{
    if (isset($_GET['page']) && $_GET['page'] == 'galleries_huge_it_gallery') {
        if (isset($_GET["task"])) {
            $task = sanitize_text_field($_GET["task"]);
        } else {
            $task = '';
        }
    }


    return $task;
}

/**
 * @param $catt
 * @param string $tree_problem
 * @param int $hihiih
 *
 * @return array
 */
function gallery_img_open_cat_in_tree($catt, $tree_problem = '', $hihiih = 1)
{

    global $wpdb;
    global $glob_ordering_in_cat;
    static $trr_cat = array();
    if (!isset($search_tag))
        $search_tag = '';
    if ($hihiih)
        $trr_cat = array();
    foreach ($catt as $local_cat) {
        $local_cat->name = $tree_problem . $local_cat->name;
        array_push($trr_cat, $local_cat);
    }
    return $trr_cat;
}