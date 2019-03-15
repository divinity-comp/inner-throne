<?php

//Defer Parsing Of JavaScript (#32 from http://www.onlinemediamasters.com/why-is-wordpress-so-slow/#host-google-analytics-locally)

/* Creates problems with ACF
if (!(is_admin() )) {
function defer_parsing_of_js ( $url ) {
if ( FALSE === strpos( $url, '.js' ) ) return $url;
if ( strpos( $url, 'jquery.js' ) ) return $url;
// return "$url' defer ";
return "$url' defer onload='";
}
add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
}
*/


function shortcode_membership_buy() {
    $users = get_users();
    $vanguard_count = 0;
    $high_level_users = 4; //admins etc
    $available_users = get_field('kotv_seats_available','options');
    foreach($users as $user) {
        if(user_can($user->ID, access_optimizemember_level4)) {
            $vanguard_count++;
        }
    }
    $vanguard_count -= $high_level_users;
    $vanguard_users_remaining = $available_users - $vanguard_count;
    if($vanguard_count < $available_users) {
    //$echo .= "<p class='vanguard_message'>We have a limited number of new seats available each month.";
    //$echo .= "<p class='vanguard_message'><em>" . $vanguard_users_remaining . " seats left</em> this month</p>";
    $echo .= do_shortcode('[optimizeMember-PayPal-Button level="4" ccaps="" desc="Inner Circle membership." ps="ryit" lc="" cc="USD" dg="0" ns="1" custom="hwww.inner-throne.com" success="http://www.inner-throne.com/inner-circle-thank-you" ta="9" tp="30" tt="D" ra="49" rp="1" rt="M" rr="1" rrt="" rra="1" image="http://www.inner-throne.com/wp-content/uploads/2015/08/join-now-button.png" output="button" /]');
    $echo .= "<p class='vanguard_message small'>Clicking button will take you to Paypal";
    } else {
    $echo .= "<p class='vanguard_message'>No seats left this month. Register for the mailing list below to be the first to know when new seats open.</p>";
    $echo .= "<div class='createsend-button' style='height:27px;display:inline-block;' data-listid='r/48/355/9C7/4E063E55B9C8AD18'>
    </div><script type='text/javascript'>(function () { var e = document.createElement('script'); e.type = 'text/javascript'; e.async = true; e.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://btn.createsend1.com/js/sb.min.js?v=3'; e.className = 'createsend-script'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(e, s); })();</script>";
    }

    return $echo;
}

add_shortcode( 'membership_buy' , 'shortcode_membership_buy' );

//acf_add_options_page( $page );
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(); 
}

function ryit_initialize() {
    if(is_front_page()) {
        ryit_next_upcoming_event();
    }
}

add_action('wp_loaded', 'ryit_initialize');

function ryit_list_archives($archive) {
    $args = array(
        'type' => 'monthly',
        'show_post_count' => false,
        'format' => 'html',
        'echo' => false
    );

    $archive = wp_get_archives( $args );

    return "<ul>" . $archive . "</ul>";
}

add_shortcode('ryit_list_archives', 'ryit_list_archives');

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin() && !current_user_can('editor')) {
      show_admin_bar(false);
    }
}

// Disable WP Rocket Lazy load on archive pages (it causes prbolems with Avada grid)
add_filter( 'wp', '__deactivate_rocket_lazyload' );
function __deactivate_rocket_lazyload() {
    if( is_archive() ) {
        add_filter( 'do_rocket_lazyload', '__return_false' );
    }
}



function shortcode_latest_blog() {
    $echo = "";

    $latest_blog = get_posts('category_name=blog&showposts=1');

    foreach($latest_blog as $blog) {
        $echo = "<h3>Latest blog post:</h3>" . "<p><a href='" . get_permalink($blog->ID) . "'>" . get_the_title($blog->ID) . "</a> <span style='text-align: right;'>(" . get_the_date('l, F j, Y', $blog->ID) . ")</span></a></p>";
    }

    return $echo;
}

add_shortcode( 'latest_blog' , 'shortcode_latest_blog' );


function ryit_testimonials() {
  $echo = "";

  $testimonials = get_posts('post_type=testimonials&showposts=-1&orderby=rand');

  $index = 1;
  foreach($testimonials as $testimonial) {
      if($index % 2 != 0) $echo .= "<div class='row'>";   
      $echo .= "<div class='testimonial'>";
      $echo .= "<img src='" . get_field('ryit_testimonial_portrait', $testimonial->ID) . "' />";
      $echo .= "<div class='text'>";
      $echo .= "<blockquote>" . get_field('ryit_testimonial_text', $testimonial->ID) . "</blockquote>";
      $echo .= "<cite>" . get_the_title($testimonial->ID) . "</cite>";    
      if($index % 2 == 0) $echo .= "</div>";      
      $echo .= "</div></div>";
      $index++;
  }

  return "<div class='testimonials'>" . $echo . "</div>";
}

add_shortcode( 'ryit_testimonials' , 'ryit_testimonials' );



function ryit_video_list($atts) {

  $type = $atts['type'];
  if(empty($type)) {
    $type = "inspiring";
  }

  global $post;

/*
  var_dump($test);
  return print_r($test);
*/

  /*
  $subscription_id = rcp_get_subscription_id( get_current_user_id() );
  if( $subscription_id == 2 ) {
    // do something here
  }*/
  
  //$echo = "<div class='video_list'>";

  if($type == "inspiring") {
    $videos = get_field('ryit_inspiring_videos', $post->ID);
  }
  else if($type == "coaching") {
    $videos = get_field('ryit_coaching_videos', $post->ID); 
  }

  $video_count = count($videos);
  $video_index = 1;


  foreach ($videos as $video) {

    //First Column
    
    $echo .= '<div class="fusion-layout-column fusion_builder_column fusion_builder_column_2_3  fusion-two-third fusion-column-first 2_3" style="margin-top:0px;margin-bottom:20px;width:66.66%;width:calc(66.66% - ( ( 4% ) * 0.6666 ) );margin-right: 4%;">';
    $echo .= '<div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left   top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">';
    $echo .= '<div class="fusion-video" style="max-width:700px;max-height:390px;"><div class="video-shortcode"><div class="fluid-width-video-wrapper">';
    

    if($video['type'] == "youtube") {
      $echo .= '<iframe src="https://www.youtube.com/embed/' . $video['id'] . '?rel=0&amp;controls=1&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe><div class="fusion-clearfix"></div>';
    }
    elseif($video['type'] == "vimeo") {
      $echo .= '<iframe src="https://player.vimeo.com/video/' . $video['id'] . '" width="640" height="360" frameborder="0" allowFullScreen mozallowfullscreen webkitAllowFullScreen></iframe><div class="fusion-clearfix"></div>';
    } 

    $echo .= '</div></div></div>';
    //$echo .= '</div>';
    $echo .= '<div class="fusion-clearfix"></div></div></div>';


    //Second column (text etc)

    $echo .= '<div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_3  fusion-one-third fusion-column-last 1_3" style="margin-top:0px;margin-bottom:20px;width:33.33%;width:calc(33.33% - ( ( 4% ) * 0.3333 ) );">';  
    $echo .= '<div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">';
    $echo .= '<div class="fusion-title title fusion-sep-none fusion-title-size-three fusion-border-below-title" style="margin-top:0px;margin-bottom:15px;"><h3 class="title-heading-left">' . $video['title'] . '</h3></div>';
    $echo .= '<div class="fusion-text">' . wpautop($video['text']) . '</div></div></div>';

/*
    $echo .= '<div class="video-text">' . wpautop($video['text']) . '</div>';
    $echo .= '</div>';
*/

    //Break apart videos
    
    if($video_index < $video_count ) {
      $echo .= '<div class="fusion-clearfix" style="padding-bottom: 35px;"></div>';
    }
    
    $video_index++;
  }

  //$echo .= '</div>';

  if($type=="inspiring") {
    return $echo . '<div class="fusion-clearfix test" style="padding-bottom: 35px;"></div>';
  }
  else { //coaching videos
    $subscription_id = rcp_get_subscription_id(get_current_user_id());
    if( $subscription_id != 3 && !current_user_can('edit_pages')) {
      return '<p style="text-align: center;">Recordings of coaching calls are only visible<br/> to members of the current Fellowship.</p><div class="fusion-clearfix" style="padding-bottom: 120px;"></div>';
    }
    return $echo . '<div class="fusion-clearfix" style="padding-bottom: 100px;"></div>'; 
  }
}

add_shortcode('ryit_video_list', 'ryit_video_list');




/* Remove menu elements reserved for logged in users for those who are not */

add_action('wp_footer','ryit_scripts');

function ryit_scripts() {
    $output = "
    <script type='text/javascript'>
        jQuery(document).ready(function($) {
            if(!jQuery('body').hasClass('logged-in')) {
                jQuery('.logged-in-show').remove();
            }
        });
    </script>
    ";

    echo $output;
}

/* End remove menu elements */


add_action('wp_head','ryit_favicon');

function ryit_favicon() {
    echo '<code style="overflow: scroll;"><link rel="shortcut icon" href="https://www.inner-throne.com/favicon.ico" type="image/ico" /></code>';
}



function ryit_custom_post_types() {

     $supports = array('title');

     $labels = array(
          'name' => 'Testimonials',
          'singular_name' => 'Testimonial',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New Testimonial',
          'edit_item' => 'Edit Testimonial',
          'new_item' => 'New Testimonial',
          'view_item' => 'View Testimonial',
          'search_items' => 'Search Testimonials',
          'not_found' => 'No testimonials found',
          'not_found_in_trash' => 'No testimonials found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Testimonials'
     );


     $args = array(
          'label' => 'Testimonials',
          'public' => true,
          'show_ui' => true,
          'hierarchial' => false,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
          'rewrite' => true,
          'supports' => $supports,
          'labels' => $labels
     );

     register_post_type( 'testimonials' , $args );

     $labels = array(
          'name' => 'Triad',
          'singular_name' => 'Triad',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New Triad',
          'edit_item' => 'Edit Triad',
          'new_item' => 'New Triad',
          'view_item' => 'View Triad',
          'search_items' => 'Search Triads',
          'not_found' => 'No triads found',
          'not_found_in_trash' => 'No triads found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Triads'
     );


     $args = array(
          'label' => 'Triads',
          'public' => true,
          'show_ui' => true,
          'hierarchial' => false,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
          'rewrite' => true,
          'supports' => $supports,
          'labels' => $labels
     );

     register_post_type( 'triads' , $args );

     $labels = array(
          'name' => 'RYIT popups',
          'singular_name' => 'RYIT popup',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New popup',
          'edit_item' => 'Edit popup',
          'new_item' => 'New popup',
          'view_item' => 'View popup',
          'search_items' => 'Search popups',
          'not_found' => 'No popups found',
          'not_found_in_trash' => 'No popups found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Popups'
     );


     $args = array(
          'label' => 'RYIT popups',
          'public' => true,
          'show_ui' => true,
          'hierarchial' => false,
          'show_in_menu' => true,
          'menu_position' => 5,
          'rewrite' => true,
          'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
          'supports' => $supports,
          'labels' => $labels
     );

     register_post_type( 'ryit_popup' , $args );
}

add_action( 'init', 'ryit_custom_post_types' );


/**************************************/
/****** ADD MOBILE DETECT CLASS *******/
/**************************************/


require_once 'includes/Mobile_Detect.php';
$detect = new Mobile_Detect;



/*********************************/
/****** CUSTOM RYIT POPUPS *******/
/*********************************/

function ryit_popup($popup_id) {
    
    global $post;
    $page_ids = array(44306,4711,46119,54255,44199,44884,44702,44135); //pages to show popup
    if(is_front_page() || is_archive() || in_array($post->ID, $page_ids)) {
        $delay_in_ms = 15000;
    }
    else if(is_single()) {
        $delay_in_ms = 30000;
    }
    else {
        return false; //hide popup on anything but front page and blog posts
    }

    //Read array_values(input)
    $title_1 = get_field('ryit_popup_title_1', $popup_id);
    $title_2 = get_field('ryit_popup_title_2', $popup_id);
    $text = get_field('ryit_popup_text',$popup_id);
    $bullets = get_field('ryit_popup_bullets', $popup_id);

    if($bullets) {
      foreach($bullets as $bullet) {
          $popup_bullets = $bullet;
      }
    }

    $echo = "<div id='ryit_popup_overlay' class='hidden'></div>";
    $echo .= "<div id='ryit_popup' class='hidden'>";
    $echo .= "<div class='innerwrap'>";
    $echo .= "<div class='logo'></div>";

    // Step #1
    $echo .= "<div class='step step_one'>";
    $echo .= "<div class='content'>";
    $echo .= "<h2>" . $title_1 . "</h2>";
    $echo .= "<h3>" . $title_2 . "</h3>";
    if(!empty($text)) :
    $echo .= "<div id='ryit_popup_text'>" . $text . "</div>";
    endif;
    $echo .= "</div>"; //end content
    $echo .= "<div class='ui'>";
    $echo .= "<ul><li class='yes'><a href='#'>Yes</a></li><li class='sep-text'>Or</li><li class='no'><a href='#'>No</a></li></ul>";
    $echo .= "</div>"; //end ui
    $echo .= "</div>"; //end step


    //Step #2
    $echo .= "<div class='step step_two'>";
    $echo .= "<div class='content'><h3>Good Call! :)</h3><p>Register below and we&rsquo;ll send you this powerful document designed to <strong>boost your POWER</strong> and set you on the Path to Sovereignty. We think you'll love it! :)</p></div>";
    $echo .= "<div class='ui'>";
    $echo .= "<div class='kartra_optin_containere4da3b7fbbce2345d7772b0674a318d5'></div><script src='https://app.kartra.com/optin/XZhs5bwnzu9c'></script>";
    /*
    $echo .= '<form action="https://masculinity-movies.createsend.com/t/r/s/' . $mailing_list . '/" method="post" id="subForm" class="clearfix">
            <div><input type="text" name="cm-name" placeholder="Your first name" id="name" /></div>
            <div><input type="email" spellcheck="false" name="cm-hjtykuu-' . $mailing_list . '" id="email" placeholder="Your email address" /></div>';
            if($opt_in) :
                if($opt_in_hidden) :
                    $echo .= '<div><input id="listdtuldjd" name="' . $opt_in . '" type="checkbox" checked="checked" style="display: none;" /></div>';
                else :
                    $echo .= '<p>
                <label>Opt into another list</label><br />
                <input id="listdtuldjd" name="' . $opt_in . '" type="checkbox" /> <label for="listdtuldjd">' . $opt_in_title . '</label>
                </p>';
                endif;
            endif;
    $echo .= '<button type="submit" value="Subscribe">Get Access NOW</button>
        </form>';*/
    $echo .= '</div>'; //end ui
    $echo .= '</div>'; //end wrap
    $echo .= '<a href="#" class="close"><span class="fa fa-times"></span></a>'; //end wrap

    //end of wrapper
    $echo .= "</div>";

    //Wrap up popups
    $echo .= "</div>";

    $echo .= '<script type="text/javascript">
        $j = jQuery.noConflict();

        $j(document).ready(function() {

          //set up cookies
          var cookie = Cookies.get("ryit_popup_' . $popup_id . '");
          console.log("cookie is: " + cookie);

          if(cookie == null || cookie == "undefined") {
              console.log("no cookie");
              var delay = ' . $delay_in_ms . ';
              setTimeout(function() {
                  Cookies.set("ryit_popup_' . $popup_id . '", "true", { expires: 30, path: "/" });   
                  $j("#ryit_popup_overlay").removeClass("hidden");
                  $j("#ryit_popup_overlay").addClass("visible");
                  $j("#ryit_popup").removeClass("hidden");
                  $j("#ryit_popup").addClass("visible");
                  var scrollTop = $j(document).scrollTop();
                  $j("#ryit_popup").css("top", scrollTop + 150);

              }, delay);
          }


          $j(".ui .yes a").on("click", function(e) {
              e.preventDefault();
              $j(".step_two").fadeIn(500, function() {
                  $j(".step_one").fadeOut(500);
              });
          });


          $j("#ryit_popup a.close").on("click", function(e) {
              e.preventDefault();
              $j("#ryit_popup_overlay").clearQueue();
              $j("#ryit_popup").clearQueue();
              $j("#ryit_popup_overlay").fadeOut(500);
              $j("#ryit_popup").fadeOut(500);
          });

          $j(".ui .no a").on("click", function(e) {
              e.preventDefault();
              $j("#ryit_popup .content").html("<h3>Got it!</h3><p>If you should change your mind, you can find the Free E-Book in the \"Our Offerings\"-menu! We think you will like it :)</p>");
              $j("#ryit_popup .ui").fadeOut(500);
              $j("#ryit_popup_overlay").delay(8000).fadeOut(500);
              $j("#ryit_popup").delay(8000).fadeOut(500);
          });

          $j(function () {
            $j("#subForm").submit(function (e) {
              e.preventDefault();
              $j("#email").removeClass();
              $j(".modal button").fadeTo(200,0.5);
              $j.getJSON(
              this.action + "?callback=?",
              $j(this).serialize(),
              function (data) {
                  $j("button").stop().fadeTo(200,1);
                  if (data.Status === 400) { 
                      if($j("#name").val() === "") {
                          $j("#name").parent().addClass("error");
                      } else {
                         $j("#name").parent().removeClass("error"); 
                      }
                      $j("#email").parent().addClass("error"); 
                  } else { 
                      $j("#ryit_popup .content").html("' . $sub_response . '");
                      $j("#ryit_popup .ui").fadeOut(300);
                  }
              });
            });
          });
        });
    </script>';

    echo $echo;
    //echo "<h2>modal wrap</h2>";
}


/*************************************/
/****** INITIALIZE RYIT PAGES ********/
/*************************************/


function is_ryit_page() {
  global $post;
  $ryit_home_page = 45618;
  if(is_page($ryit_home_page) || ($post->post_parent == $ryit_home_page)) {
    return true;
  }
}


/* SET UP STYLES */

function ryit_body_classes() {
  global $post;
  $classes = array();

  if(is_front_page()) {
      $classes[] = "home";
  }

  if(is_single()) {
      $classes[] = "single";
  }

  $classes[] = sanitize_title(get_the_title());

  $body_width = get_field('ryit_body_width');
  if($body_width != "default" && !empty($body_width)) :
      if ($body_width == "small") :
          $classes[] = "content-width-small";
      elseif ($body_width == "medium") :
          $classes[] = "content-width-medium";
      elseif ($body_width == "large") :
          $classes[] = "content-width-large";
      endif;
  endif;

  $splash_page = get_field('ryit_splash_page');
  if(!empty($splash_page)) {
    if($splash_page[0] == "yes") {
        $classes[] = "splash-page";
    }
  }

  //admin class
  if(current_user_can('administrator')) {
      $classes[] = "administrator";
  }

  //category classes
  $cats = get_the_category( $post->ID );
  foreach($cats as $cat) {
      //var_dump($cat);
      $classes[] = "category-" . $cat->slug;
  }

  //Restrict content pro 
  $subscription_id = rcp_get_subscription_id( get_current_user_id() );

  //logged in class
  if(is_user_logged_in()) { 
      $classes[] = "logged-in";
      if(current_user_can('editor') || current_user_can('administrator')) {
          $classes[] = "membership-level-1";
          $classes[] = "membership-level-2";
          $classes[] = "membership-level-3";
          $classes[] = "membership-level-4";
      }
      else {
          $classes[] = "membership-level-" . $subscription_id;
      }
  }
  else {
      $classes[] = "logged-out";
  }

  /* Journey to the Great Self class */

  if(is_jgs_page()) {
      $classes[] = "jgs-course-page";
      if(jgs_user_has_answered()) {
      $classes[] = "jgs-access-granted";
      }
  }
 
  return $classes;
}

add_filter( 'body_class', 'ryit_body_classes' );


function ryit_add_entrepreneur_track() {
  ob_start();
  if(is_ryit_page()) :
?>
  <!-- RYIT Javascripts -->
  <script type="text/javascript">   
    console.log("JAVASCRIPT IS ACTIVE");
    //Set up AJAX 
    
    var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

    jQuery(document).ready(function($j){
      $j(document).on('click', '#enable-entrepreneur-track', function(e) {
        e.preventDefault();

        var data = {
          action : 'ryit_change_acf_field_value',
          field_key : 'field_5bbb7710d14c0',  
          post_id : 'user_' + $j('body').attr('user_id'),
          new_value : 1
        };

        console.log('This is a test');

        $j.ajax({
          url: ajaxurl,
          type: 'GET', // the kind of data we are sending
          data: data,        
          dataType: 'json',
          success: function(response) {
            console.log('ajax is run');
            $j('.entrepreneur-track-enable').fadeOut();
            $j('.entrepreneur-track').fadeIn();      
          }
        });
        
      });
    });
  </script>
  <!-- End RYIT Javascripts -->
<?php
  endif;
  echo ob_get_clean();
}
add_action('wp_head', 'ryit_add_entrepreneur_track');


