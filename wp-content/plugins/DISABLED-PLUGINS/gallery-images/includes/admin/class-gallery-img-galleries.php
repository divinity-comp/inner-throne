<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Gallery_Img_Galleries {

    /**
     * Load Gallerys admin page
     */
    public function load_gallery_page() {
        global $wpdb;

        $task = gallery_img_get_gallery_task();
        $id   = gallery_img_get_gallery_id();

        switch ( $task ) {
            case 'gallery_video':
                if ( ! isset( $_REQUEST['gallery_wp_nonce_video'] ) || ! wp_verify_nonce( $_REQUEST['gallery_wp_nonce_video'], 'gallery_wp_nonce_video' ) ) {
                    wp_die( 'Security check fail' );
                }
                if ( $id ) {
                    $this->insert_gallery_img_video( $id );
                } else {
                    $id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itgallery_gallerys" );
                    $this->insert_gallery_img_video( $id );
                }
                break;
            case 'edit_cat':
                if ( $id ) {
                    $this->edit_gallery( $id );
                } else {
                    $id = $wpdb->get_var( "SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itgallery_gallerys" );
                    $this->edit_gallery( $id );
                }
                break;
            case 'apply':

                $a = isset( $_REQUEST['save_data_nonce'] );
                $b = wp_verify_nonce( $_REQUEST['save_data_nonce'], 'huge_it_gallery_nonce_save_data' . $id );
                $c = wp_verify_nonce( $_REQUEST['save_data_nonce'], 'gallery_nonce_remove_image' . ( isset( $_GET['removeslide'] ) ? absint( $_GET['removeslide'] ) : '' ) );

                if ( ! ( ( $b || $c ) && $a ) ) {
                    wp_die( 'Security check fail' );
                }
                if ( $id ) {
                    $this->save_gallery_data( $id );
                    $this->edit_gallery( $id );
                }
                break;
            default:
                $this->show_galleries_page();
                break;
        }
    }

    /**
     * Shows Gallery Main Page
     */
    public function show_galleries_page() {
        if ( isset( $_COOKIE['gallery_deleted'] ) ) {
            if ( $_COOKIE['gallery_deleted'] == 'success' ) {
                ?>
                <div class="updated"><p><strong><?php _e( 'Item Deleted.', 'gallery-img' ); ?></strong></p></div>
                <?php
            } elseif ( $_COOKIE["gallery_deleted"] == 'fail' ) {
                ?>
                <div id="message" class="error"><p><?php _e('Gallery Not Deleted','gallery-img'); ?></p></div>
            <?php }
        }
        global $wpdb;
        $limit = 0;
        if ( isset( $_POST['search_events_by_title'] ) ) {
            $search_tag = esc_html( stripslashes( $_POST['search_events_by_title'] ) );
        } else {
            $search_tag = '';
        }
        $cat_row_query = "SELECT id,name FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE sl_width=0";
        $cat_row       = $wpdb->get_results( $cat_row_query );
        $query         = $wpdb->prepare( "SELECT COUNT(*) FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE name LIKE %s", "%{$search_tag}}%" );
        $total         = $wpdb->get_var( $query );
        $query         = $wpdb->prepare( "SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM " . $wpdb->prefix . "huge_itgallery_gallerys  AS a LEFT JOIN " . $wpdb->prefix . "huge_itgallery_gallerys AS b ON a.id = b.sl_width 
LEFT JOIN (SELECT  " . $wpdb->prefix . "huge_itgallery_gallerys.ordering as ordering," . $wpdb->prefix . "huge_itgallery_gallerys.id AS id, COUNT( " . $wpdb->prefix . "huge_itgallery_images.gallery_id ) AS prod_count
FROM " . $wpdb->prefix . "huge_itgallery_images, " . $wpdb->prefix . "huge_itgallery_gallerys
WHERE " . $wpdb->prefix . "huge_itgallery_images.gallery_id = " . $wpdb->prefix . "huge_itgallery_gallerys.id
GROUP BY " . $wpdb->prefix . "huge_itgallery_images.gallery_id) AS c ON c.id = a.id LEFT JOIN
(SELECT " . $wpdb->prefix . "huge_itgallery_gallerys.name AS par_name," . $wpdb->prefix . "huge_itgallery_gallerys.id FROM " . $wpdb->prefix . "huge_itgallery_gallerys) AS g
 ON a.sl_width=g.id WHERE a.name LIKE %s  group by a.id  ", "%" . $search_tag . "%" );
        $rows          = $wpdb->get_results( $query );
        $rows          = gallery_img_open_cat_in_tree( $rows );
        $query         = "SELECT  " . $wpdb->prefix . "huge_itgallery_gallerys.ordering," . $wpdb->prefix . "huge_itgallery_gallerys.id, COUNT( " . $wpdb->prefix . "huge_itgallery_images.gallery_id ) AS prod_count
FROM " . $wpdb->prefix . "huge_itgallery_images, " . $wpdb->prefix . "huge_itgallery_gallerys
WHERE " . $wpdb->prefix . "huge_itgallery_images.gallery_id = " . $wpdb->prefix . "huge_itgallery_gallerys.id
GROUP BY " . $wpdb->prefix . "huge_itgallery_images.gallery_id ";
        $prod_rows     = $wpdb->get_results( $query );
        foreach ( $rows as $row ) {
            foreach ( $prod_rows as $row_1 ) {
                if ( $row->id == $row_1->id ) {
                    $row->ordering   = $row_1->ordering;
                    $row->prod_count = $row_1->prod_count;
                }
            }
        }
        $pageNav = '';
        $sort    = '';
        $cat_row = gallery_img_open_cat_in_tree( $cat_row );

        require_once( GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'galleries-list.php' );

    }

    /**
     * Prints Gallery images after edit data
     *
     * @param $id
     *
     * @return string
     */
    public function edit_gallery( $id ) {

        global $wpdb;
        if ( isset( $_POST["huge_it_sl_effects"] ) ) {
            if ( isset( $_GET["removeslide"] ) ) {
                $idfordelete = absint( $_GET["removeslide"] );
                if ( $idfordelete ) {
                    $wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_images  WHERE id = %d ", $idfordelete ) );
                }
            }
        }
        $query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id= %d", $id );
        $row   = $wpdb->get_row( $query );
        if ( ! isset( $row->gallery_list_effects_s ) ) {
            return 'id not found';
        }
        $images    = explode( ";;;", $row->gallery_list_effects_s );
        $par       = explode( '	', $row->param );
        $count_ord = count( $images );
        $cat_row   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id!= %d and sl_width=0", $id ) );
        $cat_row   = gallery_img_open_cat_in_tree( $cat_row );
        $query     = $wpdb->prepare( "SELECT name,ordering FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE sl_width=%d  ORDER BY `ordering` ", $row->sl_width );
        $ord_elem  = $wpdb->get_results( $query );
        $query     = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = %d order by ordering ASC  ", $row->id );
        $rowim     = $wpdb->get_results( $query );
        if ( isset( $_GET["addslide"] ) ) {
            if ( $_GET["addslide"] == 1 ) {
                $table_name = $wpdb->prefix . "huge_itgallery_images";
                $sql_2      = "
INSERT INTO 
`" . $table_name . "` ( `name`, `gallery_id`, `description`, `image_url`, `sl_url`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '" . $row->id . "', '', '', '', 'par_TV', 2, '1' )";
                $wpdb->query( $sql_2 );
            }
        }
        $query      = "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys order by id ASC";
        $rowsld     = $wpdb->get_results( $query );
        $query      = "SELECT * FROM " . $wpdb->prefix . "posts where post_type = 'post' and post_status = 'publish' order by id ASC";
        $rowsposts  = $wpdb->get_results( $query );
        $rowsposts8 = '';
        $postsbycat = '';
        if ( isset( $_POST["iframecatid"] ) ) {
            $query      = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "term_relationships where term_taxonomy_id = %d order by object_id ASC", sanitize_text_field( $_POST["iframecatid"] ) );
            $rowsposts8 = $wpdb->get_results( $query );
            foreach ( $rowsposts8 as $rowsposts13 ) {
                $query      = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "posts where post_type = 'post' and post_status = 'publish' and ID = %d  order by ID ASC", $rowsposts13->object_id );
                $rowsposts1 = $wpdb->get_results( $query );
                $postsbycat = $rowsposts1;
            }
        }
        require_once( GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'gallery-images-list-html.php' );
    }

    /**
     * Edit Gallery images and data
     *
     * @param $id
     *
     * @return bool
     */
    function save_gallery_data( $id ) {
        global $wpdb;
        if ( ! ( isset( $_POST['sl_width'] ) && isset( $_POST["name"] ) ) ) {
            return false;
        }
        $cat_row = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id!= %d ", $id ) );
        $max_ord = $wpdb->get_var( 'SELECT MAX(ordering) FROM ' . $wpdb->prefix . 'huge_itgallery_gallerys' );
        $query   = $wpdb->prepare( "SELECT sl_width FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id = %d", $id );
        $id_bef  = $wpdb->get_var( $query );
        if ( isset( $_POST['gallery_img_admin_image_hover_preview'] ) ) {
            $img_hover_preview = sanitize_text_field( $_POST['gallery_img_admin_image_hover_preview'] );
            update_option( 'gallery_img_admin_image_hover_preview', $img_hover_preview );

        }
        if ( isset( $_POST["name"] )  ) {
            if ( $_POST["name"] != '' ) {
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  name = %s  WHERE id = %d ", sanitize_text_field( $_POST["name"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  sl_width = %s  WHERE id = %d ", sanitize_text_field( $_POST["sl_width"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  sl_height = %s  WHERE id = %d ", sanitize_text_field( $_POST["sl_height"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  pause_on_hover = %s  WHERE id = %d ", sanitize_text_field( $_POST["pause_on_hover"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  gallery_list_effects_s = %s  WHERE id = %d ", sanitize_text_field( $_POST["gallery_list_effects_s"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  description = %s  WHERE id = %d ", sanitize_text_field( $_POST["sl_pausetime"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  param = %s  WHERE id = %d ", sanitize_text_field( $_POST["sl_changespeed"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  sl_position = %s  WHERE id = %d ", sanitize_text_field( $_POST["sl_position"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  huge_it_sl_effects = %s  WHERE id = %d ", sanitize_text_field( $_POST["huge_it_sl_effects"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  display_type = %s  WHERE id = %d ", sanitize_text_field( $_POST["display_type"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  content_per_page = %s  WHERE id = %d ", sanitize_text_field( $_POST["content_per_page"] ), $id ) );
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  ordering = '1'  WHERE id = %d ", $id ) );
                $wpdb->query($wpdb->prepare("UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  rating = %s  WHERE id = %d ", sanitize_text_field($_POST["rating"]), $id));
                $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_gallerys SET  autoslide = %s  WHERE id = %d ", sanitize_text_field( $_POST["autoslide"] ), $id ) );
                update_option( 'gallery_img_disable_right_click', sanitize_text_field( $_POST["disable_right_click"] ), $id ) ;
            }
        }

        $query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id = %d", $id );
        $row   = $wpdb->get_row( $query );

        if ( isset( $_POST['changedvalues'] ) && $_POST['changedvalues'] != '' ) {

            $changedValues = preg_replace( '#[^0-9,]+#', '', $_POST['changedvalues'] );
            $query         = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = %d  AND id in (" . $changedValues . ")  order by id ASC", $row->id );
            $rowim         = $wpdb->get_results( $query );
            foreach ( $rowim as $key => $rowimages ) {
                $orderBy   = sanitize_text_field( $_POST[ "order_by_" . $rowimages->id ] );
                $linkTaret = sanitize_text_field( $_POST[ "sl_link_target" . $rowimages->id ] );
                $slUrl     = sanitize_text_field( str_replace( '%', '__5_5_5__', $_POST[ "sl_url" . $rowimages->id ] ) );
                $name      = wp_kses( wp_unslash( str_replace( '%', '__5_5_5__', $_POST[ "titleimage" . $rowimages->id ] ) ), 'default' );
                $desc      = wp_unslash( str_replace( '%', '__5_5_5__', $_POST[ "im_description" . $rowimages->id ] ) );
                $imageUrl  = sanitize_text_field( $_POST[ "imagess" . $rowimages->id ] );
                $like      = sanitize_text_field( $_POST[ "like_" . $rowimages->id ] );
                $dislike   = sanitize_text_field( $_POST[ "dislike_" . $rowimages->id ] );

                if ( isset( $_POST[ "order_by_" . $rowimages->id . "" ] ) && isset( $_POST[ "like_" . $rowimages->id . "" ] ) ) {
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  ordering = '%s'  WHERE ID = %d ", $orderBy, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  link_target = '%s'  WHERE ID = %d ", $linkTaret, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  sl_url = '%s' WHERE ID = %d ", $slUrl, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  name = '%s'  WHERE ID = %d ", $name, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  description = '%s'  WHERE ID = %d ", $desc, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  image_url = '%s'  WHERE ID = %d ", $imageUrl, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `like` = %d  WHERE ID = %d ", $like, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  dislike = %d  WHERE ID = %d ", $dislike, $rowimages->id ) );
                }
                if ( isset( $_POST[ "order_by_" . $rowimages->id . "" ] ) && isset( $_POST[ "heart_" . $rowimages->id . "" ] ) ) {
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  ordering = '%s'  WHERE ID = %d ", $orderBy, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  link_target = '%s'  WHERE ID = %d ", $linkTaret, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  sl_url = '%s' WHERE ID = %d ", $slUrl, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  name = '%s'  WHERE ID = %d ", $name, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  description = '%s'  WHERE ID = %d ", $desc, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  image_url = '%s'  WHERE ID = %d ", $imageUrl, $rowimages->id ) );
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  `like` = %d  WHERE ID = %d ", $like, $rowimages->id ) );
                }
            }
        }
        if ( isset( $_POST["imagess"] ) ) {
            if ( $_POST["imagess"] != '' ) {
                $query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "huge_itgallery_images where gallery_id = %d order by id ASC", $row->id );
                $rowim = $wpdb->get_results( $query );
                foreach ( $rowim as $key => $rowimages ) {
                    $orderingplus = $rowimages->ordering + 1;
                    $wpdb->query( $wpdb->prepare( "UPDATE " . $wpdb->prefix . "huge_itgallery_images SET  ordering = %d  WHERE ID = %d ", $orderingplus, $rowimages->id ) );
                }
                $table_name        = $wpdb->prefix . "huge_itgallery_images";
                $imagesnewuploader = explode( ";;;", $_POST["imagess"] );
                array_pop( $imagesnewuploader );
                foreach ( $imagesnewuploader as $imagesnewupload ) {
                    $sql_2 = "
INSERT INTO 
`" . $table_name . "` ( `name`, `gallery_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '" . $row->id . "', '', '" . $imagesnewupload . "', '', 'image', 'on', 'par_TV', 2, '1' )";
                    $wpdb->query( $sql_2 );
                }
                $query            = $wpdb->prepare( "SELECT * FROM " . $table_name . " WHERE gallery_id=%d ORDER BY ordering", $id );
                $gallery_images = $wpdb->get_results( $query );
                $i                = 0;
                foreach ( $gallery_images as $gallery_image ) {
                    $wpdb->update(
                        $table_name,
                        array( 'ordering' => $i ),
                        array( 'id' => $gallery_image->id )
                    );
                    $i ++;
                }
            }
        }
        ?>
        <div class="updated"><p><strong><?php _e( 'Item Saved' ); ?></strong></p></div>
        <?php
        return true;

    }

    /**
     * Insert Gallery video
     *
     * @param $id
     */
    function insert_gallery_img_video( $id ) {
        require_once( GALLERY_IMG_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'video-add-html.php' );
    }

    /**
     * Removes Gallery
     *
     * @param $id
     */
    function remove_gallery( $id ) {
        global $wpdb;
        $sql_remov_tag = $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "huge_itgallery_gallerys WHERE id = %d", $id );
        if ( ! $wpdb->query( $sql_remov_tag ) ) {
            ?>
            <div id="message" class="error"><p>Gallery Deleted</p></div>
            <?php
        } else {
            ?>
            <div class="updated"><p><strong><?php _e( 'Item Deleted.' ); ?></strong></p></div>
            <?php
        }
    }
}