function remove_admin_access_for_non_editor() {
if ( is_admin() && ! current_user_can( 'edit_posts' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
    wp_redirect( home_url() );
    exit;
    }
}
add_action( 'init', 'remove_admin_access_for_non_editor' );


//Add backend styles
function ryit_add_editor_styles() {
    add_editor_style( '../plugins/ryit/ryit-editor-styles.css');
}
add_action( 'after_setup_theme', 'ryit_add_editor_styles' );


//from https://wpcurve.com/wordpress-speed/ - remove query strings from static resources
function ewp_remove_script_version( $src ){
  return remove_query_arg( 'ver', $src );
}
add_filter( 'script_loader_src', 'ewp_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'ewp_remove_script_version', 15, 1 );


function ryit_wordpress_comments() {
    //return wp_list_comments( 'type=comment&callback=mytheme_comment&echo=0' );
}

function mytheme_comment($comment, $args, $depth) {

    if ( 'div' === $args['style'] ) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    ?>

    <?php
        var_dump(get_user_by_email(get_comment_author_email()));
    ?>

    <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
    <?php if ( 'div' != $args['style'] ) : ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
    <?php endif; ?>
    <div class="comment-author vcard">
        <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
        <?php printf( __( '<cite class="fn">%s</cite> <span class="says">says:</span>' ), get_comment_author_link() ); ?>
    </div>
    <?php if ( $comment->comment_approved == '0' ) : ?>
         <em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></em>
          <br />
    <?php endif; ?>

    <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>">
        <?php
        /* translators: 1: date, 2: time */
        printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)' ), '  ', '' );
        ?>
    </div>

    <?php comment_text(); ?>

    <div class="reply">
        <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
    </div>
    <?php if ( 'div' != $args['style'] ) : ?>
    </div>
    <?php endif; ?>
    <?php
}



add_shortcode( 'ryit-wordpress-comments' , 'ryit_wordpress_comments' );


add_action('admin_init', 'disable_dashboard');

function disable_dashboard() {
    if (!current_user_can( 'publish_posts') && !defined( 'DOING_AJAX' )) {
        wp_redirect(home_url());
        exit;
    }
}

/****************************************************/
/******* RESTRICT CONTENT PRO PAGE ACCESS FIX *******/
/* PREVENTS DB SLOWOWN BUT STILL DENIES PAGE ACCESS */
/****************************************************/


function ag_rcp_redirect_from_restricted_post() {
  global $rcp_options, $post;
  $member = new RCP_Member( get_current_user_id() );
  // Bail if we're not on a single post/page.
  if ( ! is_singular() ) {
    return;
  }
  // Bail if current user has permission to view this post/page.
  if ( $member->can_access( $post->ID ) ) {
    return;
  }
  $redirect_page_id = $rcp_options['redirect_from_premium'];
  // Use chosen redirect page, or homepage if not set.
  $redirect_url = ( ! empty( $redirect_page_id ) && $post->ID != $redirect_page_id ) ? get_permalink( $redirect_page_id ) : home_url();
  wp_redirect( $redirect_url );
  exit;
}

add_action( 'template_redirect', 'ag_rcp_redirect_from_restricted_post', 999 );




/*

function appSumo() {
?>
<script src="//load.sumome.com/" data-sumo-site-id="03c434f78d73cc34fea09f34fab1bf85e8a02f27fedb4eee40151a37c8cd412f" async="async"></script>
<?php
}

add_action('wp_head', 'appSumo');
*/


function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
    $lang = get_stylesheet_directory() . '/languages';
    load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );


function ryit_add_script() {
    wp_register_script('ryit_script', get_stylesheet_directory_uri() . '/ryit-scripts.js', '', '', true);
    wp_enqueue_script('ryit_script');
} 

add_action( 'wp_enqueue_scripts', 'ryit_add_script', 999 ); 


// Our custom post type function
function create_coursepage_posttype() {
    $labels = array(
        'name' => __( 'Course page' ),
        'singular_name' => __( 'Course page' ),
        'menu_name' => __('Course page'),
        'parent_item_colon' => __('Course page'),
        'all_items' => __('Course pages'),
        'view_item' => __('View course page'),
        'add_new_item' => __('Add new course page'),
        'add_new' => __('Add new'),
        'edit_item' => __('Edit course page'),
        'update_item' => __('Edit course page'),
        'search_items' => __('Search course pages'),
        'not_found' => __('Not found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    $args = array(
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'hierarchical' => true,
        'rewrite' => array(
                 'slug'       => 'initiation', // if you need slug
                 'with_front' => false,
                 ),
        'labels' => $labels,
        'taxonomies'  => array( 'course-type' ),
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes' ),
        'can_export' => true,
        'publicly_queryable'  => true,
    );  

    // Registering your Custom Post Type
    register_post_type( 'course-page', $args );
}

// Hooking up our function to theme setup

add_action( 'init', 'create_coursepage_posttype' );


//Advanced Custom Fields override
//add_filter('acf/settings/remove_wp_meta_box', '__return_false'); //don't remove custom fields meta box


// Our custom post type function
function create_blog_posttype() {
    $labels = array(
        'name' => __( 'Blog' ),
        'singular_name' => __( 'Blog' ),
        'menu_name' => __('Blog'),
        'parent_item_colon' => __('Parent blog'),
        'all_items' => __('All blogs'),
        'view_item' => __('View blog'),
        'add_new_item' => __('Add new blog'),
        'add_new' => __('Add new'),
        'edit_item' => __('Edit blog'),
        'update_item' => __('Edit blog'),
        'search_items' => __('Search blogs'),
        'not_found' => __('Not found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    $args = array(
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => true,
        'rewrite' => array(
                 'slug'       => 'blog', // if you need slug
                 'with_front' => false,
                 ),
        'labels' => $labels,
        'taxonomies'  => array( 'category', 'post_tag' ),
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'can_export' => true,
        'publicly_queryable'  => true,
    );  

    // Registering your Custom Post Type
    register_post_type( 'blog', $args );
}

// Hooking up our function to theme setup

add_action( 'init', 'create_blog_posttype' );


function create_icblog_posttype() {
    $labels = array(
        'name' => __( 'IC Blog' ),
        'singular_name' => __( 'IC Blog' ),
        'menu_name' => __('IC Blog'),
        'parent_item_colon' => __('Parent IC blog'),
        'all_items' => __('All IC blogs'),
        'view_item' => __('View IC blog'),
        'add_new_item' => __('Add new IC blog'),
        'add_new' => __('Add new'),
        'edit_item' => __('Edit IC blog'),
        'update_item' => __('Edit IC blog'),
        'search_items' => __('Search IC blogs'),
        'not_found' => __('Not found'),
        'not_found_in_trash' => __('Not found in Trash'),
    );

    $args = array(
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'has_archive' => true,
        'labels' => $labels,
        'taxonomies'  => array( 'ic-category', 'post_tag' ),
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'rewrite' => array('slug' => 'ic-blog'),
        'can_export'          => true,
        'publicly_queryable'  => true,
    );  

    // Registering your Custom Post Type
    register_post_type( 'inner-circle-blog', $args );
}

// Hooking up our function to theme setup

add_action( 'init', 'create_icblog_posttype' );

function namespace_add_custom_types( $query ) {
  if( is_category() || is_tag() && empty( $query->query_vars['suppress_filters'] ) ) {
    $query->set( 'post_type', array(
     'post', 'nav_menu_item', 'blog'
        ));
      return $query;
    }
}
add_filter( 'pre_get_posts', 'namespace_add_custom_types' );


//ADD IC taxonomy

// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'ryit_create_taxonomies', 0 );

function ryit_create_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Categories', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Categories', 'textdomain' ),
        'all_items'         => __( 'All categories', 'textdomain' ),
        'parent_item'       => __( 'Parent categories', 'textdomain' ),
        'parent_item_colon' => __( 'Parent category:', 'textdomain' ),
        'edit_item'         => __( 'Edit category', 'textdomain' ),
        'update_item'       => __( 'Update category', 'textdomain' ),
        'add_new_item'      => __( 'Add new category', 'textdomain' ),
        'new_item_name'     => __( 'New category', 'textdomain' ),
        'menu_name'         => __( 'IC category', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'genre' ),
    );

    register_taxonomy( 'ic-category', array( 'ic-blog' ), $args );

    /* Register course page taxonomy */

    $labels = array(
        'name'              => _x( 'Course types', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Course types', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Course types', 'textdomain' ),
        'all_items'         => __( 'All course types', 'textdomain' ),
        'parent_item'       => __( 'Parent course types', 'textdomain' ),
        'parent_item_colon' => __( 'Parent course type:', 'textdomain' ),
        'edit_item'         => __( 'Edit course type', 'textdomain' ),
        'update_item'       => __( 'Update course type', 'textdomain' ),
        'add_new_item'      => __( 'Add new course type', 'textdomain' ),
        'new_item_name'     => __( 'New course type', 'textdomain' ),
        'menu_name'         => __( 'Course type', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'course-type' ),
    );

    register_taxonomy( 'course-type', array( 'course-page' ), $args );  
}


/****************************************/
/********* BBpress FUNCTIONS ************/
/****************************************/


function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );

function bbp_tinymce_paste_plain_text( $plugins = array() ) {
    $plugins[] = 'paste';
    return $plugins;
}
add_filter( 'bbp_get_tiny_mce_plugins', 'bbp_tinymce_paste_plain_text' );

add_filter( 'bbp_after_get_the_content_parse_args', 'bavotasan_bbpress_upload_media' );
/**
 * Allow upload media in bbPress
 *
 * This function is attached to the 'bbp_after_get_the_content_parse_args' filter hook.
 */
function bavotasan_bbpress_upload_media( $args ) {
  $args['media_buttons'] = true;

  return $args;
}


/****************************************/
/*********** EDD FUNCTIONS **************/
/****************************************/


/**
 * Variable price output
 *
 * Outputs variable pricing options for each download or a specified downloads in a list.
 * The output generated can be overridden by the filters provided or by removing
 * the action and adding your own custom action.
 *
 * @since 1.2.3
 * @param int $download_id Download ID
 * @return void
 */
function ryit_purchase_variable_pricing( $download_id = 0, $args = array() ) {
    global $edd_displayed_form_ids;

    // If we've already generated a form ID for this download ID, append -#
    $form_id = '';
    if ( $edd_displayed_form_ids[ $download_id ] > 1 ) {
        $form_id .= '-' . $edd_displayed_form_ids[ $download_id ];
    }

    $variable_pricing = edd_has_variable_prices( $download_id );

    if ( ! $variable_pricing ) {
        return;
    }

    $prices = apply_filters( 'edd_purchase_variable_prices', edd_get_variable_prices( $download_id ), $download_id );

    // If the price_id passed is found in the variable prices, do not display all variable prices.
    if ( false !== $args['price_id'] && isset( $prices[ $args['price_id'] ] ) ) {
        return;
    }

    $type   = edd_single_price_option_mode( $download_id ) ? 'checkbox' : 'radio';
    $mode   = edd_single_price_option_mode( $download_id ) ? 'multi' : 'single';
    $schema = edd_add_schema_microdata() ? ' itemprop="offers" itemscope itemtype="http://schema.org/Offer"' : '';

    // Filter the class names for the edd_price_options div
    $css_classes_array = apply_filters( 'edd_price_options_classes', array(
        'edd_price_options',
        'edd_' . esc_attr( $mode ) . '_mode'
    ), $download_id );

    // Sanitize those class names and form them into a string
    $css_classes_string = implode( array_map( 'sanitize_html_class', $css_classes_array ), ' ' );

    if ( edd_item_in_cart( $download_id ) && ! edd_single_price_option_mode( $download_id ) ) {
        return;
    }


    // show longer payment plans on enroll page
    $var = $_GET['payment_plan'];
    if($var && $var == 9) {
        $echo .= "<style type='text/css'>";
        $echo .= "#edd_price_option_657_9-monthplan { display: table-row !important; }";
        $echo .= "</style>";
    }
    else if($var && $var == 12) {
        $echo .= "<style type='text/css'>";
        $echo .= "#edd_price_option_657_9-monthplan, #edd_price_option_657_12-monthplan { display: table-row !important; }";
        $echo .= "</style>";        
    }
    else if($var && $var == "custom") {
        $echo .= "<style type='text/css'>";
        $echo .= "#edd_price_option_657_josephcasansplan { display: table-row !important; }";
        $echo .= "</style>";        
    }
    echo $echo;

    //RYIT download id = 657
    //Donation download it = 46649

    do_action( 'edd_before_price_options', $download_id ); ?>
    <table class="<?php echo esc_attr( rtrim( $css_classes_string ) ); ?>">
        <thead>
        <?php if($download_id != 46649) : ?>
            <th>Plan type</th>
            <th>Price & plan info</th>
        <?php else : ?>
            <th>Donation type</th>
            <th>Donation size</th>
        <?php endif; ?>
        </thead>
        <tbody>
            <?php
              
                if($download_id == 657) {
                    $product_price = edd_get_variable_prices( 657 );
                    $product_price = $product_price[1]['amount'];                     
                }
                elseif($download_id == 55179) {
                    $product_price = 329;
                }
                else {
                    $product_price = 100000;
                }

             if ( $prices ) :
                $checked_key = isset( $_GET['price_option'] ) ? absint( $_GET['price_option'] ) : edd_get_default_variable_price( $download_id );

                foreach ( $prices as $key => $price ) :

                    if($price['recurring']=='yes') { //item has recurring payments
                        $price_desc = "";
                        $price_total = $price['amount'] * $price['times'] + $price['signup_fee'];
                        $price_msg =  "$" . strval($price['amount']) . " x " . strval($price['times']);
                        if($price['signup_fee']) { 
                            $price_desc .= " + $" . $price['signup_fee'] . " extra first month<sup>*</sup>. ";
                        }
                        /*
                        else {
                            $price_desc .= " &ndash; ";
                        }*/
                        if($price_total > $product_price) {
                            $price_desc .= " ($" . strval($price_total-$product_price) . " plan fees, $" . $price_total . " total)";
                        }
                    }
                    else {
                        $price_msg = "$" . $price['amount'];
                        //$price_desc = " &ndash; One payment. No fees.";
                    }

                    echo '<tr id="edd_price_option_' . $download_id . '_' . sanitize_key( $price['name'] ) . $form_id . '"' . $schema . '>';
                        echo '<td class="plan">';
                        echo '<label for="' . esc_attr( 'edd_price_option_' . $download_id . '_' . $key . $form_id ) . '">';
                        echo '<input type="' . $type . '" ' . checked( apply_filters( 'edd_price_option_checked', $checked_key, $download_id, $key ), $key, false ) . ' name="edd_options[price_id][]" id="' . esc_attr( 'edd_price_option_' . $download_id . '_' . $key . $form_id ) . '" class="' . esc_attr( 'edd_price_option_' . $download_id ) . '" value="' . esc_attr( $key ) . '" data-price="' . edd_get_price_option_amount( $download_id, $key ) .'"/>&nbsp;';
                        $item_prop = edd_add_schema_microdata() ? ' itemprop="description"' : '';
                        echo '<span class="edd_price_option_name"' . $item_prop . '>' . esc_html( $price['name'] ) . '</span>';
                        echo '</td>';
                        echo '<td class="desc">';
                            echo '<span class="edd_price_option_price">' . $price_msg . '</span>';
                            echo '<span class="edd_price_option_desc">' . $price_desc . '</span>';
                            if( edd_add_schema_microdata() ) {
                                echo '<meta itemprop="price" content="' . esc_attr( $price['amount'] ) .'" />';
                                echo '<meta itemprop="priceCurrency" content="' . esc_attr( edd_get_currency() ) .'" />';
                            }
                        echo '</label>';
                        do_action( 'edd_after_price_option', $key, $price, $download_id );
                        echo '</td>';
                    echo '</tr>';
                endforeach;
            endif;
            do_action( 'edd_after_price_options_list', $download_id, $prices, $type );
            ?>
        </tbody>
    </table><!--end .edd_price_options-->
    <?php if($download_id == 657) : ?>
    <p style="font-size: 13px; text-align: center; margin: 2em 0 1.5em 0;"> Payment plan fees incur due to the additional risk and administration work involved for us. <br/>If you have a discount code, it will be applied on the next page.<br/><br/><sup>*</sup> For long plans, we ask 30% up front.</p>
    <?php elseif($download_id == 46649) :?>
    <p style="font-size: 13px;">All donations will be recorded manually and be visible for all site members to see. Being featured on website and newsletter is optional. If you don't want it, we'll not do it.</p>
    <?php endif; ?>
<?php
    do_action( 'edd_after_price_options', $download_id );
}

remove_action( 'edd_purchase_link_top', 'edd_purchase_variable_pricing', 10, 2 );
add_action( 'edd_purchase_link_top', 'ryit_purchase_variable_pricing', 10, 2 );



/**
 * Shows the final purchase total at the bottom of the checkout page
 *
 * @since 1.5
 * @return void
 */
function ryit_checkout_final_total() {
?>
<p id="edd_final_total_wrap">
    <strong><?php _e( 'Pay now:', 'easy-digital-downloads' ); ?></strong>
    <span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></span>
</p>
<?php
}
remove_action( 'edd_purchase_form_before_submit', 'edd_checkout_final_total', 999 );
add_action( 'edd_purchase_form_before_submit', 'ryit_checkout_final_total', 999 );




/* Update remaining seats for Journey to the Great Self upon payment */

function ryit_edd_after_purchase( $payment_id ) {
    $cart_items = edd_get_payment_meta_cart_details( $payment_id );
    foreach($cart_items as $item) {
        if($item['id'] == 55179) { //Journey to the Great Self
            $available_seats = get_field('ryit_greatself_seats', 'option');
            if($available_seats > 0) {
                update_field('ryit_greatself_seats', --$available_seats, 'option');
            }
            break;
        }
        elseif($item['id'] == 657) { //Reclaim your Inner Throne
            $user_id = get_current_user_id();
            $args = array(
                'subscription_id'    => 5,
                'status'             => 'free'
            );
            
            rcp_add_user_to_subscription( $user_id, $args ); //This adds user to the RYIT Basecamp access level

            //update available seats
            $available_seats = get_field('ryit_seats', 'option');
            if($available_seats > 0) {
                update_field('ryit_seats', --$available_seats, 'option');
            }
        }
        elseif($item['id'] == 57423) { //RYIT deposit
            //update available seats
            $available_seats = get_field('ryit_seats', 'option');
            if($available_seats > 0) {
                update_field('ryit_seats', --$available_seats, 'option');
            }
        }
    }
}
add_action( 'edd_complete_purchase', 'ryit_edd_after_purchase' );



function ryit_greatself_seats() {
    return get_field('ryit_greatself_seats', 'option');
}

add_shortcode('ryit_greatself_seats', 'ryit_greatself_seats');


function ryit_seats() {
    return get_field('ryit_seats', 'option');
}

add_shortcode('ryit_seats', 'ryit_seats');


function fb_comments($atts) {
    extract(shortcode_atts(array(
          'href' => '',
          'numposts' => 20,
          'title' => 'Comments',
          'width' => '600'
       ), $atts));
    $echo = "<h2 style='text-align: center; margin-bottom: 30px;'>" . $title . "</h2><div style='text-align: center; max-width:" . $width . "px; margin: 0 auto;'><div class='fb-comments' data-href='" . $href . "' data-width='" . $width . "' data-numposts='" . $numposts . "' ></div></div>";
    return $echo;
}

add_shortcode( 'fb_comments' , 'fb_comments' );

function pre_avada_shortcodes() {
    $shortcode = get_field('pre_avada_shortcode');
    if($shortcode) {
        echo do_shortcode($shortcode);
    }
}

add_filter( 'avada_before_body_content', 'pre_avada_shortcodes' );


function ryit_start_time($args, $format) {
    $args = shortcode_atts( array(
            'day_offset' => 0,
            'format' => "F j Y"
        ), $args );
        
    $day_offset = $args['day_offset'];
    $format = $args['format'];

    $date = get_field('ryit_start_time', 'options', false, false);
    $date = new DateTime($date);

    if(!empty($day_offset)) {
        $offset = 86400 * $day_offset;
        return date($format, strtotime($date->format('Y-m-d')) + $offset);
    }
    else {
        return date($format, strtotime($date->format('Y-m-d')));
    }
}

add_shortcode('ryit_start_time', 'ryit_start_time');



function ryit_next_upcoming_event() {
    if(is_front_page()) :
        $events = tribe_get_events( 
            array(
                'eventDisplay'=>'upcoming',
                'posts_per_page'=>1,
                'tax_query'=> array(
                    array(
                        'taxonomy' => 'tribe_events_cat',
                        'field' => 'slug',
                        'terms' => array('community-call','alumni-call','webinar')
                    )
                )
            )
        );

        if($events) :
            $event = $events[0];
            $event = get_object_vars($event);
            $thumbnail = get_the_post_thumbnail($event['ID'], 'thumbnail');
            $start_date = $event['EventStartDate'];
            $start_date = substr($start_date, 0,10);

            $now = new DateTime(date("Y-m-d")); // or your date as well
            $start_date = new DateTime($start_date);

            $datediff = $start_date->diff($now)->format("%a");
            $when_echo = $datediff >= 1 ? "In " . $datediff . " days" : "Today";


            $echo = "<div id='upcoming-event'><a href='" . $event['guid'] . "'><div class='thumbnail'>" . $thumbnail . "</a></div><div class='event-content'><h4>Next event you can attend</h4><h3><a href='" . $event['guid'] . "'>" . $event['post_title'] . "</a></h3><p>" . $when_echo . "<span style='margin: 0 8px; color: #aaa;'>|</span><a href='/calendar'>Full calendar</a></p></div></a></div>"; 
            return $echo;
        endif;   
    endif;
}

add_shortcode('list_upcoming_event','ryit_next_upcoming_event');

function ryit_video_training_html() {
    return get_field('ryit_video_training_html', 'options');
}

add_shortcode('ryit_video_training_html', 'ryit_video_training_html');

function add_upcoming_event() {
    if(is_front_page()) :
    do_shortcode('[ryit_next_upcoming_event]');
    ?>
    <script type="text/javascript">
        $j = jQuery.noConflict();

        function toggleClass() {
            $j('#upcoming-event').removeClass('show');
        }

        $j(document).ready(function() {
            $j("#upcoming-event").appendTo('.flexslider.main-flex');
            $j('#upcoming-event').addClass('show');
            setTimeout(toggleClass, 6000);
        });
    </script>
<?php
    endif;
}

add_filter( 'avada_before_body_content', 'add_upcoming_event' );


function ryit_fb_apps() {
    global $post;
    $url = get_permalink($post->ID);
    $post_time = get_post_time('U');
    $switch_time = 1485043200; //Jan 22, 2017

    //we switched to https and trailing slash was added

    //Page IDs where Facebook has stored likes without a trailing slash
    $fb_pages_array = array(45526,45542);

    if($post_time <= $switch_time) {
        $url = str_replace('https', 'http', $url);
        if(in_array($post->ID, $fb_pages_array)) {
            $like_url = rtrim($url, '/');
        }
        else {
            $like_url = $url;
        }
    }

    $fb_like = '<div class="fb-like" data-href="' . $like_url . '" data-layout="standard" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>';
    $fb_comments = '<div class="fb-comments" data-href="' . $url . '" data-width="600" data-numposts="5"></div>';
    return "<div id='fbApps'>" . $fb_like . $fb_comments . "</div>";
}

add_shortcode( 'ryit_fb_apps' , 'ryit_fb_apps' );

function ryit_scroll_alert() {
    ?>
    <script type="text/javascript">
        $j = jQuery.noConflict();
        $j(document).ready(function() {
            if ($j('.flexslider.main-flex').length) {
                //console.log('icon: yes');
                $j('.flexslider.main-flex').prepend('<div class="ryit-arrow-down" aria-hidden="true"></div>');
            }
        });
    </script>
    
<?php
}

add_filter( 'avada_before_body_content', 'ryit_scroll_alert' );

function ryit_show_ssl_header() {
    ?>
<script type="text/javascript"> //<![CDATA[ 
var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.comodo.com/" : "http://www.trustlogo.com/");
document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
</script>
<?php
}

function ryit_show_ssl_footer() {
?>
    <div id="comodo-trust">
    <script language="JavaScript" type="text/javascript">
    TrustLogo("https://www.inner-throne.com/wp-content/uploads/2017/03/comodo_secure_seal_113x59_transp.png", "CL1", "trust-logo");
    </script>
    <a href="https://ssl.comodo.com" id="comodoTL">Comodo SSL</a>
    </div>
<?php
}

function ryit_ssl_logo_check() {
  global $post;
    if(get_field('ryit_show_ssl_logo', $post->ID)) {
        add_action('wp_head', 'ryit_show_ssl_header');
        add_action('avada_after_main_content', 'ryit_show_ssl_footer');
    }
}

add_action('wp_enqueue_scripts', 'ryit_ssl_logo_check');

//flush_rewrite_rules( false ); - THIS DOES NOT SEEM NECESSARY
    
add_filter( 'avada_before_main_container', 'ryit_lesson_slider' );

function ryit_lesson_slider() {
    global $post;
    $post_id = $post->ID;
    if(get_field('ryit_activate_slider')) :
      $ryit = (is_page(45618) || wp_get_post_parent_id($post_id) == 45618) ? " is_ryit" : "";
?>
    <div id='sliders-container' class="fullscreen<?php echo $ryit; ?>">

        <div class='fusion-slider-container fusion-slider-52277 -container' style='height: 762px; max-width: 100%; max-height: 762px;'>
        <style type='text/css' scoped='scoped'>
            .fusion-slider-52277 .flex-direction-nav a {
                width:63px;height:63px;line-height:63px;font-size:25px; 
            }
        </style>

        <?php
            $title = get_field('ryit_slider_title');
            $top_text = get_field('ryit_slider_top');
            $caption = do_shortcode(get_field('ryit_slider_caption'));
            $align = get_field('ryit_slider_align');
            if(!$align) $align = "center";
        ?>

        <div class='tfs-slider flexslider main-flex' style='max-width: 100%; height: 762px;' data-slider_width='100%' data-slider_height='400px' data-slider_content_width='' data-full_screen='1' data-parallax='0' data-nav_arrows='1' data-nav_box_width='63px' data-nav_box_height='63px' data-nav_arrow_size='25px' data-pagination_circles='0' data-autoplay='1' data-loop='0' data-animation='fade' data-slideshow_speed='7000' data-animation_speed='600' data-typo_sensitivity='1' data-typo_factor='1.5'>
          <?php
            if(get_field('ryit_bottom_text')) :
              echo "<div class='bottom-text'>" . get_field('ryit_bottom_text') . "</div>";
            endif;
          ?>
            <ul class='slides' style='max-width: 100%; width: 100%;'>
                <li data-mute='yes' data-loop='yes' data-autoplay='yes' class='flex-active-slide' style='width: 100%; float: left; margin-right: -100%; position: relative; opacity: 1; display: block; z-index: 2;'>
                <div class='slide-content-container slide-content-<?php echo $align; ?>' style=''>
                    <div class='slide-content' style='opacity: 1; margin-top: 0px;'>
                        <?php if($top_text) : ?>
                        <div class='chapter-header with-bg'>
                            <div class='fusion-title-sc-wrapper' style='background-color: rgba(0,0,0, 0.4);'>
                                <div class='fusion-title title fusion-sep-none fusion-title-center fusion-title-size-two fusion-border-below-title' style='margin-top:0px;margin-bottom:0px;'>
                                    <h4 class='title-heading-center' style='color: rgb(255, 255, 255); font-size: 59.7px; line-height: 79.6px;' data-inline-fontsize='true' data-inline-lineheight='true' data-fontsize='60' data-lineheight='80'><?php echo $top_text; ?></h4>
                                </div>                                  
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($title) : ?>
                        <div class='heading with-bg'>
                            <div class='fusion-title-sc-wrapper' style='background-color: rgba(0,0,0, 0.4);'>
                                <div class='fusion-title title fusion-sep-none fusion-title-center fusion-title-size-two fusion-border-below-title' style='margin-top:0px;margin-bottom:0px;'>
                                    <h2 class='title-heading-center' style='color: rgb(255, 255, 255); font-size: 59.7px; line-height: 79.6px;' data-inline-fontsize='true' data-inline-lineheight='true' data-fontsize='60' data-lineheight='80'><?php echo $title; ?></h2>
                                </div>                                  
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($caption) :?>
                        <div class='caption with-bg'>
                            <div class='fusion-title-sc-wrapper' style='background-color: rgba(0, 0, 0, 0.4);'>
                                <div class='fusion-title title fusion-sep-none fusion-title-<?php echo $align; ?> fusion-title-size-three fusion-border-below-title' style='margin-top:0px;margin-bottom:0px;'>
                                    <h3 class='title-heading-center' style='color: rgb(255, 255, 255); font-size: 20px; line-height: 32px;' data-inline-fontsize='true' data-inline-lineheight='true' data-fontsize='20' data-lineheight='32'><?php echo $caption; ?></h2></h3>
                                </div>                                  
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class='buttons'>
                        </div>
                    </div>
                </div>
                <?php if(is_jgs_page()) : ?>
                    <div class='background background-image bg-overlay' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>); max-width: 100%; height: 762px; width: 100%;' data-imgwidth='1920'></div>
                <?php endif; ?>
                <div class='background background-image' style='background-image: url(<?php echo get_the_post_thumbnail_url(); ?>); max-width: 100%; height: 762px; width: 100%;' data-imgwidth='1920'></div>
                </li>
            </ul>
            <ul class='flex-direction-nav'><li><a class='flex-prev flex-disabled' href='#' tabindex='-1'></a></li><li><a class='flex-next flex-disabled' href='#' tabindex='-1'></a></li></ul></div>
        </div>
    </div>
<?php
    endif;
}



/* Shared functions */

function ryit_print_name($atts) {
    $atts = shortcode_atts(
        array(
            'fullname' => false
        ), $atts, 'ryit_print_name');

    $user_id = get_current_user_id();   
    $fullname = $atts['fullname'];

    global $current_user;
    get_currentuserinfo();

    if($fullname == false) {
        $name = $current_user->user_firstname;
        //in case mother's maiden name is stored in first name, remove it
        $name = explode(" ", $name);
        $first_name = $name[0];
        return $first_name;
    }
    else {
        $name = $current_user->user_firstname . " " .  $current_user->user_lastname;
        return $name;
    }
}

add_shortcode( 'ryit_name' , 'ryit_print_name' );


function ryit_comment() {
    ob_start();
    comments_template('/comments-view.php');
    $comments = ob_get_clean();
    return $comments;
}

add_shortcode( 'ryit_comment' , 'ryit_comment' );


function ryit_list_available_weeks() {
    // Set up the objects needed
    $my_wp_query = new WP_Query();
    $all_wp_pages = $my_wp_query->query(array('post_type' => 'page', 'posts_per_page' => '-1'));

    // Get the page as an Object
    $ryit_parent_page = get_post(45618);

    // Filter through all pages and find Portfolio's children
    $ryit_ancestor_pages = get_page_children( $ryit_parent_page->ID, $all_wp_pages );
    //var_dump(expression);

    $ryit_ancestor_pages = array_reverse($ryit_ancestor_pages); //show oldest pages first

    ob_start();
    foreach ($ryit_ancestor_pages as $page) {
        $image_url = wp_get_attachment_url( get_post_thumbnail_id($page->ID) );
        if(rcp_user_can_access(get_current_user_id(), $page->ID)) :
    ?>
        <div class="fusion-builder-row fusion-builder-row-inner fusion-row">
            <div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_4  fusion-one-fourth fusion-column-first week-1 1_4" style="margin-top: 0px;margin-bottom: 20px;width:25%;width:calc(25% - ( ( 4% ) * 0.25 ) );margin-right:4%;">
                <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
                    <span style="-moz-box-shadow: 2px 3px 7px rgba(0,186,0,.3);-webkit-box-shadow: 2px 3px 7px rgba(0,186,0,.3);box-shadow: 2px 3px 7px rgba(0,186,0,.3);" class="fusion-imageframe imageframe-dropshadow imageframe-1 hover-type-zoomin"><a class="fusion-no-lightbox" href="<?php echo get_permalink($page->ID); ?>" target="_self" aria-label="call-to-adventure-fullhd"><img src="<?php echo $image_url; ?>" width="600" height="338" alt="" class="img-responsive"></a></span>
                </div>
            </div>
            <div class="fusion-layout-column fusion_builder_column fusion_builder_column_3_4  fusion-three-fourth fusion-column-last week-1 3_4" style="margin-top: 0px;margin-bottom: 20px;width:75%;width:calc(75% - ( ( 4% ) * 0.75 ) );">
                <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
                    <div class="fusion-title title fusion-sep-none fusion-title-size-three fusion-border-below-title">
                        <h3 class="title-heading-left"><?php echo $page->post_title; ?></h3>
                    </div>
                    <div class="fusion-text">
                        <?php echo get_field('ryit_week_description', $page->ID); ?><p class="readmore"><a href="<?php echo get_permalink($page->ID); ?>" data-hover="Read more&nbsp;»"><span>Read more&nbsp;»</span></a></p>
                    </div>
                </div>
            </div>
        </div>
    <?php
      endif;
    }
    return "<div id='course-weeks'>" . ob_get_clean() . "</div>";
}

add_shortcode('ryit_week_list', 'ryit_list_available_weeks');



function ryit_list_courses() {
    
    /*$ryit_id = 45618;
    $jgs_id = 52269;
    $alumnus_id = 51901;*/

    $subscription_id = rcp_get_subscription_id( get_current_user_id() );
    
    $echo = "";
    if($subscription_id == 2 || $subscription_id == 3) { //rcp_user_can_access(get_current_user_id(), $ryit_id) || rcp_user_can_access(get_current_user_id(), $alumnus_id)) { //RYIT
        $echo .= '<div class="course">';
        $echo .= '<a href="/courses/ryit">';
        $echo .= '<img src="https://www.inner-throne.com/wp-content/uploads/2017/12/ryit-box-325x375.jpg" />';
        $echo .= '<h2>Reclaim your Inner Throne</h2>';
        $echo .= '</a>';
        $echo .= '</div>';
    }
    if($subscription_id == 4) { //rcp_user_can_access(get_current_user_id(), $jgs_id)) { //JGS
        $echo .= '<div class="course">';
        $echo .= '<a href="/courses/jgs">';
        $echo .= '<img src="https://www.inner-throne.com/wp-content/uploads/2017/12/greatself-box-325x375.jpg" />';
        $echo .= '<h2>Journey to the Great Self</h2>';
        $echo .= '</a>';
        $echo .= '</div>';
    }

    if(!empty($echo)) {
        $echo = '<div class="course-listing">' . $echo . '</div>';
    } 
    else {
        $echo = "<div class='maxwidth-600' style='text-align: center;'><h2>You haven't purchased any trainings yet</h2><p><a href='/services'>Check them out</a></p></div>";
    }
    return $echo;
}

add_shortcode( 'ryit_list_courses' , 'ryit_list_courses' );


function ryit_next_workshop() {
  $events = tribe_get_events(
              array(
                'eventDisplay'=>'upcoming',
                'posts_per_page'=> -1,
                'tax_query'=> array(array(
                        'taxonomy' => 'tribe_events_cat',
                        'field' => 'slug',
                        'terms' => 'workshop'
                ))
              )
            );

  $echo = "";
  //var_dump($events);


  foreach ($events as $event) {
    $echo .= '<h2>Next workshop</h2>';
    $echo .= '<h3>' . $event->post_title . '</h3>';
    $echo .= '<p class="date">' . $event->EventStartDate . ' &ndash; ' . $event->EventEndDate . '</p>';
    $echo .= '<p><a href="' . $event->guid . '">Read more</a></p>';
  }

  if(empty($echo)) $echo = "<h2>Your city next?</h2><h3>We're currently planning our next workshop. Stay tuned.</h3>";
  return '<div id="upcoming-workshop">' . $echo . '</div>';
  
}

add_shortcode( 'ryit_next_workshop' , 'ryit_next_workshop' );


/*******************************************/
/**** GENERAL ACF FIELD UPDATE FUNCTION ****/
/*******************************************/

function ryit_change_acf_field_value() {
    if(isset($_GET['field_key'])) {    
      $field_key = $_GET['field_key'];
    }
    else {
      die();
    }

    if(isset($_GET['new_value'])) {
      $new_value = $_GET['new_value'];
    }
    else {
      die();
    }

    if(isset($_GET['post_id'])) {
      $post_id = $_GET['post_id'];
    }
    else {
      die();
    }

   update_field($field_key, $new_value, $post_id);
   wp_send_json_success(true);
   die();
}

add_action( 'wp_ajax_ryit_change_acf_field_value', 'ryit_change_acf_field_value' );
add_action( 'wp_ajax_nopriv_ryit_change_acf_field_value', 'ryit_change_acf_field_value' );


/* ************************************ */
/* Journey to the Great Self functions */
/* ************************************ */

function is_jgs_page() {
    global $post;
    if( !is_object($post) ) return;
    $parent = wp_get_post_parent_id($post->ID);
    if($parent == 52269) {
        return true; 
    }
    else {
        return false;
    }
}

/*
if(is_jgs_page()) {
  echo "<h1>LOAD SCRIPT</h1>";
  add_action('wp_head', 'add_jgs_stylesheet');
  add_action('wp_head', 'jgs_initialize');
}*/

add_action('wp_head', 'jgs_initialize');

function jgs_initialize() {
  if(is_jgs_page()) {
      add_jgs_stylesheet();
      jgs_enqueue_jplayer();
    }
}

function jgs_enqueue_jplayer() {
    $echo = '<link rel="stylesheet" href="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/skin/circle.player.css">';
    $echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/jquery.jplayer.min.js"></script>';
    $echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/jquery.transform2d.js"></script>';
    $echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/jquery.grab.js"></script>';
    $echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/mod.csstransforms.min.js"></script>';
    $echo .= '<script type="text/javascript" src="' . get_stylesheet_directory_uri() . '/js/lib/circle-player/js/circle.player.js"></script>';
    echo $echo;
}

function add_jgs_stylesheet() {
    global $post;
    if( !is_object($post) ) return;
    $parent = wp_get_post_parent_id($post->ID);
    if($parent == 52269) {
        wp_enqueue_style('greatself-style', get_stylesheet_directory_uri() . "/css/greatself-homestudy.css");
        wp_enqueue_style('rpg-style', get_stylesheet_directory_uri() . "/css/rpg-awesome.css");
    }
}


function jgs_embed_jplayer() {
?>
    <!-- The container for the interface can go where you want to display it. Show and hide it as you need. -->
    <li id="label">Menu</li>
    <li id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
        <div class="jp-type-single">
            <div class="jp-gui jp-interface">
                <button class="jp-play" role="button" tabindex="0"><span class="fa fa-play"></span><span class="fa fa-pause"></span></button>
                <!--
                <div class="jp-volume-controls">
                    <button class="jp-mute" role="button" tabindex="0">mute</button>
                    <button class="jp-volume-max" role="button" tabindex="0">max volume</button>
                    <div class="jp-volume-bar">
                        <div class="jp-volume-bar-value"></div>
                    </div>
                </div>
                <div class="jp-controls-holder">
                    <div class="jp-controls">
                        <button class="jp-play" role="button" tabindex="0">play</button>
                        <button class="jp-stop" role="button" tabindex="0">stop</button>
                    </div>
                    <div class="jp-progress">
                        <div class="jp-seek-bar">
                            <div class="jp-play-bar"></div>
                        </div>
                    </div>
                    <div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
                    <div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
                    <div class="jp-toggles">
                        <button class="jp-repeat" role="button" tabindex="0">repeat</button>
                    </div>
                </div>
                -->
            </div>
            <!--
            <div class="jp-details">
                <div class="jp-title" aria-label="title">&nbsp;</div>
            </div>
            <div class="jp-no-solution">
                <span>Update Required</span>
                To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
            </div>
            -->
        </div>
    </li>
<?php
}


//Ajax update of Fragments form
add_action( 'wp_ajax_display_echo_system', 'jgs_display_echo_system' );
add_action( 'wp_ajax_nopriv_display_echo_system', 'jgs_display_echo_system' );

function jgs_display_echo_system() {
    if(isset($_REQUEST['post_id'])) {    
        $post_id = $_REQUEST['post_id'];
    }
    else {
        die();
    }

    $response = array();
    $response[] = jgs_echo_archives(array("post_id" => $post_id));
    echo json_encode($response);
    die();
}



function jgs_user_has_answered() {
    global $post;
    //Check for existing comment
    $args = array(
        'post_id' => $post->ID, 
        'user_id' => get_current_user_id(),
    );
    /*
    print_r($args);
    echo "post id: " . $post->ID;
    echo "user id: " . get_current_user_id(); */
    $comments = get_comments($args);
    if(count($comments) > 0 ) :
        return true;
    else : 
        return false;
    endif;
}

add_shortcode('jgs_user_has_answered', 'jgs_user_has_answered');

//Simple Comment Editing
add_filter( 'sce_comment_time', 'edit_sce_comment_time' );


function edit_sce_comment_time( $time_in_minutes ) {
    return 60;
}


//Register AJAX functions
add_action('wp_ajax_dialogue_request', 'dialogue_request' );
add_action('wp_ajax_nopriv_dialogue_request', 'dialogue_request' );


function dialogue_request() {
    if(isset($_REQUEST['post_id'])) {    
        $post_id = $_REQUEST['post_id'];
    }
    else {
        die();
    }

    $response = get_field('jgs_character', $post_id);
    $response = $response['response'];
    echo json_encode($response);
    die();
}

//AJAX functions for progress
add_action('wp_ajax_update_progress', 'jgs_update_progress' );
add_action('wp_ajax_nopriv_update_progress', 'jgs_update_progress' );   

function jgs_update_progress() {
    if(isset($_REQUEST['week_id'])) {    
        $week_id = $_REQUEST['week_id'];
    }
    if(isset($_REQUEST['step'])) {    
        $step = $_REQUEST['step'];
    }

    $user_id = get_current_user_id();
    $progress = $week_id . "," . $step;

    //progress field id = field_5a58d033aeb25
    update_field('jgs_user_data_progress', $progress, 'user_' . $user_id);

    die();
}

//Ajax update of Advanced Custom Fields form
add_action( 'wp_ajax_save_my_data', 'acf_form_head' );
add_action( 'wp_ajax_nopriv_save_my_data', 'acf_form_head' );


function jgs_fragments_collected($user_id) {
    if(empty($user_id)) {
        $user_id = get_current_user_id();
    }

    $fragments_count = 4;
    $fragments = get_field('jgs_fragments','user_' . $user_id);

    for($i=1;$i<= $fragments_count;$i++) {
        $field = $fragments['fragment_' . $i];
        if(empty($field)) {
            return false;
        }
        else {
            return true;
        }
    }
}


add_action('comment_post', 'ajaxify_comments',20, 2);

function ajaxify_comments($comment_ID, $comment_status){
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
    //If AJAX Request Then
    switch($comment_status){
    case '0':
        //notify moderator of unapproved comment
        wp_notify_moderator($comment_ID);
    case '1': //Approved comment
        echo "success";
        $commentdata=&get_comment($comment_ID, ARRAY_A);
        $post=&get_post($commentdata['comment_post_ID']);
        wp_notify_postauthor($comment_ID, $commentdata['comment_type']);
    break;
    default:
        echo "error";
    }
    exit;
    }
}


function jgs_user_profile() {
    global $post;
    $user_id = get_current_user_id();

    $fields = array('field_5a576ed8b86eb','field_59e7447aceaf8','field_5a4be6cf193be','field_59e748b89988c','field_5a58cff4aeb23','field_5a5a254d8bae5');

    if(jgs_fragments_collected($user_id)) {
        $fields[] = 'field_5a6518b25fd3d';
    }

   $settings = array(
        'id' => 'acf_user_profile_form',
        'fields' => $fields,
        'post_id' => 'user_' . $user_id
    );

?>

    <div id="account" class="closed hide">
        <div class="toggle"><i class="fa fa-cog" aria-hidden="true"></i></div>
        <div class="wrap">
            <div class="wrap">
                <h3>Account settings</h3>
                <?php
                    $user = get_currentuserinfo();
                    $username = $user->user_firstname . " " . $user->user_lastname;
                ?>
                <p class='welcome'>For <?php echo $username; ?>
                <?php acf_form($settings); ?>
            </div>
        </div>
    </div>

<?php
}

function jgs_embed_videos() { 
    global $post;
    $calls = get_field('jgs_videos', $post->ID);
    if($calls) {
?>
<div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_1  fusion-one-full fusion-column-first fusion-column-last maxwidth-1000 1_1" id="coaching-call">
<?php
        foreach ($calls as $call) :
            $title = $call['title'];
            $text = $call['text'];
            $src = $call['src'];
?>
<?php if($src && $title) : ?>
    <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
    <div class="fusion-builder-row fusion-builder-row-inner fusion-row "><div class="fusion-layout-column fusion_builder_column fusion_builder_column_2_3  fusion-two-third fusion-column-first 2_3" style="margin-top: 0px;margin-bottom: 20px;width:66.66%;width:calc(66.66% - ( ( 4% ) * 0.6666 ) );margin-right:4%;">
    <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
    <div class="fusion-video fusion-vimeo fusion-alignleft" style="max-width:700px;max-height:394px; width:100%"><div class="video-shortcode"><div class="fluid-width-video-wrapper" style="padding-top: 56.2857%;"><iframe src="https://player.vimeo.com/video/<?php echo $src; ?>?autoplay=0&amp;api=1&amp;player_id=player_3&amp;wmode=opaque" allowfullscreen="" title="vimeo251266841" id="player_3" name="fitvid0" kwframeid="3"></iframe></div></div></div>

    </div>
    </div><div class="fusion-layout-column fusion_builder_column fusion_builder_column_1_3  fusion-one-third fusion-column-last 1_3" style="margin-top: 0px;margin-bottom: 20px;width:33.33%;width:calc(33.33% - ( ( 4% ) * 0.3333 ) );">
    <div class="fusion-column-wrapper" style="padding: 0px 0px 0px 0px;background-position:left top;background-repeat:no-repeat;-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;" data-bg-url="">
    <div class="fusion-title title fusion-sep-none fusion-title-size-two" style="margin-top:0px;margin-bottom:15px;"><h2 class="title-heading-left"><?php echo $title; ?></h2></div><div class="fusion-text"><?php echo $text; ?></p>
    </div>

    </div>
    </div></div><div class="fusion-clearfix"></div>
    </div>

<?php endif; endforeach; ?>
</div>
<?php
    }
}


/************************** ALUMNUS DIRECTORY / THE BROTHERHOOD **************************/


add_action( 'wp_ajax_alumnus_directory', 'ryit_member_directory' );
add_action( 'wp_ajax_nopriv_alumnus_directory', 'ryit_member_directory' );

function ryit_user_is_alumnus($user_id=NULL) {
  if(empty($user_id)) {
    $user_id = get_current_user_id();
  }
  $subscription_id = rcp_get_subscription_id( $user_id );

  if($subscription_id == 2) {
    return true; //user counted as on current round. includes leadership team
  }
  else {
    return false; //user considered an alumnus
  }
}

function ryit_user_is_current($user_id=NULL) {
  if(empty($user_id)) {
    $user_id = get_current_user_id();
  }
  $subscription_id = rcp_get_subscription_id( $user_id );

  if($subscription_id == 3) {
    return true; //user counted as on current round. includes leadership team
  }
  else {
    return false; //user considered an alumnus
  }
}

function ryit_member_directory() {
  $user_id = get_current_user_id();
  if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {  
    $is_ajax = false;
    $display_type = get_user_meta($user_id, "alumnus_directory_display_type",true);
    $sort_type = get_user_meta($user_id, "alumnus_directory_sort_type",true);
    $filter_type = get_user_meta($user_id, "alumnus_directory_filter_type",true);
    if(!isset($display_type)) $display_type = 0;
    if(!isset($sort_type)) $sort_type = 0;
    if(!isset($filter_type)) $filter_type = 0;
  }
  else {
    $is_ajax = true;

    if(isset($_GET['display_type'])) {    
      $display_type = $_GET['display_type'];
      //if(is_empty($display_type)) { $display_type = 0; }
      update_user_meta($user_id, "alumnus_directory_display_type", $display_type);
    }
    if(isset($_GET['sort_type'])) {    
      $sort_type = $_GET['sort_type'];
      //if(is_empty($sort_type)) $sort_type = 0;
      update_user_meta($user_id, "alumnus_directory_sort_type", $sort_type);
    }
    if(isset($_GET['filter_type'])) {    
      $filter_type = $_GET['filter_type'];
      //if(is_empty($filter_type)) $filter_type = 0;
      update_user_meta($user_id, "alumnus_directory_filter_type", $filter_type);
    }
  }
  
  $header_echo = "";

  if(ryit_user_is_current()) {
    $members = rcp_get_members('free',3,0,999999,'ASC');
    $header_echo .= '<h1 class="title-heading-center"><p style="text-align:center;">The Fellowship</p></h1>';
    $header_echo .= '<div class="fusion-text maxwidth-600" style="margin-bottom: 40px;"><p style="text-align: center;">The men traveling with you through the Realm of Forgotten Kings.</p>
</div>';
  }
  else {
    $alumni = rcp_get_members('free',2,0,999999,'ASC');
    $current = rcp_get_members('free',3,0,999999,'ASC');
    $members = array_merge($alumni,$current);
    $header_echo .= '<h1 class="title-heading-center"><p style="text-align:center;">The Fellowship</p></h1>';
    $header_echo .= '<div class="fusion-text maxwidth-600" style="margin-bottom: 40px;"><p style="text-align: center;">The men who have been through the Realm of Forgotten Kings and who have started to remember.</p></div>';
  }
  if($sort_type == 0) { //Sort by RYIT round
    $rounds = array();
    $round_number_last = 0;
    foreach($members as $member) {
      if($member->ID == 84 ) continue;
      $round_number = get_field('ryit_round_number', 'user_' . $member->ID);
      $comma_index = strpos($round_number, ','); //If user took part in several rounds then...
      if(!empty($comma_index)) {
        $round_number = substr($round_number,0,strpos($round_number, ',')); //....list them as participating in only the first
      }
      if($round_number == get_field('ryit_round_number', 'options')) {
        if(isset($round_number)) {
          $rounds[$round_number][] = $member->ID;
        }
      }
    }
    ksort($rounds);
  }
  else if($sort_type == 1) { //Sort by name
    $member = array(); //Men to be sorted
    foreach($members as $member) {
      $member_data = get_userdata($member->ID);
      $key = $member_data->first_name . " " . $member_data->last_name;
      $alumni_names[$key]['first_name'] = $member_data->first_name;
      $alumni_names[$key]['last_name'] = $member_data->last_name;
      $alumni_names[$key]['id'] = $member_data->ID;
    }
    ksort($alumni_names);
  }
  else if($sort_type == 2) {
    
  }

  //set up AJAX javascript
    
  ob_start();
  if(!$is_ajax) :
  ?>

  <script type='text/javascript'>
    jQuery('document').ready(function($j) {
      var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
      $j(document).on('change', '#alumnus_directory_settings #display_type select', function(e) {
        e.preventDefault();
        console.log("change display type");
        update_alumnus_view();
      });

      $j(document).on('change', '#alumnus_directory_settings #sort_type select', function(e) {
        e.preventDefault();
        console.log("change sort type");   
        update_alumnus_view();   
      });

      function update_alumnus_view() {
        var display_val = $j('#display_type select').prop('selectedIndex');
        var sort_val = $j('#sort_type select').prop('selectedIndex');
        var filter_val = $j('#filter_type select').prop('selectedIndex');

        //console.log(display_val);

        var data = {
          action: 'alumnus_directory',
          display_type: display_val,
          sort_type: sort_val,
          filter_type: filter_val
        };

        console.log("update");

        $j.ajax({
          url: ajaxurl,
          type: 'GET', // the kind of data we are sending
          data: data,        
          dataType: 'json',
          success: function(response) {
            console.log("response : " + response);
            $j('#directory_listing').html(response.data.echo);
            $j('#display_type_val').text(response.data.display_type);
          }
        });
      }
   });
  </script>

  <?php 
  endif;

  $form_js = ob_get_clean();

  /**************** CREATE FORM *****************/

  $display_types = array();
  $display_types[] = array('display_name','Name only');
  $display_types[] = array('display_portrait','Name & Portrait');

  $sort_types = array();

  if($user_is_alumnus) {
    $sort_types[] = array('sort_by_round','Round');
  }
  $sort_types[] = array('sort_by_name','Name');
  /*$sort_types[] = array('sort_by_country','Country'); */

  $form_echo = "";
  $form_echo .= "<form id='alumnus_directory_settings'>";
  $form_echo .= "<div id='display_type'><h3>View type</h3>";
  $form_echo .= "<select id='display_type_input'>";

  $i = 0;
  foreach($display_types as $type) {
    $form_echo .= "<option id='" . $type[0] . "'";
    if($display_type == $i) $form_echo .= " selected='selected'";
    $form_echo .= "'>" . $type[1] . "</option>";
    $i++;
  }

  $form_echo .= "</select>";
  $form_echo .= "</div>";
  $form_echo .= "<div id='sort_type'><h3>Sort by</h3>";
  $form_echo .= "<select id='sort_type_input'>";

  $i = 0;
  foreach($sort_types as $type) {
    $form_echo .= "<option id='" . $type[0] . "'";
    if($sort_type == $i) $form_echo .= " selected='selected'";
    $form_echo .= ">" . $type[1] . "</option>";
    $i++;
  }

  $form_echo .= "</select>";
  $form_echo .= "</div>";
  $form_echo .= "<div id='filter_type'><h3>Filter</h3>";
  $form_echo .= "<select id='filter_type_input'><option>Default (more coming)</option></select>";
  $form_echo .= "</div>";
  $form_echo .= "</form>";

  $echo = "";

  if(ryit_user_is_current()) {
    $echo.= '<p style="text-align: center; max-width: 600px; margin: -0.5em auto 3em; color: #999;">When you complete the training, the full alumni will be visible to you here, with networking opportunities etc.</p>';
  }

  //Define default avatar  
  $upload_dir = wp_upload_dir();
  $default_avatar = $upload_dir['baseurl'] . "/2014/12/crown-logo.png";
  
  if($sort_type == 0) { /************* Sort alumni by round ******************/
    foreach($rounds as $round_number=>$users) {
      $round_echo = "";

      foreach ($users as $user_id) {
        $alumnus = get_userdata($user_id);
       
        $avatar = get_field('field_5a576ed8b86eb','user_' . $user_id);
        if(empty($avatar)) {
          $avatar = $default_avatar;
        }

        if($display_type == 0) :
          $round_echo .= "<li class='alumnus'><h4><a href='/user-profile?user_id=" . $alumnus->ID . "'>" . $alumnus->first_name . " " . $alumnus->last_name . "</a></h4></li>";
        endif;

        if($display_type == 1) :
          $round_echo .= "<div class='alumnus'>";
          $round_echo .= "<div class='portrait' style='background-image: url(" . $avatar . ");'>";
          $round_echo .= "<div class='hover'><a href='/user-profile?user_id=" . $alumnus->ID . "'><div class='hover_bg'></div></a></div>";
          $round_echo .= "</div>";
          $round_echo .= "<h4><a href='/user-profile?user_id=" . $alumnus->ID . "'>" . $alumnus->first_name . " " . $alumnus->last_name . "</a></h4>";
          $round_echo .= "</div>";
        endif;
      }

      switch($display_type) { //Gather 
        case 0: //Show name only
          $echo .= "<div class='alumnus-group'>";
          $echo .= "<h2>Round " . $round_number . "</h2>";
          $echo .= "<ul>" . $round_echo . "</ul>";
          $echo .= "</div>";
          break;
        case 1: //Show name and photo
          $echo .= "<div class='alumnus-group'>";
          $echo .= "<h2>Round " . $round_number . "</h2>";
          $echo .= $round_echo;
          $echo .= "</div>";
          break;
      }
    }
    if($is_ajax) {
      $echo = "<div id='rounds'>" . $echo . "</div>";
    }
    else {
      $echo = $header_echo . $form_js . $form_echo . "<div id='directory_listing'><div id='rounds'>" . $echo . "</div></div>"; 
    }
  }
  else if($sort_type == 1) { /******* Sort alumni by last name ********/
    foreach ($alumni_names as $alumnus) {
      $alumnus_data = get_userdata($alumnus['id']);
      $user_id = $alumnus_data->ID;

      $avatar = get_field('field_5a576ed8b86eb','user_' . $user_id);
      if(empty($avatar)) {
        $avatar = $default_avatar;
      }

      switch($display_type) { //Return HTML based on display type
        case 0: //Show name only
          $echo .= "<li><a href='/user-profile?user_id=" . $alumnus_data->ID . "'>" . $alumnus['first_name'] . " " . $alumnus['last_name'] . "</a></li>";
          break;
        case 1: //Show name and photo
          $echo .= "<div class='alumnus'>";
          $echo .= "<div class='portrait' style='background-image: url(" . $avatar . ");'>";
          $echo .= "<div class='hover'><a href='/user-profile?user_id=" . $alumnus_data->ID . "'><div class='hover_bg'></div></a></div>";
          $echo .= "</div>";
          $echo .= "<h4><a href='/user-profile?user_id=" . $alumnus_data->ID . "'>" . $alumnus_data->first_name . " " . $alumnus_data->last_name . "</a></h4>";
          $echo .= "</div>";
          break;
      }    
    }

    $title = "<h2>Sorted by name</h2>";
    
    if($display_type == 0) {
      $echo = $title . "<ul id='alumnus_names'>" . $echo . "</ul>";
    }
    else {
      $echo = $title . $echo;
    }
    
    if(!$is_ajax) {
      $echo = $header_echo . $form_js .  $form_echo . "<div id='directory_listing'><div class='alumnus-group'>" . $echo . "</div></div>";
    }
    else {
      $echo = "<div class='alumnus-group'>" . $echo . "</div>";
    }
  }
  else {
     $echo = $header_echo . $form_js .  $form_echo . "<div id='directory_listing'><div class='alumnus-group'>" . $echo . "</div></div>";
  }

  if($is_ajax) {
    $return['echo'] = $echo;
    if(isset($display_type)) {
      $return['display_type'] = $display_type;
    }
    if(isset($sort_type)) {
      $return['sort_type'] = $sort_type;
    }
    if(isset($filter_type)) {
      $return['filter_type'] = $filter_type;
    }
    wp_send_json_success($return);
    die();
  }
  else {
    return $echo;
  }
}

add_shortcode('member_directory', 'ryit_member_directory');


/************************** ALUMNUS/USER PROFILE **************************/


function ryit_alumnus_profile() {
  acf_form_head();
  wp_enqueue_style("media-upload", get_site_url() . "/wp-includes/css/media-views.min.css"); //ensure that image uploader looks correct

  global $post;
  $user_id = $_GET['user_id'];
  $current_user_id = get_current_user_id();

  /**** test ****/

  if(is_user_logged_in() && !isset($user_id)) {
    $user_id = get_current_user_id();
  }
  $user_data = get_userdata($user_id);
  if(!empty(get_avatar($user_data->ID))) {
    $args = array(
      'size' => 250,
      'default' => 'mysteryman'
    );
    $avatar = get_avatar_url($user_data->ID,$args);
  }

  $echo = "";

  ob_start();
?>
  <script type="text/javascript">
    var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

    jQuery('document').ready(function($j) {
      $j('#profile .field.can-edit .field-content').append('<div class="edit"><i class="fas fa-pencil-alt"></i></div>');
      $j('#profile .field.can-edit form .acf-form-submit').append('<div class="cancel"><i class="fas fa-times-circle"></i></div>');
    });

    $j(document).on('click', '#profile .field.can-edit .field-content', function(e) {
      e.preventDefault();
      $j(this).slideUp(250,function() {
        $j(this).parents('.field').find('form').slideDown(250);  
      });
    });

    $j(document).on('click', '#profile .cancel', function(e) {
      e.preventDefault();
      $j(this).parents('form').slideUp(250,function() {
        $j(this).parents('.field').find('.field-content').slideDown(250);  
      });
    });

    $j(document).on('click', '#edit-user-data', function(e) {
      $j('#user-data').fadeOut(500,function() {
        $j('#user-data-form-wrapper').fadeIn(500);
      })
    });

    $j(document).on('click', '#edit-user-data-cancel', function(e) {
      $j('#user-data-form-wrapper').fadeOut(500,function() {
        $j('#user-data').fadeIn(500);
      })
    });

    var baseurl = <?php get_permalink($post->ID); ?>

    $j(document).on('click', '.tabs li', function() {
      $j('.tabs li').removeClass('active');
      $j(this).addClass('active');
      var active_field = $j(this).attr('id');
      active_field = active_field.substring(4);
      $j('.main').attr('active-field',active_field);
      
      //Update rangeslider (is this needed now?)
      $j('input[type="range"]').rangeslider('update', true);
      $j('input[type="range"]').val(3).change();

      //Unselect dropdown
      $j('#ryit-menu select').val('-- Choose your week --');

      //Change address bar
      var stateObj = { foo: "bar" };
      history.pushState(stateObj, "Active field", "?active-field=" + active_field);
      $j('form').attr('return', '?active-field=' + active_field + '&updated=true');
    });


    $j(document).on('change', '.dropdown select', function() {
      $j('.tabs li').removeClass('active');
      $j(this).find('option').removeClass('active');
      $j(this).find(':selected').addClass('active');
      var active_field = $j(this).find(':selected').attr('id');
      active_field = active_field.substring(4);
      $j('.main').attr('active-field',active_field);

      //Change address bar
      var stateObj = { foo: "bar" };
      history.pushState(stateObj, "Active field", "?active-field=" + active_field);
      $j('form').attr('return', '?active-field=' + active_field + '&updated=true');
    });

    $j(document).on('click', '#button-claim-triad-member', function() {
      var data = {
        action: 'email_ping_triad_member',
        user_id: $j('#profile').attr('user_id')
      }

      $j.ajax({
        url: ajaxurl,
        type: 'POST', // the kind of data we are sending
        data: data,        
        dataType: 'json',
        success: function(response) {
          $j('#triad-member-claim .fusion-button').fadeOut(500, function() {
            $j('#triad-member-claim').html('<h2 style="border: none; margin-top: 1em;">Congratulations, the e-mail has been sent successfully!</h2>');
          });
        }
      });
    });
  </script>

  <?php
  $echo .= ob_get_clean(); //Set up javascript

  $active_field = $_GET['active-field'];

  if(ryit_user_is_alumnus($user_id)) { //Alumnus

    $fields_goals = array(
      'field_5b31265e34b15', //vision
      'field_5b7ab42bbe941', //mission
      'field_5b338e4e928e8', //ten year goal
      'field_5b338e3b928e7', //five year goal
      'field_5b338e2b928e6' //one-year goal
    );

    $fields_interests = array(
      'field_5b7ead2fe4691', //more info about your interests
      'field_5b338694a6537' //fields of interest or skill
    );

    $fields = array(array("Vision & Roadmap", $fields_goals), array("Purpose & Business", $fields_interests));
    if(empty($active_field)) {
      $active_field = 'vision-roadmap';
    }
  }
  else if(ryit_user_is_current($user_id)) { //Active course participants
    $fields_week1 = array(
      'field_5bdb128593646',  // Archetypal life wheel
      'field_5bdb1937f5228' // Intensity
    );

    $fields_week2 = array(
      'field_5bdad881a4bd1', // Commitments
      'field_5bdae7b751dc2' // Traumas
    );

    $fields_week3 = array(
      'field_5bdb1d215394e' // Addictions
    );

    $fields_week8 = array(
      'field_5bf5bc868e089' // Life commitments
    );

    $fields_week9 = array(
      'field_5beaec6a90723' // Life commitments
    );

    $fields_week12 = array(
      'ryit_user_vision',
      'ryit_user_mission',
      'ryit_user_goal_ten_year', // Life commitments
      'ryit_user_goal_five_year',
      'ryit_user_goal_one_year',
      'field_5c0a90689b6c0  '
    );

    $fields = array(array("Call to Adventure", $fields_week1), array("Path of Unknowing", $fields_week2), array("Mapmaker of the East", $fields_week3), array("Mystic Glade", $fields_week8), array("Valley of the Black Knight", $fields_week9), array("Reclaim your Inner Throne", $fields_week12));
    
    if(empty($active_field)) {
      $active_field = 'call-to-adventure';
    }
  }

  //Set up category tabs
  if(!empty($fields)) {
    $nav_echo = '<nav id="profile-menu" class="clearfix">';
    $nav_echo .= '<div id="ryit-menu" class="dropdown"><select>';
    $nav_echo .= '<option id="inactive">-- Choose your week --</option>';
    foreach($fields as $field) {
      $nav_echo .= '<option id="tab-' . sanitize_title($field[0]) . '"';
      if($active_field == sanitize_title($field[0])) {
        $nav_echo .= 'class="active" selected';
      }
      $nav_echo .= '>' . $field[0] . '</option>';
    }
    $nav_echo .= '</select></div>';
    $nav_echo .= '<ul class="tabs">';
    //$echo .= '<li id="tab-all">View all</li>';   
    if($user_id == $current_user_id) {
      $ui_buttons = array(array('life-assessment','Life Assessment'), array('edit-account','Edit Account'), array('purchase-history', 'Purchase History'));
      $button_count = count($ui_buttons);

      for($i=0; $i < $button_count; $i++) {
        $nav_echo .= '<li id="tab-' . $ui_buttons[$i][0] . '"';
        if($ui_buttons[$i][0] == $active_field) {
          $nav_echo .= ' class="active"';
        }
        $nav_echo .= '>' . $ui_buttons[$i][1] . '</li>';
      }
    }
    $nav_echo .= '</ul>';
    $nav_echo .= '</nav>';


    $fields_with_val = 0;
    $fields_echo = "";

    //Set up fields inside categories

    foreach($fields as $field_group) {
      $fields_echo .= '<div class="field-group" id="field-group-' . sanitize_title($field_group[0]) . '">';
      $fields = $field_group[1];

      $field_echo = "";

      /* Print field values to screen */
      foreach ($fields as $field) {

        //get essential profile data
        $field_obj = get_field_object($field, 'user_' . $user_id);
        
        if(!empty($field_obj['value']) || ($user_id == $current_user_id)) {
          $fields_with_val++;
          $can_edit = ($user_id == $current_user_id) ? " can-edit" : "";
          $is_message = ($field_obj['type'] == "message") ? " message" : "";
          $field_echo .= '<div id="' . $field_obj['name'] . '" class="field' . $can_edit . $is_message . '">';
          $field_echo .= '<h3>' . $field_obj['label'] . '</h3>';
          $field_echo .= '<div class="field-data">';
          if(empty($field_obj['value']) && ($user_id == $current_user_id)) { //Field does not have a value assigned
            if($field_obj['type'] == "message") {
              $field_echo .= '<div class="message"><p>' . $field_obj['message'] . '</p></div>';
            }
            else {
              $field_echo .= '<div class="field-content"><p>Instructions: ' . $field_obj['instructions'] . '</p><p class="add-response button">Add your response</p></div>'; 
            }
          }
          else { //Field does have a value
            if(is_array($field_obj['value'])) { //Advanced field, stored as array
              $i = 0;
              $val_output = ""; 

              $val = $field_obj['value'];

              if(!empty($val['label'])) { //Select field stored as array
                $val_output .= $val['label'];
              }
              else {
                foreach($field_obj['value'] as $val) {
                  if(is_array($val))  { //repeater field
                    if(key($val) == "group") { //group inside repeater field
                      $group = current($val);
                      $val_output .= '<div class="content-group"><h4>' . current($group) . '</h4>'; //headline
                      $val_output .= next($group) . '</div>';
                    } 
                    else { //Not a group field
                      if(empty($val_output)) $val_output .= "<ol>";
                      if(is_array($val)) {
                        $output_temp = current($val);
                        if(is_array($output_temp)) {
                          $val_output .= "<li>" . current($output_temp) . " : " . next($output_temp) . "</li>";
                        }
                        else {
                          $val_output .= "<li>" . current($val) . "</li>";
                        }
                      }
                      else {
                        $val_output .= "<li>" . $val. "</li>";
                      }
                    }
                  }
                  else {
                    //var_dump($val);
                    if($i > 0) {
                      $val_output .= ", " . $val;
                    }
                    else {
                      $val_output .= $val;
                    }
                  }
                  $i++;
                }
              }

              $field_echo .= '<div class="field-content">' . $val_output . '</div>';
            }
            else { //Simple text field
              $field_echo .= '<div class="field-content">' . $field_obj['value'] . '</div>';
            }
          }

          if($user_id == $current_user_id) {
            $settings = array(
              'post_id' => 'user_' . $user_id,
              'html_updated_message' => '',
              'fields' => array($field),
              'form_attributes' => array(
                'class' => 'clearfix'
              ),
              'id' => 'form_' . $field_obj['name']
            );
            ob_start();
            acf_form($settings);
            $form = ob_get_clean();
            $field_echo .= $form; 
          }
          $field_echo .= '</div>'; //end field-data          
          $field_echo .= '</div>'; //end .field
        }
      }

      if($fields_with_val > 0 ) { //Show if values have been filled in by user :
          $fields_echo .= "<h2>" . $field_group[0] . "</h2>" . $field_echo;
      }

      $fields_echo .= '</div>'; //end field group
    }
   
    //Add RCP profile and purchase history sections
    if($user_id == $current_user_id) {
      $echo .= '<div class="field-group" id="field-group-life-assessment">';
      ob_start();
      echo do_shortcode('[life_assessment]');
      $echo .= '<div class="field">' . ob_get_clean() . '</div>';
      $echo .= '</div>';

      $echo .= '<div class="field-group" id="field-group-edit-account">';
      ob_start();
      echo do_shortcode('[edd_profile_editor]');
      $echo .= '<div class="field">' . ob_get_clean() . '</div>';
      $echo .= '</div>';

      $echo .= '<div class="field-group" id="field-group-purchase-history">';
      ob_start();
      echo do_shortcode('[purchase_history]');
      echo '<div style="margin-top: 30px;">' . do_shortcode('[edd_subscriptions]') . '</div>';
      $echo .= '<div class="field">' . ob_get_clean() . '</div>';
      $echo .= '</div>';
    }

    if($fields_with_val > 0) {
      $echo = $nav_echo . $echo . $fields_echo;
    }
    else {
      $echo = $echo;
    }

    /* Set up main */
    $echo = '<div class="main" active-field="' . $active_field . '">' . $echo . '</div>'; //Close main


    $profile_image = get_field('ryit_user_profile_image', 'user_' . $user_id);
    $country = get_field('field_59e748b89988c', 'user_' . $user_id);
    $city = get_field('field_5a4be6cf193be', 'user_' . $user_id);
    $dob = get_field('field_5a5a255e8bae6', 'user_' . $user_id);
    $triad = get_field('field_5bdc3a83b2f62', 'user_' . $user_id);

    ob_start();    
    if(!alumnus_sidebar_empty($user_id) || $user_id == $current_user_id) :
    ?>
    <div id="sidebar">
      <div id="user-data">
        <!-- Set up profile fields batch #1 -->
        <?php if(!empty($profile_image)) : ?>
        <div class="portrait">
          <?php 
            echo '<img src="' . $profile_image . '" />';
          ?>
        </div>
        <?php endif; ?>

        <?php
          
          $intensity = get_field('user_ryit_intensity', 'user_' . $user_id);          
          $intensity = $intensity['value'];
          if(!empty($intensity)) {
            $legend_echo .= '<div id="legend-intensity" class="' . $intensity . '"></div>';
          }

          if(ryit_user_is_current() || ryit_user_is_alumnus()) {
            $legend_echo .= '<div id="legend-ryit"></div>';
          }
        ?>

        <?php if(!empty($legend_echo)) : ?>
        <div id="legend">
          <?php echo $legend_echo; ?>
        </div>
        <?php endif; ?>

        <!-- Set up profile fields batch #2 -->
        <ul class="personal-data">
          <?php
            if(!empty($country)) {
              echo '<li><p class="description">Country</p><p class="data">' . $country . '</p></li>';
            }

            if(!empty($city)) {
              echo '<li><p class="description">City</p><p class="data">' . $city . '</p></li>';
            }

            if(!empty($dob)) {
              $date = explode("/", $dob);
              $time = mktime(0,0,0,$date[1],$date[2],$date[0]);
             echo '<li><p class="description">Date of birth</p><p class="data">' . date('F d, Y',$time) . '</p></li>'; 
            }

            echo '<li><p class="description">E-mail</p><p class="data"><a href="mailto:' . $user_data->user_email . '">' . $user_data->user_email . '</a></p></li>'; 

            /* Life assessment output */
            $total_avg = get_user_meta($user_id, 'ryit_user_life_assessment_total_average', true);
            $mind_avg = get_user_meta($user_id, 'ryit_user_life_assessment_mind_average', true);
            $body_avg = get_user_meta($user_id, 'ryit_user_life_assessment_body_average', true);
            $people_avg = get_user_meta($user_id, 'ryit_user_life_assessment_people_average', true);
            $purpose_avg = get_user_meta($user_id, 'ryit_user_life_assessment_purpose_average', true);

            echo '<li id="life-assessment">';
            if(!empty(get_user_meta($user_id, 'ryit_user_life_assessment_total_average'))) {
              echo '<p class="description">Life assessment</p>';
              echo '<ul>';
              if(!empty($mind_avg)) {
                echo '<li><span>' . $mind_avg . '</span><p>Mind</p></li>';
              }
              if(!empty($body_avg)) {
                echo '<li><span>' . $body_avg . '</span><p>Body</p></li>';
              }
              if(!empty($people_avg)) {
                echo '<li><span>' . $people_avg . '</span><p>People</p></li>';
              }
              if(!empty($purpose_avg)) {
                echo '<li><span>' . $purpose_avg . '</span><p>Purpose</p></li>';
              }
              if(!empty($total_avg)) {
                echo '<li><span>' . $total_avg . '</span><p>Average</p></li>';
              }

              //echo '<li><span style="background-color: rgba(100, 140, 140, 1);">1-5</span><p>Scale is:</p></li>';
              echo '</ul>';
            }
            echo '</li>';

            if(!empty($triad) && get_field('ryit_current_week','options') >= 3) {
              $post_id = $triad->ID;
              echo '<div id="banner"><div id="banner-symbol-bg" style="background-color:' . get_field('ryit_triad_color', $post_id) . '"><div id="banner-symbol-texture"></div><div id="banner-symbol-texture-layer2"></div><div id="banner-symbol" style="background-image: url(' . get_field('ryit_triad_banner_symbol', $post_id) . ')"></div></div>';
              echo '<h3 id="banner-name">' . get_the_title($post_id) . '</h3>';
              echo '</div>';

              $alumni = rcp_get_members('free',2,0,999999,'ASC');
              $current = rcp_get_members('free',3,0,999999,'ASC');
              $users = array_merge($alumni,$current);

              foreach($users as $user) {
                if(!isset($triad_members)) { $triad_members = array();  }
                $triad = get_field('user_ryit_triad', 'user_' . $user->ID);
                if($user->ID == 84) continue; //skip dev account
                if($triad->ID == $post_id) {
                  $triad_members[] = $user->ID;
                }
              }

              foreach($triad_members as $member_id) {
                $user_info = get_userdata($member_id);
                $triad_member_html .= '<div class="triad-member">';
                $triad_member_html .= '<a href="/user-profile?user_id=' . $user_info->ID . '"><div class="portrait" style="background-image: url(' .  get_field("ryit_user_profile_image", "user_" . $member_id) . ')"></div>';
                $triad_member_html .= '<h4>' . $user_info->first_name . ' ' .  $user_info->last_name . '</a></h4>';
                $triad_member_html .= '</div>';
              }
              if(count($triad_members) < 3 && $user_id == $current_user_id) {
                $triad_member_html = '<div class="triad-members">' . $triad_member_html . '<p style="text-align: center;">Your triad is not complete. Go <a href="/the-brotherhood">claim your triad members</a>.</div>';
              }
              else {
                $triad_member_html = '<div class="triad-members">' . $triad_member_html . '</div>'; 
              }

            ?>
              <script type="text/javascript">     
                $j(document).on('click', '#banner', function(e) {
                  e.preventDefault();
                  console.log("test");
                  if($j('#ryit_popup').length <= 0) {
                  $j('body').append('<div id="ryit_popup_overlay"></div><div id="ryit_popup"><div id="popup-banner"><img src="<?php echo get_field('ryit_triad_banner', $post_id); ?>" /></div><div id="popup-content"><h2><?php echo get_the_title($post_id); ?></h2><div id="triad-mission"><?php echo trim(get_field('ryit_triad_mission', $post_id)); ?></div><h3>Members</h3><?php echo $triad_member_html; ?><a href="#" class="close"><span class="fa fa-times"></span></a></div></div>');
                  }
                  else {
                    $j('#ryit_popup').fadeIn(500);
                    $j('#ryit_popup_overlay').fadeIn(500);
                  }
                  var scrollTop = $j(document).scrollTop();
                  $j("#ryit_popup").css("top", scrollTop + 50);
                });

                $j(document).on('click', 'a.close', function(e) {
                  e.preventDefault();
                  $j('#ryit_popup').fadeOut(500);
                  $j('#ryit_popup_overlay').fadeOut(500);
                });
              </script>
            <?php
            }
          ?>
        </ul>
        <?php
          if($user_id == $current_user_id) :
        ?>
            <div id="edit-user-data" class="button" style="margin-bottom: 30px;">Edit profile data</div>
        <?php
          endif;
          echo '<p style="text-align: center;">Back to <a href="/the-fellowship">"The Fellowship"</a></p>';
        ?>
      </div> <!-- End User data -->
  <?php
    endif;

  //Sidebar when edited

    if($user_id == $current_user_id) {
      echo '<div id="user-data-form-wrapper">';
      
      $fields = array(
        'field_5a576ed8b86eb', //image
        'field_59e748b89988c', //country
        'field_5a4be6cf193be', //city
        'field_5a5a255e8bae6', //birthdate
        'field_5bdc3a83b2f62' //triad
      );

      $settings = array(
        'post_id' => 'user_' . $user_id,
        'html_updated_message' => '',
        'fields' => $fields,
        'id' => 'user-data-form'
      );
      
      ob_start();
      acf_form($settings);
      echo ob_get_clean(); 
      echo '<div class="button cancel" id="edit-user-data-cancel">Cancel</div>';
      echo '</div>'; //end form wrapper
    }

    if(!alumnus_sidebar_empty($user_id) || $user_id == $current_user_id) {
      echo '</div>'; //end sidebar
    }

    echo '</div>';

    $fields = null;

    $echo .= ob_get_clean();

    //Retrieve the correct fields for the profile page


    if($fields_with_val <= 0 ) { //No values filled in by user
      $echo .= '<div id="profile" class="incomplete">';
      $echo .= '<h3 style="text-align: center; margin-bottom: 1em;">' . $user_data->first_name . ' has not filled in his profile.</h3>';

      if(empty(get_field('user_ryit_triad', 'user_' . $user_id))) {
        $echo .= '<h2 style="margin-top: 30px; padding-left: 0;">' . $user_data->first_name . ' needs a triad</h2>';
        $echo .= '<p>Is ' . $user_data->first_name . ' in your triad? Then ask him to register his membership in the system! (remember to register for the triad yourself too!) :)';
        $echo .= '<div id="triad-member-claim" style="padding: 15px 0;"><div class="fusion-button simple" id="button-claim-triad-member" style="margin: 0 auto; display: table;">Claim ' . $user_data->first_name . ' for your Triad</div></div>';
        $echo .= '<p class="clear" style="font-style: italic; margin-top: 30px; color: #999;">NB! Clicking this button will send an e-mail to ' . $user_data->first_name . ' notifying you of your wish to have him fill in his profile and assign himself to your triad. Don\'t use it if he is not in your triad.</p>';
      }
      //$echo .= '<div id="send-profile-challenge"><p>Challenge ' . $user_data->first_name . '</p></div>';
      $echo .= '</div>';
    }

    $display_sidebar = (alumnus_sidebar_empty($user_id) && $user_id != $current_user_id) ? " " : " display_sidebar";
    $echo = '<div id="profile" user_id="' . $user_id . '" class="clearfix ' . $display_sidebar . '"><h1>' . $user_data->first_name . ' ' . $user_data->last_name . '</h1>' . $echo . '</div>';
  }

  return $echo;
}

add_shortcode('alumnus_profile', 'ryit_alumnus_profile');


function ryit_life_assessment() {
  $assessment_tool = get_field('ryit_life_assessment_tool', 'options');

  ob_start();
?>

  <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/rangeslider.js-2.3.0/rangeslider.min.js"></script>
  <script type="text/javascript">
    $j = jQuery.noConflict();

    $j('document').ready(function() {
      $j('input[type="range"]').rangeslider({
        polyfill : false/*,
        onInit : function() {
          this.output = $( '<div class="range-output" />' ).insertAfter( this.$range ).html( this.$element.val() );
        },
        onSlide : function( position, value ) {
          this.output.html( value );
        }*/
      });

      $j('input[type="range"]').val(3).change(); //ensure that slider is drawn correctly
      $j('input[type="range"]').rangeslider('update', true);

      var dimension_group = $j('#dimension-groups > div:first-of-type');
      var is_first_dimension = true; //track if it's the first dimension in the group
      var dimension;
      var dimension_next;
      var group_metrics = "";
      var full_metrics = "";

      $j('#progress_button').on('click', function() {
        if($j('#assessment-intro').length > 0) { //Intro is showing. Fade it out and show the first assessment group
          $j('#progress_button').text('Continue'); //update button text
          $j('#assessment-intro').fadeOut(300, function(){
            $j('#assessment-intro').remove(); //remove general intro slide
            dimension_group.addClass('intro show'); //show dimension group intro slide
            update_menu_highlight(dimension_group.children('h2').text().toLowerCase());
          });
        } 
        else {
          show_next_dimension(dimension_group);
        }
      });

      function update_menu_highlight(dim_group) {
        $j('#life-assessment-tool ul li').removeClass('active');
        $j('#life-assessment-tool ul').find('.' + dim_group).addClass('active');
      }

      function show_next_dimension(dim_group) {
        dimension = dim_group.children('div:first-of-type');
        dimension_next = dimension.next('.dimension');

        //Store value
        if(!dimension_group.hasClass('intro')) {
          group_metrics += dimension.find('span.var-name').text() + "," + dimension.find('input').val().toString() + "|";
        }

        if(dimension_next.length <= 0) { //last dimension in group, so switch groups and continue
          dimension.fadeOut(500, function() {
            dimension.remove();
            dim_group.remove();
            $j('#progress_button').text('Start section'); //update button text

            //Handle metric strings
            group_metrics = group_metrics.slice(0,-1); //remove last comma in string
            if($j('#dimension-groups div').length > 0) { //More groups exist
              full_metrics += group_metrics + ";";
            }
            else {
              full_metrics += group_metrics;
            }

            group_metrics = "";

            if($j('#dimension-groups div').length > 0) { //More groups exist
              dimension_group = $j('#dimension-groups > div:first-of-type');
              dimension_group.addClass('show intro');
              update_menu_highlight(dimension_group.children('h2').text().toLowerCase());
              is_first_dimension = true;
            }
            else { //This was the last group - complete assessment 
              var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';

              var data = {
                action: 'save_assessment_results',
                full_metrics: full_metrics,
                user_id: $j('body').attr('user_id')
              };

              $j('#progress_button').fadeOut(500);

              //Remove existing popup from source, if it exists
              if($j('#ryit_popup').length > 0) {
                $j('#ryit_popup_overlay').remove();
                $j('#ryit_popup').remove();
              }

              //Create new popup
              $j('body').append('<div id="ryit_popup_overlay"></div>');
              $j('body').append('<div id="ryit_popup" class="assessment"><h3>Saving assessment results</h3><a href="#" class="close"><span class="fa fa-times"></span></a></div>');
              var scrollTop = $j(document).scrollTop();
              $j("#ryit_popup").css("top", scrollTop + 100);

              $j.ajax({
                url: ajaxurl,
                type: 'GET', // the kind of data we are sending
                data: data,        
                dataType: 'json',
                success: function(response) {
                  $j('#ryit_popup').html('<h3>Results successfully saved</h3><p>Reloading page to display new results...</p>');
                  location.reload();
                  /*
                  setTimeout(
                    function() {
                      $j(document).find('#ryit_popup').fadeOut(1000, function() {
                        $j(document).find('#ryit_popup').remove();
                      });
                      $j(document).find('#ryit_popup_overlay').fadeOut(1000, function() {
                        $j(document).find('#ryit_popup').remove();
                      });
                    }, 1000
                  );
                  */
                }, error: function() {
                  $j('#ryit_popup').html('<h3>Oops! We couldn\'t save your data.</h3><p>Please contact <a href="https://innerthrone.kartra.com/help/helpdesk" target="_blank">Helpdesk</a></p>');
                }
              }); 
            }
          });
        }
        else {
          if(is_first_dimension && dim_group.hasClass('intro')) {
            dim_group.fadeTo(500, 0, function() { //Fade out group title
              switch_dimension_text();
              dim_group.removeClass('intro');
              dim_group.fadeTo(500, 1);
              dimension.addClass('show');
            });
          }
          else { //Normal dimension iteration
            dim_group.fadeTo(500,0, function() {
              dimension.remove();
              if(dimension_next.length > 0) {
                dimension = dimension_next;
                switch_dimension_text();
                dimension.addClass('show');
                dim_group.fadeTo(500,1);
                is_first_dimension = false;
              }
            });
          }
        }

        function switch_dimension_text() {
          /* SWITCH DIMENSION HEADLINE & DESCRIPTION */
          
          var dimension_headline = dimension_group.children('h2');
          var dimension_description = dimension_group.children('.description');

          //Switch dimension headline
          dimension_headline.text(dimension.children('h3').text());
          dimension_description.text(dimension.children('.description').text());

          if(dim_group.find('.dimension').length <= 1) {
            $j('#progress_button').text('Complete section'); //update button text
          }
          else {
            $j('#progress_button').text('Continue'); //update button text
          }
        }
      }
    });
  </script>

<?php
 $script = ob_get_clean();
 $echo = $script;

  $echo .= '<div id="life-assessment-tool">';
  $echo .= '<div id="assessment-tool-header"><h3>Life Assessment Tool</h3><ul><li class="mind">Mind</li><li class="body">Body</li><li class="people">People</li><li class="purpose">Purpose</li></ul></div>';
  //$echo .= '<img src="' . get_stylesheet_directory_uri() . '/images/four-quadrant-assessment-circle.png" id="quadrants">';
  $echo .= '<div id="assessment-intro">';
  $echo .= '<div class="maxwidth-600"><h2>Assess the Quality of your Life</h2><p>Using this simple assessment tool, you can establish a clear idea of how you are doing in your life. You will be assessing yourself in the four dimensions of Mind, Body, People & Purpose, which is the official coaching framework of Reclaim your Inner Throne*.</p><p>Based on these metrics, you will be able to utilize our other tools and track your progress. What you track improves and so this is a simple yet crucial step on your journey.</p></p><blockquote>If you can\'t measure it, you can\'t improve it. &ndash; Peter Drucker</blockquote><p style="font-style: italic; color: #aaa; font-size: 0.9em;"><sup>*</sup>A Coaching framework is about goals and direction, and is drastically different to an initiation framework which is about death and rebirth. Though in effect coaching <em>can</em> inadvertently <em>become</em> an initiation, but it\'s not intended as such from the outset.</div>';
  $echo .= '</div>';
  $echo .= '<div id="dimension-groups">';
  foreach($assessment_tool as $group) {  
    $echo .= '<div class="dimension-group">';
    $echo .= '<h2>' . $group['group_name'] . '</h2>';
    $echo .= '<p class="description">' . $group['group_description'] . '</p>';
    $dimensions = $group['dimensions'];
    foreach($dimensions as $dimension) {
      $dimension = $dimension['dimension'];
      $echo .= '<div class="dimension"><h3 style="display: none;">' . $dimension['dimension_name'] . '</h3>';
      $echo .= '<div class="description" style="display: none;">' . $dimension['dimension_description'] . '</div>';
      $echo .= '<div class="slider"><input type="range" min="1" max="5" step="1" value="3" data-rangeslider role="input-range">' . $dimension['dimension'] . '</input></div>';
      $echo .= '<p class="min-text">' . $dimension['min_text'] . '</p>';
      $echo .= '<p class="max-text">' . $dimension['max_text'] . '</p>';
      $echo .= '<span class="var-name" style="display: none;">' . $dimension['dimension_var_name'] . '</span>';
      $echo .= '</div>';
    }
    $echo .= '</div>';
  }
  $echo .= '</div>';
  $echo .= '<ul class="scale-steps"><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li></ul>';
  $echo .= '<div id="progress_button" class="button simple">Start the Assessment</div>';
  $echo .= '</div>';
  return $echo;
}

add_shortcode('life_assessment', 'ryit_life_assessment');


add_action( 'wp_ajax_save_assessment_results', 'ryit_save_assessment_results' );
add_action( 'wp_ajax_nopriv_save_assessment_results', 'ryit_save_assessment_results' );
  
function ryit_save_assessment_results() {
  $full_metrics = $_REQUEST['full_metrics'];
  $full_metrics = explode(';', $full_metrics);
  $metric_sum = 0;
  $total_avg = 0;
  if(!empty($_REQUEST['user_id'])) {
    $user_id = $_REQUEST['user_id'];
  }
  else {
    $user_id = 1;
  }
  
  for($i=0; $i < count($full_metrics); $i++) {
    $group_metrics = $full_metrics[$i];
    $group_metrics = explode('|', $group_metrics);

    foreach ($group_metrics as $metric) {
      $metric = explode(',', $metric);
      update_user_meta($user_id, $metric[0], $metric[1]);
      $metric_sum += intval($metric[1]);
      if(!next($group_metrics)) { //extract if group is Mind,Body,People or Purpose based on variable names
        $var_name = $metric[0];
        $var_name = explode("_", $var_name);
        $var_name = $var_name[4];
      }
    }

    //Calculate averages
    $group_avg = round($metric_sum / count($group_metrics),1);
    $total_avg += $group_avg;

    update_user_meta($user_id, 'ryit_user_life_assessment_' . $var_name . '_average', $group_avg);
    
    $group_avg = $metric_sum = 0; //Reset counters
  }

  $total_avg = round($total_avg / count($full_metrics),1);
  update_user_meta($user_id, 'ryit_user_life_assessment_total_average', $total_avg);
} 


/*

function my_acf_settings_capability( $path ) {
    return false;
}

add_filter('acf/settings/capability', 'my_acf_settings_capability');
*/

//Modify Triad Post Object field to prevent user to be able to choose triad from different rounds

function ryit_post_object_query( $args, $field, $post_id ) {
  $user_id = get_current_user_id();

  $query_args = array('post_type' => 'triads', 'posts_per_page' => -1);
  $triads = get_posts($query_args);
  $triads_in_round = array();

  foreach($triads as $triad) {
    if(get_field('ryit_round_number', 'user_' . $user_id) == get_field('ryit_round_number', $triad->ID)) {
      $triads_in_round[] = $triad->ID;
    }
  }

  // only show children of the current post being edited
  $args['post__in'] = $triads_in_round;
  
  // return
  return $args;
}


// filter for every field
add_filter('acf/fields/post_object/query/name=user_ryit_triad', 'ryit_post_object_query', 10, 3);


function alumnus_sidebar_empty($user_id) {
  $profile_image = get_field('ryit_user_profile_image', 'user_' . $user_id);
  $country = get_field('field_59e748b89988c', 'user_' . $user_id);
  $dob = get_field('field_5a5a255e8bae6', 'user_' . $user_id);
  if(empty($profile_image) && empty($country) && empty($dob)) {
    return true;
  }
  else {
    return false;
  }
}

function validate_gravatar($email) {
  // Craft a potential url and test its headers
  $hash = md5(strtolower(trim($email)));
  $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
  $headers = @get_headers($uri);
  if (!preg_match("|200|", $headers[0])) {
    $has_valid_avatar = FALSE;
  } else {
    $has_valid_avatar = TRUE;
  }
  return $has_valid_avatar;
}




/************ E-MAIL FUNCTIONS *******************/


add_action( 'wp_ajax_email_ping_triad_member', 'ryit_email_ping_triad_member' );
add_action( 'wp_ajax_nopriv_email_ping_triad_member', 'ryit_email_ping_triad_member' );


function ryit_email_ping_triad_member() {
  if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  else {
    return false;
    die();
  }
  $user_data_to = get_userdata($user_id);
  $user_data_from = get_userdata(get_current_user_id());
  $subject = $user_data_from->first_name . ' says you\'re in his triad, ' . $user_data_to->first_name . '! (open to see new features)';
  $message = '<p>Hello ' . $user_data_to->first_name .'!</p>'; 
  $message .= '<p>A new feature has launched on the Reclaim your Inner Throne platform and ' . $user_data_from->first_name . ' is eager for you to use it.</p>';
  $message .= '<p>Please <a href="https://www.inner-throne.com/member_login">log in</a> as you normally do and then go to your <a href="https://www.inner-throne.com/user-profile/">User Profile</a> under My Accounts. Assign yourself to the right triad, feed your results from early work on this training into the system and ensure that all your triad members join. This is the start of an amazing new feature, that will only expand in the coming weeks.</p>';
  $message .= '<p>(You can also find a complete list of the men in your training on the new <a href="https://www.inner-throne.com/the-brotherhood">Brotherhood page</a>.)</p>';
  $message .= '<p>Come next week (November 12), we\'ll give you a proper introduction to all these new features (and where we\'ll take them next)!</p>';
  $message .= '<p>See you on <a href="https://www.inner-throne.com/member_login">the Course Pages</a>!</p>';
  $message .= '<p><em>The Reclaim your Inner Throne Bot,<br/>on behalf of ' . $user_data_from->first_name . ' ' . $user_data_from->last_name . '</em></p>';
  $headers[] = "From: RYIT Bot <support@inner-throne.com>";
  wp_mail($user_data_to->user_email, $subject, $message, $headers);
  $message = '<p>Hi ' . $user_data_from->first_name . '!</p><p>This is just a friendly notification to inform you that your message to ' . $user_data_to->first_name . ' ' . $user_data_to->last_name . ' was successfully delivered :)</p>';
  $message .= '<p><em>Sincerely<br/>The Reclaim your Inner Throne bot</em></p>';
  wp_mail($user_data_from->user_email, 'Your message was delivered', $message, $headers);
  die();
}

function wpse27856_set_content_type(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','wpse27856_set_content_type' );

/*

add_action( 'wp_ajax_send_profile_challenge', 'ryit_send_profile_challenge' );
add_action( 'wp_ajax_nopriv_send_profile_challenge', 'ryit_send_profile_challenge' );

function ryit_send_profile_challenge() {
  check_ajax_referer('my_email_ajax_nonce');
  if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  }
  $user_data = get_userdata($user_id);
  $to = "eivind@enigmation.com";
  $subject = $user_data->first_name . " " . $user_data->last_name . 'wants to know you better!';
  $message = 'Hello 'There is a great service that you\'re not using and' . $user_data->first_name . ' wants you to. If you don\'t want to be part of using this service, then you can disable your profile in the system';
  //$headers = 'From: Reclaim your Inner Throne <support@inner-throne.com>' . '\r\n';
  wp_mail($to, $subject, $message);
  echo 'email sent';
  die();
}
*/


function preload_spinner() {
?>
  <div class="lds-css ng-scope">
  <div class="lds-spinner" style="width: 100%; height:100%"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
<?php
}


/* ******************** */
/*       JGS CODE       */
/* ******************** */

function jgs_user_interface() {
    $user_id = get_current_user_id();
    echo "<ul id='jgs_interface' class='hide'>";
    jgs_embed_jplayer();
    echo '<li id="jgs_challenge_box_menuitem"><img src="' . get_site_url() .  '/wp-content/uploads/2018/01/jgs-magic-box-150x100.png"></li>';
    //echo '<li id="jgs_inventory_system"><img src="' . get_stylesheet_directory_uri() . '/images/burlap-bag.png">'; 
    $week_id = get_field('jgs_week_id');
    $progress_week = get_field('jgs_user_data', 'user_' . $user_id);
    $progress_week = $progress_week['progress'];
    $progress_week = explode(",", $progress_week);
    $progress_week = $progress_week[0];
    if($progress_week >= 3 && $week_id > 2) {
        echo "<li id='jgs_echo_system'><img src='" . get_stylesheet_directory_uri() . "/images/echo-system-icon.png' /></li>";
    }
    
    $draco_count = ryit_get_reward_balance();
    if(!empty($draco_count)) {
        $draco_echo = "<div><span>" . $draco_count . "</span></div>";
    }
    echo "<li id='jgs_draco'>" . $draco_echo . "<img src='" . get_stylesheet_directory_uri() . "/images/coin-stack.png' /></li>";
    echo "<li id='jgs_forum'><a href='/forums/forum/jgs-beta/' target='_blank'><img src='" . get_stylesheet_directory_uri() . "/images/forum-icon.png' /></a></li>";
    echo "</ul>";
}


function jgs_character_interview() {
  global $post;
    $character = get_field('jgs_character',$post->ID);
    if($character) {
        $name = $character['name'];
        $description = $character['description'];
        $portrait_url = $character['portrait'];
        $greeting = $character['greeting'];
        $response = $character['response'];
        $output = "<div class='jgs_character_interview'>";
        $output .= "<div class='portrait'><img src='" . $portrait_url . "' width='300' height='300' /></div>";
        $output .= "<h2>" . $name . "</h2>";
        $output .= "<h4>" . $description . "</h4>";
        if(jgs_user_has_answered()) {
            $output .= "<div class='greeting'>" . $response . "</div>";
        }
        else {
            $output .= "<div class='greeting'>" . $greeting . "</div>";        
        }

        $response_none = get_field('jgs_character_length_responses_response_none', $post->ID);
        $response_short = get_field('jgs_character_length_responses_response_short');
        $response_medium = get_field('jgs_character_length_responses_response_medium');
        $response_long = get_field('jgs_character_length_responses_response_long');
        $output .= "<input id='echo_length_responses' type='hidden' response_none='" . $response_none . "' response_short='" . $response_short . "' response_medium='" . $response_medium . "' response_long='" . $response_long . "'>";
        $output .= "</div>";
        return $output;
    }
}

add_shortcode('jgs_character_interview', 'jgs_character_interview');


function jgs_variable_text($atts = [], $content = null) {
    $args = shortcode_atts( 
        array(
          'type' => 'gender',
          'value' => 'default',
          'ne' => false, //show if NOT equal
          'fallback' => ''
        ), 
        $atts
    );

    $type = $args['type'];
    $value = $args['value'];
    $display_if_not_equal = $args['ne'] == "true" ? true : false;
    $user_id = get_current_user_id();

    if($type == 'gender') {
        $type_val = get_field('ryit_user_profile_gender', 'user_' . $user_id);
        if(($type_val == $value && !$display_if_not_equal) || ($type_val != $value && $display_if_not_equal)) {
            $content = do_shortcode($content);
            return $content;
        }
    }
    if($type == 'last_id') {
        $last_id = get_field('jgs_user_data_last_choice_id', 'user_' . $user_id);
        if(strpos($type,",")) {
            $vals = explode(",",$type);
            if((in_array($last_id,$vals) && !$display_if_not_equal) || (!in_array($last_id,$vals) && $display_if_not_equal)) {
                $content = do_shortcode($content);
                return $content;
            }        
        }
        else {
            if(($last_id == $value && !$display_if_not_equal) || ($last_id != $value && $display_if_not_equal)) {
                $content = do_shortcode($content);
                return $content;
            }
        }
    }

    return false;
}

add_shortcode('jgs_var_text', 'jgs_variable_text');
add_shortcode('jgs_var_text_nested', 'jgs_variable_text');


function jgs_retrieve_fragment($atts) {
    extract(shortcode_atts(array(
          'value' => '1'
       ), $atts));

    $user_id = get_current_user_id();

    $fragments = get_field('jgs_fragments', 'user_' . $user_id);

    if($value) {
        $fragment = $fragments['fragment_' . $value];
        if($fragment) {
            return $fragment;
        }
    }

    return false;
}

add_shortcode('jgs_retrieve_fragment', 'jgs_retrieve_fragment');




//Journey continues button

function jgs_continue_btn_func($atts) {
    extract(shortcode_atts(array(
          'step' => '1',
          'label' => 'Continue your Journey',
          'finalize_week' => false,
       ), $atts));

    if($finalize_week) {
        $finalize = " finalize='true'";
    }

    //HTML
    $echo = "<div class='continue-button' step='" . $step . "'" . $finalize . ">";
    $echo .= "<a href='#'>";
    $echo .= "<span>" . $label . "</span>"; 
    $echo .= "</a>";
    $echo .= "</div>";

    return $echo;
}

add_shortcode('jgs_continue_btn', 'jgs_continue_btn_func');



function jgs_echo_archives($params) {

    // compensating for inability to send booleans with shortcode

    switch ($params['display']) {
        case 'input':
            $hide_input = false;
            $hide_output = true;
            break;
        case 'all':
            $hide_input = false;
            $hide_output = false;
        case 'comments':
            $hide_input = true;
            $hide_output = false;
            break;
        default:
            $hide_input = true;
            $hide_output = false;
            break;
    }


    if($params['post_id']) {
        $post_id = $params['post_id'];
    }
    else {
        global $post;
        $post_id = $post->ID;
    }

    //end compensating

   /* Show comments */
    if ( get_comments_number($post_id) > 0 && !$hide_output) {
        $args = array('post_id' => $post_id);
        $comments = get_comments($args);  
        $echo = "";
        $echo .= "<div id='comments'>";

        foreach($comments as $comment) {
            $author_ID = $comment->user_id;      
            //echo "author id: " . $author_ID . "|";
            $gender = get_field('ryit_user_profile_gender', 'user_' . $author_ID);

            //Check ECHO-archives visibility settings
            $visibility = get_field('jgs_user_profile', 'user_' . $author_ID);
            $visibility = $visibility['echo_visibility'];
            if(empty($visibility) || $visibility == "") $visibility = "show"; 

 
             //Determine gender settings
            if($visibility != anonymous) {
                $avatar = get_field('ryit_user_profile_image', 'user_' . $author_ID);
                if($gender == 'man') {
                    $icon = "<span class='gender'><i class='fa fa-mars'></i></span>";  
                }
                else if($gender == 'woman') {
                    $icon = "<span class='gender'><i class='fa fa-venus'></i></span>";
                }
                else {
                    $icon = "";
                }
            }


            //Determine name settings
            if($visibility == "show") {
                $name = $comment->comment_author;
                $city = get_field('ryit_user_profile_city', 'user_' . $author_ID);
                $country = get_field('ryit_user_profile_country', 'user_' . $author_ID);
                if($city) {
                    $location = $city;
                    if($country) {
                        $location .= ", " . $country;
                    }
                }
                else {
                    if($country) {
                        $location = $country;
                    }
                }
                $location = "<span class='location'>" . $location . "</span>";
            }
            if($visibility == "anonymous") {
                $name = get_field('ryit_user_profile_nickname', 'user_' . $author_ID);
                if(!$name) {
                    $name = "Anonymous";
                }
                $location = "";
            }


            if($visibility == "show" || $visibility == "anonymous") {
                $echo .= "<div class='comment'>";
                $placeholder = "";
                if(empty($avatar)) {
                    if($gender == "man") {
                        $placeholder = " placeholder-man";
                    }
                    else if($gender == "woman") {
                        $placeholder = " placeholder-woman";
                    }
                    else {
                        $placeholder = " placeholder-default";
                    }
                }
                $echo .= "<div class='avatar" . $placeholder . "'><img src='" . $avatar . "' /><span class='date'> Posted on" . get_comment_date('M j, Y', $comment->comment_ID) . "</span></div>";                
                $echo .= "<div class='text'>";
                $echo .= "<h3>" . $name . "</h3>";
                $echo .= "<div class='meta'>" . $icon . $location . "</div>";
                $echo .= "<div class='body'>" . apply_filters('the_content', $comment->comment_content) . "</div>";
                $echo .= "</div>";
                $echo .= "</div>";
            }

            unset($avatar,$name,$gender,$city,$country);
        }

        $echo .= "</div>";
    }
    else {
        if($hide_input) {
            $echo .= "<div><p>No comments in the ECHO-archives for this week.</p></div>";
        }
    }

    /* Show comment form, if no comment has already been added */

    //no answer has been given, print out input field
    if(!jgs_user_has_answered() && !$hide_input) {
        $label_submit = get_field('jgs_character', $post->ID);
        $label_submit = "Respond to " . $label_submit['name'];

        ob_start();
        $args = array(
            'label_submit' => $label_submit,
            'title_reply' => '',
            'logged_in_as' => '',
            'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="jgs_echo_comment" name="comment" cols="45" rows="8" aria-required="true">The ECHO-system awaits you...</textarea></p>'
        );
        comment_form($args, $post->ID);
        $echo .= ob_get_clean();
    }

    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        $echo .= '<p class="no-comments">' . esc_html_e( "Comments are closed.", "Avada" ) . '</p>';
    endif;

    return $echo;
}

add_shortcode('jgs_echo_archives', 'jgs_echo_archives');




//Fragments of the past form

function jgs_fragments() {
    $user_id = get_current_user_id();
    $fragments_count = 6;
    $fragments = get_field('jgs_fragments','user_' . $user_id);

    for($i=1;$i<= $fragments_count;$i++) {
        $field = $fragments['fragment_' . $i];
        if(empty($field)) {
            break;
            $form_complete = false;
        }
        else {
            $form_complete = true;
        }
    }
    $settings = array(
        'fields' => array('field_5a6518b25fd3d'),
        'id' => 'acf_fragments_form',
        'post_id' => 'user_' . $user_id
    );

    if($form_complete) {
        return "<div class='form_complete'>You have already shared your fragments. Edit them in the profile settings.</div>";
    }
    else {
        ob_start();
        acf_form($settings);
        return ob_get_clean();
    }
}

add_shortcode("jgs_fragments", "jgs_fragments");


//Fragments of the past form

function jgs_acf_input_form($atts) {
    acf_form_head();
    $atts = shortcode_atts(
        array(
            'fields' => '',
            'return_msg' => 'Thank you',
            'require' => false,
            'submit_message' => "",
            'style' => 'default'
        ), $atts, 'jgs_input_form');

    $user_id = get_current_user_id();
    $fields = explode(",", $atts['fields']);
    $msg = $atts['submit_message'];
    $require = $atts['require'];
    $style = $atts['style'];

    $form_complete = true; //set as default and if one field is not filled in, change to false
    $submit_value = "Update form";
    foreach($fields as $field) {
        if(empty(get_field($field, 'user_' . $user_id))) {
            $form_complete = false;
            $submit_value = "Save form";
        }
    }

    $form_attr = array();
    $classes = array();
    $classes[] = "style-" . $style;
    $classes[] = "acf-form";
    $classes[] = $form_complete ? "complete" : "incomplete";

    $form_attr['class'] = implode(" ", $classes);

    if($require == "all") {
        $form_attr['require'] = 'all';
    }

    $settings = array(
        'fields' => $fields,
        'id' => 'acf_input_form',
        'post_id' => 'user_' . $user_id,
        'html_updated_message' => $msg,
        'form_attributes' => $form_attr,
        'submit_value' => $submit_value
    );

    ob_start();
    acf_form($settings);
    return ob_get_clean();
}

add_shortcode('jgs_input_form', 'jgs_acf_input_form');


//Configure buttons for multiple choice sequences
function jgs_init_multichoice_buttons($atts) {
    $args = shortcode_atts( 
        array(
            'setup' => '',
            'mode' => "respond"
        ), 
        $atts
    );

    $mode = $args['mode']; //are the questions proactive or in response to a character? (respond/proactive) - implemented in the future
    $buttons_settings = $args['setup'];
    $buttons_settings = explode("|",$buttons_settings);
    $buttons_echo = "";

    foreach($buttons_settings as $button) {
        $vars = explode(";",$button);
        $button_label = $vars[0];
        $button_target = $vars[1];
        if(empty($vars[2])) {
            $button_js_callback = false; 
        }
        else {
            $button_js_callback = $vars[2];
        }
        $buttons_echo .= "<input type='hidden' class='button-settings' data-target='" . $button_target . "' data-js-callback='" . $button_js_callback . "' data-label='" . $button_label . "' style='display: none;'></input>";
    }
    $buttons_echo = "<form class='buttons-settings'>" . $buttons_echo . "</form>";

    return $buttons_echo;
}

add_shortcode('jgs_init_multichoice_buttons', 'jgs_init_multichoice_buttons');


function jgs_button_init($atts) {
    $args = shortcode_atts( 
        array(
            'value'   => 'Continue your Journey',
            'callback_func' => '',
        ), 
        $atts
    );

    $value = $args['value'];
    $callback_func = $args['callback_func'];
    //  call_user_func($call_func);
    if(!empty($callback_func)) {
        $callback_func = " data-function='" . $callback_func . "'";
    }
    
    return "<span class='button_label_placeholder'" . $callback_func . " style='display:none;'>" . $value . "</span>";
}

add_shortcode("jgs_btn_label", "jgs_button_init");


add_action( 'wp_ajax_update_lastid', 'jgs_update_lastid' );
add_action( 'wp_ajax_nopriv_update_lastid', 'jgs_update_lastid' );

function jgs_update_lastid() {
    $last_id = $_GET['last_id'];
    $user_id = get_current_user_id();
    update_field('jgs_user_data_last_choice_id', $last_id, 'user_' . $user_id);
    die();
}

add_action( 'wp_ajax_defeat_dragon', 'jgs_defeat_dragon' );
add_action( 'wp_ajax_nopriv_defeat_dragon', 'jgs_defeat_dragon' );

function jgs_defeat_dragon() {
    $dragon = $_GET['dragon'];
    $user_id = get_current_user_id();
    if($dragon == "redfang") {
        update_field('jgs_user_data_redfang_defeated', '1', 'user_' . $user_id);
    }
    else {
        update_field('jgs_user_data_beira_defeated', '1', 'user_' . $user_id);   
    }
    die();
}


add_action( 'wp_ajax_update_heatindex', 'jgs_update_heatindex' );
add_action( 'wp_ajax_nopriv_update_heatindex', 'jgs_update_heatindex' );

function jgs_update_heatindex() {
    $type = $_GET['type'];
    $amount = $_GET['amount'];
    $user_id = $_GET['user_id'];
    if($type === "fire" || $type === "ice") {
        $curr_amount = get_field('jgs_user_data_heat_index_' . $type, 'user_' . $user_id);
        if(!empty($amount)) {
            if(strpos($amount,"+") !== false) { //increase amount relatively
                $amount = str_replace("+","",$amount); //remove operator
                if($curr_amount + intval($amount) > 10) {
                    $new_amount = 10;
                }                
                else {
                    $new_amount = $curr_amount + $amount; 
                }
                //Update totals for this heat variable
                $total = get_field('jgs_user_data_heat_index_' . $type . '_total', 'user_' . $user_id);
                if(empty($total)) {
                    $total = 0;
                }
                update_field('jgs_user_data_heat_index_' . $type . '_total', $total + $amount, 'user_' . $user_id);
            }
            elseif(strpos($amount,"-") !== false) { //reduce amount relatively
                $amount = str_replace("-","",$amount); //remove operator
                if($curr_amount - intval($amount) <= 0) {
                    $new_amount = 0;
                }                
                else {
                    $new_amount = $curr_amount - $amount; 
                }
            }
            else {
                $new_amount = $amount; //set amount absolutely
            }
        }
        update_field('jgs_user_data_heat_index_' . $type, $new_amount,'user_' . $user_id);
    }
    $echo['type'] = $type;
    $echo['amount'] = $new_amount;
    wp_send_json_success($echo);
    die();
}


/*
function jgs_draco_collection_test() {
    $user_id = get_current_user_id();
    $draco_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);

    $echo = "<p><span class='fusion-dropcap dropcap dropcap-boxed' style='background-color:#1a80b6;'>Y</span>ou reach into your pocket  simple creature like yourself cannot speak with someone as magnificent as Redfang without paying a price course.</p><p>&quot;Since I like you, I will only charge you 10 draco. MY money. <em>Give it to me</em>!&quot; ";
    $echo .= "You fumble through your pockets and come up with " . $draco_balance . " draco. ";

    if($draco_balance < 10 && $draco_balance > 0) {
        $echo .= "You put the money on the altar.</p>";
        $echo .= "<p>&quot;I said TEN draco, " . do_shortcode('[ryit_name]') . "! <strong>Give me my money!</strong>&quot;</p>";
    }
    else if($draco_balance > 10) {
        $echo .= "You put 10 draco on the altar</p>";
    }
    else {
        $echo .= "</p><p>&quot;I have nothing,&quot; you think to yourself. &quot;Oh shit...&quot;</p>";
        $echo .= "<p>&quot;So you have nothing? You have NOTHING?!&quot; The last word ripples through the air as a shockwave, knocking the air out of your lungs.";
        $echo .= "<p>&quot;So you must think you are very special, facing off with Redfang without having followed the instructions of your little friends. You like to do things your own way I see. Yes, you're so grand. So wonderful. Oh yes, I see it! You're so powerful and special, just like me. And you know better than everyone. EVERYONE!</p><p>Why don't people just love you, worship you? Yes, it's a good question, is it not? Why don't they bow at your feet, as they do to mine - for surely no-one is as wonderful as " . do_shortcode('[ryit_name]') . ", the Chosen One who would prevent the Dragon Wars by doing NOTHING AT ALL!&quot;</p><p>Redfang roars with scornful laughter.</p>";
        $echo .= "<p>The massive dragon puts his giant feet towards the remaining wobbly pillars and pushes. &quot;You had a chance " . do_shortcode('[ryit_name]') . ". You had potential. The Order, the Clan of the Dragon Heart, even my wretched brother believed in you! But I see your true nature. You are one of us. Yes, me and my sister will....recruit you.</p>";
        $echo .= "<p>Redfang pushes the pillars to the ground and enters the temple through the opening. He looks at you, heat fills your whole body, and then everything goes black.</p>";
    }
    
    return $echo;
}

add_shortcode("jgs_draco_test", "jgs_draco_collection_test");
*/

function jgs_multiple_choice_sequence() {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {  
        global $post;
        $post_id = $post->ID;
        $user_id = get_current_user_id();
        $ajax = false;
        //echo "AJAX FALSE";
    }  
    else {
        $is_ajax = true;
        $post_id = $_GET['post_id'];
        $user_id = $_GET['user_id'];
        //echo "AJAX TRUE";
    }

    //Determine if AJAX
    if(!$is_ajax) {
        $sequence_ID = get_field('jgs_user_data_dragon_progress', 'user_' . $user_id);
        if(empty($sequence_ID)) {
            $sequence_ID = "redfang-start";
        }
    }
    else {
        $sequence_ID = $_GET['sequence_ID'];
    }

    //Determine which dragon is active
    $active_dragon = "";
    $heatmap_type = "";
    if(strpos($sequence_ID,'redfang') !== false) {
        $active_dragon = "redfang";
        $heatmap_type = "fire";
    }
    else if(strpos($sequence_ID,'beira') !== false) {
        $active_dragon = "beira";
        $heatmap_type = "ice";
    }

    $sequence = jgs_init_sequence_contents($post_id, $user_id, $sequence_ID);
    //var_dump($sequence);
    $sequence_content = $sequence['sequence_content'];
    $buttons_html = get_string_between($sequence_content,"<form class='buttons-settings'>","</form>");
    //var_dump($sequence_content);

    //Set up buttons
    $buttons_array = array();
    while(!empty(strpos($buttons_html,"</input>"))) {
        $needle = "</input>";
        $cut_index = strpos($buttons_html, $needle);
        $buttons_array[] = substr($buttons_html,0,$cut_index + strlen($needle));
        $buttons_html = substr($buttons_html, $cut_index + strlen($needle));
    }

    $buttons_html = "";
    $buttons_count = count($buttons_array);
    if($buttons_count > 1) { //Multiple choice response
        foreach($buttons_array as $button) {
            $button_target = get_string_between($button,"data-target='", "'");
            $button_js_callback = get_string_between($button,"data-js-callback='", "'");
            $button_label = get_string_between($button,"data-label='", "'");
            $buttons_html .= "<option data-target='" . $button_target . "' data-js-callback='" . $button_js_callback . "'>&ndash; " . $button_label . "</option>";
        }
        $buttons_html = "<form id='choice-respond'><select><option>Select an option:</option>" . $buttons_html . "</select></form>";
        $buttons_html .= '<div class="sequence-buttons"></div>'; //placeholder
    }
    else { //Single choice
        foreach($buttons_array as $button) {
            $button_target = get_string_between($button,"data-target='", "'");
            $button_js_callback = get_string_between($button,"data-js-callback='", "'");
            $button_label = get_string_between($button,"data-label='", "'");
            $buttons_html .= "<div class='sequence-button'><a class='button-setting' href='#' data-target='" . $button_target . "' data-js-callback='" . $button_js_callback . "'>" . $button_label . "</a></div>";
        }
        $buttons_html = '<div class="sequence-buttons">' . $buttons_html . '</div>';
        $buttons_html .= '<form id="choice-respond"></form>'; //placeholder
    }

    //Retrieve dragon heat map variables

    $ice_level = get_field('jgs_user_data_heat_index_ice', 'user_' . $user_id);
    $ice_level_echo = (isset($ice_level) && $ice_level > 0) ? " level-" . $ice_level : " level-0";
    
    $fire_level = get_field('jgs_user_data_heat_index_fire', 'user_' . $user_id);
    $fire_level_echo = (isset($fire_level) && $fire_level > 0) ? " level-" . $fire_level : " level-0";


    $dragon_loader = '<div id="dragon-loader" style="display: none;"><div class="dragon-pendant"><div class="dragon-pendant-glow"></div><div class="dragon-pendant-object"></div></div></div>';
    $close_btn = '<div id="choice-sequence-close"><span class="close fa fa-times-circle"></span></div>';
    $redfang_is_defeated = get_field("jgs_user_data_redfang_defeated", "user_" . $user_id) == '1' ? true : false;
    $beira_is_defeated = get_field("jgs_user_data_beira_defeated", "user_" . $user_id) == '1' ? true : false;

    $heatmap_redfang = '<div id="heatmap-redfang" class="heatmap' . $fire_level_echo . ' status-' . ($redfang_is_defeated ? "defeated" : "alive") . '"><img src="' . get_site_url() . '/wp-content/uploads/2018/03/jgs-red-dragon-1.jpg" /><div class="heatbar"><span class="marker"></span><span class="fill"></span></div></div>';

    $heatmap_beira = '<div id="heatmap-beira" class="heatmap' . $ice_level_echo . ' status-' . ($beira_is_defeated ? "defeated" : "alive") . '"><img src="' . get_site_url() . '/wp-content/uploads/2018/03/beira-white-dragon.jpg" /><div class="heatbar"><span class="marker"></span><span class="fill"></span></div></div>';
    $heatmap = '<div id="heatmaps" class="' . $active_dragon . '">' . $heatmap_beira . $heatmap_redfang . '</div>';

    if(!$is_ajax) { //will run the first time the shortcode is displayed
        return $dragon_loader . "<div id='choice-sequence-bg' class='hide'></div><div id='choice-sequence' class='hide'>" . $close_btn . "<div class='wrap'><div class='sequence-element' id='" . $sequence_ID . "' active_dragon='" . $active_dragon . "'>" . $sequence_content . "</div></div>" . $buttons_html . $heatmap . "</div></div>";
    }
    else {       
        update_field('jgs_user_data_dragon_progress', $sequence_ID, 'user_' . $user_id);
        $ajax_echo['content'] = "<div class='sequence-element' id='" . $sequence_ID . "' active_dragon='" . $active_dragon . "'>" . $sequence_content . "</div>";
        $ajax_echo['buttons'] = $buttons_html;
        $ajax_echo['dragon'] = $active_dragon;
        //$ajax_echo['ice'] = $heatmap_beira; */
        // . $buttons_html . $heatmap . "</div>";
        wp_send_json_success($ajax_echo);
    }
}

add_shortcode("jgs_multiple_choice_sequence", "jgs_multiple_choice_sequence");

add_action( 'wp_ajax_update_sequence', 'jgs_multiple_choice_sequence' );
add_action( 'wp_ajax_nopriv_update_sequence', 'jgs_multiple_choice_sequence' );


function jgs_init_sequence_contents($post_id, $user_id, $sequence_ID) {
    //retrieve multiple choice sequences
    $sequences = get_field('jgs_choice_sequence', $post_id);

    //extract the correct sequence
    foreach($sequences as $sequence) {
        if($sequence['sequence_ID'] == $sequence_ID)  {
            $sequence_content = $sequence['sequence_content'];

            $advanced = $sequence['sequence_enable_advanced'];
            if(!empty($advanced)) {
                $settings = $sequence['advanced_controls'];
                $php_function_calls = $settings['php_function_call'];
                //var_dump($php_function_calls);
                foreach($php_function_calls as $php_call) {
                    $php_call = $php_call['php_function'];
                    $arg = get_string_between($php_call,"(",")");
                    $func = substr($php_call,0,strpos($php_call,"("));
                    //call_user_func_array($func,array($arg,$user_id));
                }
            }
            break;
        }
    }
    return array('sequence_content' => $sequence_content, 'image_echo' => $image_echo);
}

 function get_string_between($string, $start, $end, $inclusive = false){ 
    $string = " " . $string; 
    $ini = strpos($string,$start); 
    if ($ini == 0) return ""; 
    if (!$inclusive) $ini += strlen($start); 
    $len = strpos($string,$end,$ini) - $ini; 
    if ($inclusive) $len += strlen($end); 
    return substr($string,$ini,$len); 
} 

function jgs_init_dragon() {
    $user_id = get_current_user_id();
    $progress = get_field('jgs_user_data_dragon_progress', 'user_' . $user_id);
    if(!empty($progress)) {
        $label = "Return to the Dragons";
    }
    else {
        $label = "Face the Dragons";
    }

    return "<div class='fusion-button-wrapper fusion-aligncenter'><a href='#' data-callback='init_dragon' class='fusion-button button-3d fusion-button-round button-medium button-default button-1 simple'>" . $label . "</a></div>";
}

add_shortcode("jgs_init_dragon", "jgs_init_dragon");



function jgs_get_user_var($atts, $content = null) {
    $user_id = get_current_user_id();
    $args = shortcode_atts(array(
            'field' => '',
        ),$atts);
        
    $field = $args['field'];

    return get_field($field, 'user_' . $user_id);
}

add_shortcode("jgs_get_user_var", "jgs_get_user_var");


function jgs_dragon_conclusion() {
    $user_id = get_current_user_id();

    $ice_total = get_field('jgs_user_data_heat_index_ice_total', 'user_' . $user_id);
    $fire_total = get_field('jgs_user_data_heat_index_fire_total', 'user_' . $user_id);

    $echo = "<h3>Redfang battle summary</h3>";
    $echo .= "<p>Your total fire index is " . $fire_total . ".</p>";
    if($fire_total < 3) {
        $echo .= "<p>This is a very low score and if you have been honest, you are not in any danger of being possessed by Redfang in the future. However, watch out so you don't become a pushover, for Redfang has his way of taking hold in even the sweetest of people.</p>";
    }
    else if($fire_total >= 3 && $fire_total <= 5) {
        $echo .= "<p>This is a medium score and if you have been honest, you are not in any danger of being possessed by Redfang in the future.</p>";   
    }
    else if($fire_total > 6 && $fire_total <= 9) {
        $echo .= "<p>This is a fairly high score, and you may need to watch out for feelings of specialness, judgment, arrogance and grandiosity. Also watch out for your tendency to repress your vulnerability and sensitivity.</p>";   
    }
    else if($fire_total > 9) {
        $echo .= "<p>This is a high score, and you will likely be prone to judgmental thoughts, feeling special, better than others. You likely also repress your vulnerability and sensitivity a lot. Keep doing the practices of regulating archetypal energy, and you will do fine. Good luck!</p>";   
    }
    $echo .= "<h3>Beira battle summary</h3>";
    $echo .= "<p>Your total ice index is " . $ice_total . ".</p>";

    if($ice_total < 3) {
        $echo .= "<p>Beira's magic does not have much power over you and you are safe (or in denial).</p>";
    }
    else if($ice_total >= 3 && $ice_total <= 5) {
        $echo .= "<p>This is a medium score and if you have been honest, there is no immediate danger of you being possessed by Beira in the future, but you should be mindful not to buy into the stories of your wounds and victimhood too much (by using practices in Marion's Box that build your power and fortitude).</p>";   
    }
    else if($ice_total > 6 && $ice_total <= 9) {
        $echo .= "<p>This is a fairly high score, so watch out for the pull of unconsciousness, of sinking into apathy and sleep, and waiting to be saved by caretakers. Make sure to use practices to build your power and resilience.</p>";   
    }
    else if($ice_total > 9) {
        $echo .= "<p>This is a very high score, and Beira (as the Devouring Mother) really has her teeth in you. You must be careful to do practices that build your vitality, your sense of order and direction, and that you take responsibility for your life. Also: Start forgiving people. Good luck, you will do fine.</p>";   
    }

    return $echo;
}

add_shortcode("jgs_dragon_conclusion", "jgs_dragon_conclusion");


function jgs_dragon_pendant() {
  $user_id = get_current_user_id();

  $redfang_defeated = get_field('jgs_user_data_redfang_defeated', 'user_' . $user_id);
  $beira_defeated = get_field('jgs_user_data_beira_defeated', 'user_' . $user_id);

  $echo = ""; 
  if(!$redfang_defeated || !$beira_defeated)  {
    $echo .= '<div class="dragon-pendant">';
    $echo .= '<div class="dragon-pendant-glow"></div>';
    $echo .= '<div class="dragon-pendant-object"></div>';
    $echo .= '</div>';
    $echo .= '<div class="fusion-title title fusion-sep-none fusion-title-center fusion-title-size-three fusion-border-below-title" style="margin-top:20px;margin-bottom:5px;"><h3 class="title-heading-center"><p style="text-align: center;">Master Cirrus’s dragon pendant glows</p></h3></div>';
    $echo .= '<p style="text-align: center;"><span style="color: #ffffff;"><em>They have arrived...</em></span></p>';
  }
  else {
    $echo .= '<div class="dragon-pendant">';
    $echo .= '<div class="dragon-pendant-object"></div>';
    $echo .= '</div>';
    $echo .= '<div class="fusion-title title fusion-sep-none fusion-title-center fusion-title-size-three fusion-border-below-title" style="margin-top:20px;margin-bottom:5px;"><h3 class="title-heading-center"><p style="text-align: center;">Master Cirrus’s dragon pendant lays dormant</p></h3></div>';
    $echo .= '<p style="text-align: center;"><span style="color: #ffffff;"><em>Redfang and Beira have fled</em></span></p>';
  }

  return $echo;
}

add_shortcode('jgs_dragon_pendant','jgs_dragon_pendant');

/*
function jgs_exit_dragon_sequence() {

}

add_shortcode("jgs_exit_dragon_sequence", "jgs_exit_dragon_sequence");
*/

function jgs_numeric_user_var_compare($atts, $content = null) {
    $user_id = get_current_user_id();
    $args = shortcode_atts( array(
            'val' => 0,
            'field' => '',
            'op' => false,
        ), $atts );
        
    $op = $args['op'];
    $val = $args['val'];
    $val = strpos($val, "|") ? $val : intval($val); //treat values without pipe as integers
    $stored_val = get_field($args['field'], 'user_' . $user_id);

    if(empty($stored_val)) $stored_val = 0;
    
    if(!$op) { //break if no operator is set
        return false;
    }
    else {
        if($op == "lt") {
            if($stored_val < $val) {
                $content = do_shortcode($content);
            }
            else {
                return false;
            }
        }
        elseif($op == "lte") { 
            if($stored_val <= $val) {
                $content = do_shortcode($content);
            }
            else {
                return false;
            }     
        }
        elseif($op == "gt") { 
            if($stored_val > $val) {
                $content = do_shortcode($content);
            }
            else {
                return false;
            }     
        }
        elseif($op == "gte") { 
            if($stored_val >= $val) {
                $content = do_shortcode($content);
            }
            else {
                return false;
            }     
        }
        elseif($op == "eq") { 
            if($stored_val == $val) {
                $content = do_shortcode($content);
            }
            else {
                return false;
            }     
        }
        elseif($op == "ne") { 
            if($stored_val != $val) {
                $content = do_shortcode($content);
            }
            else {
                return false;
            }     
        }
        elseif($op == "between") {
            if(!empty($val)) {
                $values = explode("|", $val);
                if($stored_val >= intval($values[0]) && $stored_val <= intval($values[1])) {
                    $content = do_shortcode($content);
                }
                else {
                    return false;
                } 
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    return $content;
}

add_shortcode('jgs_usr_var_comp','jgs_numeric_user_var_compare');
add_shortcode('jgs_usr_var_innercomp','jgs_numeric_user_var_compare');


//Add Frontend form code
function ryit_frontend_form() {
  global $post;
  if( !is_object($post) ) return;
  if(get_field('ryit_acf_form_head',$post->ID)) {
    acf_form_head();
  }
}

add_action('init','ryit_frontend_form');

function ryit_text_comment($atts) {
    $user_id = get_current_user_id();
    $args = shortcode_atts( array(
            'content' => '',
            'label' => 'Tip'
        ), $atts );

    return "<span class='tip-wrap'><span class='tip-bg'></span><span class='tip'><span class='tip-icon'>" . $args['label'] . "</span></span><span class='content'>" . $args['content'] . "</span></span>";
}

add_shortcode('ryit_text_comment','ryit_text_comment');


function ryit_get_reward_balance() {
    $user_id = get_current_user_id();
    $curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
    return $curr_balance;
}

add_shortcode('jgs_draco_count', 'ryit_get_reward_balance');


/* BBpress */

function ryit_forum_back_link() {
    ?>
    <div class="nav_menu">Return to forum: <a class="bbp-forum-title" href="<?php bbp_forum_permalink();?>"><?php bbp_forum_title(); ?></a></div>
    <?php
}

add_action( 'bbp_template_before_replies_loop', 'ryit_forum_back_link' );

add_filter('bbp_get_do_not_reply_address','scap_bbp_no_reply_email');

function scap_bbp_no_reply_email(){
    $admin_email = get_option('admin_email');
    return $admin_email;
}




/* ************************** */
/*      CHALLENGE SYSTEM      */
/* ************************** */

function jgs_open_challenge_system($atts) {
    $atts = shortcode_atts(
            array(
                'filter' => '',
            ), $atts, 'jgs_open_challenge_system' );

    $filter = $atts['filter'];

    if(empty($filter)) {
        return "<a href='#' class='jgs_challenge_box'><img src='/wp-content/uploads/2018/01/jgs-magic-box-150x100.png' /></a>";    
    }
    else {
        return "<a href='#' class='jgs_challenge_box' filter_name='" . $filter . "'><img src='/wp-content/uploads/2018/01/jgs-magic-box-150x100.png' /></a>";       
    }
    
}

add_shortcode('jgs_marions_box', 'jgs_open_challenge_system');

function ryit_archetype_challenge_post_type() {

     $supports = array('title');

     $labels = array(
          'name' => 'Challenge',
          'singular_name' => 'Challenge',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New Challenge',
          'edit_item' => 'Edit Challenge',
          'new_item' => 'New Challenge',
          'view_item' => 'View Challenge',
          'search_items' => 'Search Challenges',
          'not_found' => 'No challenges found',
          'not_found_in_trash' => 'No challenges found in Trash',
          'parent_item_colon' => '',
          'menu_name' => 'Challenges'
     );


     $args = array(
          'label' => 'Challenges',
          'public' => true,
          'show_ui' => true,
          'hierarchial' => false,
          'menu_icon' => get_stylesheet_directory_uri() . '/images/ryit-crown-icon.png',
          'show_in_menu' => true,
          'menu_position' => 5,
          'rewrite' => array( 'slug' => 'challenge' ),
          'supports' => $supports,
          'labels' => $labels
     );

     register_post_type( 'kwml_challenge' , $args );
}

add_action( 'init', 'ryit_archetype_challenge_post_type' );


function ryit_archetype_challenge_taxonomy() {

    $labels = array(
        'name'              => _x( 'Challenge type', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Challenge type', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search types', 'textdomain' ),
        'all_items'         => __( 'All Challenge Types', 'textdomain' ),
        'edit_item'         => __( 'Edit Type', 'textdomain' ),
        'update_item'       => __( 'Update Type', 'textdomain' ),
        'add_new_item'      => __( 'Add New Type', 'textdomain' ),
        'new_item_name'     => __( 'New Type Name', 'textdomain' ),
        'menu_name'         => __( 'Challenge Type', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'challenge_type' ),
    );

    register_taxonomy( 'kwml_challenge_type', array( 'kwml_challenge' ), $args );
}

add_action( 'init', 'ryit_archetype_challenge_taxonomy' );


function ryit_archetype_challenge_benefit_taxonomy() {

    $labels = array(
        'name'              => _x( 'Challenge Benefit', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Challenge Benefit', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Benefits', 'textdomain' ),
        'all_items'         => __( 'All Benefit Types', 'textdomain' ),
        'edit_item'         => __( 'Edit Benefit', 'textdomain' ),
        'update_item'       => __( 'Update Benefit', 'textdomain' ),
        'add_new_item'      => __( 'Add New Benefit', 'textdomain' ),
        'new_item_name'     => __( 'New Benefit Name', 'textdomain' ),
        'menu_name'         => __( 'Benefit Type', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'benefit' ),
    );

    register_taxonomy( 'kwml_benefit', array( 'kwml_challenge' ), $args );
}

add_action( 'init', 'ryit_archetype_challenge_benefit_taxonomy' );




add_action( 'init', 'get_kwml_terms' );

function get_kwml_terms() {
    $terms = get_terms( 'kwml_challenge_type', array( 'hide_empty' => true));
    //return "<pre>" . print_r($terms) . "</pre>";
    $kwml_terms = array();
    foreach($terms as $term) {
        if($term->parent != 0) {
            $kwml_terms[] = array('slug' => $term->slug, 'parent' => $term->parent);
        }
    }
    return $kwml_terms;
}


function ryit_list_kwml_challenges($user_id, $filter) {

    // Get the categories for post and product post types
    $terms = get_kwml_terms();

    //Get challenge posts filtered on terms
    foreach($terms as $term) {
        $args =  array(
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => 'kwml_challenge',        
            'tax_query' => array(
                array(
                    'taxonomy' => 'kwml_challenge_type',
                    'terms' => $term['slug'],
                    'field' => 'slug',
                )
            )
        );
        $kwml_challenges[$term['slug']] = array('parent' => $term['parent'], 'posts' => get_posts($args));
    }
    $user_id = get_current_user_id();
    $reward_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
    $reward_balance = empty($reward_balance) ? 0 : $reward_balance;

    $output = '<div id="challenges_overlay"><div class="close_wrap"><a class="close"><span class="fa fa-times"></a></div>';
    if($filter == "fight-redfang") {
        $output .= '<span id="reward_balance">Dragon Power: <strong>' . get_field('jgs_user_data_heat_index_fire', 'user_' . $user_id) . '</strong></span>';
    }
    if($filter == "fight-beira") {
        $output .= '<span id="reward_balance">Dragon Power: <strong>' . get_field('jgs_user_data_heat_index_ice', 'user_' . $user_id) . '</strong></span>';
    }
    $output .= '<div id="challenges"' . (empty($filter) ? '' : ' class="' . $filter . '"') . '><div class="scrollwrap">';

    //Loop through challenge objects
    foreach($kwml_challenges as $challenge_type => $challenge) {
       
        $challenge_parent = $challenge['parent']; //assign archetype
        $challenge_parent = get_term_by( 'id', $challenge_parent, 'kwml_challenge_type' );

        //return $challenge_parent->slug;

        //$challenge_parent_slug = $challenge_parent['slug'];

        if($challenge_parent->slug == "magician") {
            $icon = "ra-crystal-wand";
        }
        else if($challenge_parent->slug == "warrior") {
            $icon = "ra-sword";
        }
        else if($challenge_parent->slug == "sovereign") {
            $icon = "ra-crown";
        }
        else if($challenge_parent->slug == "lover") {
            $icon = "ra-hearts";
        }

        $complete_label = "Yes, I did it";

        switch ($filter) {
            case "fight-redfang":
                $complete_label = "Strike Redfang";
                break;
            case "fight-beira":
                $complete_label = "Strike Beira";
                break;
        }

        $cat_echo = "";

        if(!empty($filter)) {
            $tag_filter_match = false;
            $tag_filter_matches = 0;
        }
        foreach($challenge['posts'] as $post) {
            $tags = wp_get_post_terms($post->ID, 'kwml_benefit');
            if($tags) {
                $tags_echo = '<ul class="tags">';
                $tags_classes = "";
                foreach($tags as $tag) {
                    if(!empty($filter) && $tag->slug == $filter) {
                        $tag_filter_match = true;
                        $tag_filter_matches++; 
                    }
                    if(strpos($tag->name,'fight-') !== false) {
                        continue; //challenge has filter related to dragon encounter. skip this tag from listing
                    }
                    $tags_echo .= "<li class='" . $tag->slug . "' tag_name='" . $tag->name . "'>" . $tag->name . "</li>";
                    $tags_classes .= " " . $tag->slug;
                }
                $tags_echo .= "</ul>";
            }
            else {
                $tags_echo = "";
            }
            if(!empty($filter) && !$tag_filter_match) {
                continue;
                $tag_filter_match = false;
            }
            else {
                $tag_filter_match = false;
            }

            $intensity = get_field('kwml_challenge_enable_intensity', $post->ID);
            $intensity_values = get_field('kwml_challenge_intensity_values', $post->ID);
            $intensity_text = ($intensity) ? $intensity_values['intensity_medium'] : "";
            $description = get_field('kwml_challenge_description', $post->ID);

            if($intensity) {
                $description = str_replace('[intensity_text]', '<strong>' . $intensity_text . '</strong>', $description);
            }

            $cat_echo .= '<li class="challenge' . $tags_classes . '" value="' . get_field('kwml_challenge_reward',$post->ID) . '">';
            $cat_echo .= '<div class="header"><h2>' . get_the_title($post->ID) . '</h2><span class="reward">' . get_field('kwml_challenge_reward',$post->ID) . '</span><span class="toggle">Click for Info</span><span class="archetype ra ' . $icon . '"></span></div>';
            $cat_echo .= '<div class="info">' . $description . '</div>';
            $cat_echo .= '<div class="meta">' . $tags_echo . '<a href="#" id="kwml_complete_challenge">' . $complete_label . '</a></div>';
            $cat_echo .= '</li>';
        }

        if($prev_challenge_type != $challenge_type) {
            $term = get_term_by('slug',$challenge_type,'kwml_challenge_type', 'ARRAY_A');  
            $challenge_type_name = $term['name'];   
            if(empty($filter)) {
                $output .= '<h3>' . $challenge_type_name . '</h3><ul class="challenge_cat">' . $cat_echo;
            }
            else {
                if($tag_filter_matches > 0) {
                    $output .= '<h3>' . $challenge_type_name . '</h3><ul class="challenge_cat">' . $cat_echo;
                }
            }
            $cat_echo = "";
            $output .= '</ul>'; // end challenge cat
        }

        $prev_challenge_type = $challenge_type;
    }

    $output .= '</div></div></div>';
    return $output;
}

add_action( 'wp_ajax_update_reward_balance', 'ryit_update_reward_balance_ajax' );
add_action( 'wp_ajax_nopriv_update_reward_balance', 'ryit_update_reward_balance_ajax' );

function ryit_update_reward_balance_ajax() {
    if(!empty($_GET['user_id'])) {
        $user_id = $_GET['user_id'];
    }
    if(!empty($_GET['value'])) {
        $challenge_value = $_GET['value'];
    }
    $curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
    $curr_balance = empty($curr_balance) ? 0 : $curr_balance;
    $new_balance = intval($curr_balance) + intval($challenge_value);
    if($new_balance < 0) $new_balance = 0;
    update_field('ryit_user_currency_balance', $new_balance, 'user_' . $user_id);
    echo $new_balance;
    die();
}


function ryit_update_reward_balance($balance,$user_id=false) {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        //Not AJAX
        $user_id = get_current_user_id();
    }

    if(substr($balance,0,1) == "+") {
        $val_increment = intval(substr($balance,1));
        $curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
        update_field('ryit_user_currency_balance', $curr_balance + $val_increment, 'user_' . $user_id);
    }
    elseif(substr($balance,0,1) == "-") {
        $val_increment = intval(substr($balance,1));
        $curr_balance = get_field('ryit_user_currency_balance', 'user_' . $user_id);
        if(($curr_balance - $val_increment) < 0 ) {
            update_field('ryit_user_currency_balance', 0, 'user_' . $user_id);  
        }
        else {
            update_field('ryit_user_currency_balance', $new_balance - $val_increment, 'user_' . $user_id);        
        }
    }
    else {
        update_field('ryit_user_currency_balance', $balance, 'user_' . $user_id);        
    }
}

/**
 * Extend get terms with post type parameter.
 *
 * @global $wpdb
 * @param string $clauses
 * @param string $taxonomy
 * @param array $args
 * @return string
 */
function df_terms_clauses( $clauses, $taxonomy, $args ) {
    if ( isset( $args['post_type'] ) && ! empty( $args['post_type'] ) && $args['fields'] !== 'count' ) {
        global $wpdb;

        $post_types = array();

        if ( is_array( $args['post_type'] ) ) {
            foreach ( $args['post_type'] as $cpt ) {
                $post_types[] = "'" . $cpt . "'";
            }
        } else {
            $post_types[] = "'" . $args['post_type'] . "'";
        }

        if ( ! empty( $post_types ) ) {
            $clauses['fields'] = 'DISTINCT ' . str_replace( 'tt.*', 'tt.term_taxonomy_id, tt.taxonomy, tt.description, tt.parent', $clauses['fields'] ) . ', COUNT(p.post_type) AS count';
            $clauses['join'] .= ' LEFT JOIN ' . $wpdb->term_relationships . ' AS r ON r.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN ' . $wpdb->posts . ' AS p ON p.ID = r.object_id';
            $clauses['where'] .= ' AND (p.post_type IN (' . implode( ',', $post_types ) . ') OR p.post_type IS NULL)';
            $clauses['orderby'] = 'GROUP BY t.term_id ' . $clauses['orderby'];
        }
    }
    return $clauses;
}


add_filter( 'terms_clauses', 'df_terms_clauses', 10, 3 );




//Ajax update of Fragments form
add_action( 'wp_ajax_display_coaching_system', 'jgs_display_coaching_system' );
add_action( 'wp_ajax_nopriv_display_coaching_system', 'jgs_display_coaching_system' );

function jgs_display_coaching_system() {
    $filter = $_GET['filter'];
    $user_id = get_current_user_id();
    $output = ryit_list_kwml_challenges($user_id, $filter);
    if($output) {
        $ajax_output = array();
        $ajax_output[] = $output;
        echo json_encode($ajax_output);
    }
    else {
        echo "No output";
    }
    die();
}


?>