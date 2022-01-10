<?php

    @ini_set( 'upload_max_size' , '1064M' );
    @ini_set( 'post_max_size', '1064M');
    @ini_set( 'max_execution_time', '30000' );

    global $enable_ppd;
    $enable_ppd = true;

    global $theme_dir;
    $theme_dir = get_stylesheet_directory();

    // include('includes/sf_crm.php');


    $general_email_signature =  "<span style='font-style:normal;font-variant-ligatures:normal;font-variant-caps:normal;font-weight:400;letter-spacing:normal;text-align:start;text-indent:0px;text-transform:none;white-space:normal;word-spacing:0px;text-decoration-style:initial;text-decoration-color:initial;font-size:7.5pt;font-family:Arial,sans-serif;color:rgb(102,102,102)'><br/>
                                    <a href='https://www.google.lt/maps/place/138+South+St,+Romford+RM1+1TE,+UK/@51.5743984,0.1818805,17z/data=!3m1!4b1!4m5!3m4!1s0x47d8a4cbc6766b97:0x93896e0b0c73f082!8m2!3d51.5743951!4d0.1840692' style='color:rgb(17,85,204)' target='_blank' >138 South Street, 5th floor,<span> </span></a><br/>
                                    <a href='https://www.google.lt/maps/place/138+South+St,+Romford+RM1+1TE,+UK/@51.5743984,0.1818805,17z/data=!3m1!4b1!4m5!3m4!1s0x47d8a4cbc6766b97:0x93896e0b0c73f082!8m2!3d51.5743951!4d0.1840692'>Romford, RM1 1TE,<br/>United Kingdom</a><br/>
                                    Telephone: <a href='tel:+442037347558' '+44 2037 347558'>+44 2037 347558</a><span> </span><br/>
                                    E-mail: <a href='mailto:info@pigeon.com' style='color:rgb(17,85,204)' target='_blank'>info@pigeon.com</a><br/>
                                    Website: <a href='http://www.pigeon.com/' style='color:rgb(17,85,204)' target='_blank' data-saferedirecturl='https://www.google.com/url?hl=en&amp;q=http://www.pigeon.com/&amp;source=gmail&amp;ust=1527665646655000&amp;usg=AFQjCNEZzyy3d7hI0zv1Pb-pjLpb3pzhoA'>www.pigeon.com</a>
                                </span>";


    // TURN OFF CRM FUNCTIONS IF IN DEVELOPMENT MODE

    global $is_local_host;
    $disable_crm = 'false';




// --------------------------------- THEME SETUP FUNCTIONS START-------------------------------

    add_action( 'after_setup_theme', 'pigeon_setup' );
    function pigeon_setup() {
        load_theme_textdomain( 'pigeon' );
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 282, 282, array( 'center', 'top' ) ); // default Post Thumbnail dimensions
        register_nav_menus(
        array(
          'top'             => __( 'Top Menu' ),
          'small'           => __( 'Small Menu' ),
        )
      );

    }

    // Include fav icon in general and admin pages
    add_action('login_head', 'add_favicon');
    add_action('admin_head', 'add_favicon');
    function add_favicon() {
        $favicon_url = get_template_directory_uri() . '/favicon.ico';
        echo '<link rel="icon" href="'.$favicon_url.'" type="image/x-icon">';
    }

    // *.js and *.css files ar listed in assets.php for correct gulp bumping
    include('assets.php');

    // Remove visual composer from front page
    add_action( 'wp_enqueue_scripts', 'remove_vc_from_front_page', 20 );
    function remove_vc_from_front_page() {
        if ( is_front_page() ){
            wp_deregister_style('js_composer_front');
        }
    }

    // Remove admin bars margin
    function remove_admin_bar_margin() {
        remove_action('wp_head', '_admin_bar_bump_cb', '433454bdbff50b');
    }


    //remove a metatag (Powered by Visual Composer) from the wordpress
    add_action('init', 'remove_vc_meta_tag', 100);
    function remove_vc_meta_tag() {
    //    remove_action('wp_head', array(visual_composer(), 'addMetaData'));
    }

    // Remove emoji support
    // remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    // remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    // remove_action( 'wp_print_styles', 'print_emoji_styles' );
    // remove_action( 'admin_print_styles', 'print_emoji_styles' );

    // Remove type=javascript/css from script and style tags
    add_filter('style_loader_tag', 'myplugin_remove_type_attr', 10, 2);
    // add_filter('script_loader_tag', 'myplugin_remove_type_attr', 10, 2);



    // Remove "type" attribute from javascript files in html
    function myplugin_remove_type_attr($tag, $handle) {
        return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
    }

    // Function to get assets with file_get_content
    function get_stylesheet_assets() {
        $stylesheet_url = get_stylesheet_directory_uri();
        if (substr( $stylesheet_url, 0, 4 ) === "http") {
            return $stylesheet_url;
        } else {
            $stylesheet_url = site_url().$stylesheet_url;
            return $stylesheet_url;
        }
    }

// -------------------------------------------------------GENERAL FUNCTIONS-------------------------------------------------------------
// =====================================================================================================================================
// =====================================================================================================================================
// =====================================================================================================================================

// ----------------------------LOGGING FUNCTION START-----------------------
    function logging($type = 'unknown', $data = 'Unknown event has occured that triggered logging'){
        $keep_logs = get_field('keep_logs','options');

        if (!$keep_logs){
            return;
        }

        $year = date("Y");
        $month = date('m');
        $time_stamp = date('Y-m-d H:i:s', strtotime('2 hour'));

        $data = $time_stamp . " ------------------------------------------" . "\n" . $data . "\n\n";


        $dir = ABSPATH . '/logs/'. $type . '/' . $year . '/';
        $file = $dir . $year . '-' . $month .'-'.$type.'.log';

        if ( !file_exists( $dir ) ) {
                mkdir($dir, 0777, true);
        }

        if ( !file_exists( $file) ){
            $fp = fopen($file ,"wb");
            fwrite($fp,$data);
            fclose($fp);
        }else{
            $content = $data . "\n";
            $content .= file_get_contents($file);
            file_put_contents($file, $content);
        }

    }

    // new logg function
    function logg($type = 'unknown', $data = 'Unknown event has occured that triggered logging'){

        date_default_timezone_set('Europe/Vilnius');

        $year = date("Y");
        $month = date('m');
        $time_stamp = date('Y-m-d H:i:s', strtotime('2 hour'));
        $output = $time_stamp . " - " . $type . " ------------------------------------------" . "\n";

        if (is_array($data) ){
            foreach ($data as $key => $value) {

                if (is_array($value) ){
                    $value  = json_encode($value);
                }else{
                    $value = str_replace("<br/>","\n", $value);
                }

                $output .= $key . ": " . $value . "\n";
            }
        }else if (is_object($data)) {
            $output = json_encode($data);
        }else{
            $output .= $data . "\n";
        }

        $output .= "------------------------------------------" . "\n\n";

        $dir = ABSPATH . '/logs/'. $type . '/' . $year . '/';
        $file = $dir . $year . '-' . $month .'-'.$type.'.log';

        if ( !file_exists( $dir ) ) {
                mkdir($dir, 0777, true);
        }

        if ( !file_exists( $file) ){
            $fp = fopen($file ,"wb");
            fwrite($fp,$output);
            fclose($fp);
        }else{
            $content = $output . "\n";
            $content .= file_get_contents($file);
            file_put_contents($file, $content);
        }

    }
// ----------------------------LOGGING FUNCTION END----------------------

// ----------------------------SITEMAP GENERATOR-----------------------------


// ----------------------------HIDING INFOGRAPHIC ARCHIVE CATEGORY FROM ADMIN START----------------
    function remove_category($args, $taxonomies) {
        if (implode('', $taxonomies) == 'category') {
            //Infographic archive ID = 8
            $args['exclude'] = '8';
        }
        return $args;
    }

    if (is_admin()) {
        add_filter('get_terms_args', 'remove_category', 10, 2);
    }
// ----------------------------HIDING INFOGRAPHIC ARCHIVE CATEGORY FROM ADMIN END----------------


// ----------------------------HIDING SYSTEM PAGES FROM ADMIN START--------------------------
    add_action( 'pre_get_posts' ,'exclude_this_page' );
    function exclude_this_page( $query ) {
        if( !is_admin() )
            return $query;

        global $pagenow;

        if( 'edit.php' == $pagenow && ( get_query_var('post_type') && 'page' == get_query_var('post_type') ) )
            $query->set( 'post__not_in', array(341,336,338,288,332,2649) );

        return $query;
    }
// ----------------------------HIDING SYSTEM PAGES FROM ADMIN END--------------------------



// ----------------------------LINK CORRECT FORMAT START---------------------------------
    function check_link($urlStr){
        if ($urlStr != ''){
            $parsed = parse_url($urlStr);
            if (empty($parsed['scheme'])) {
                $urlStr = 'http://' . ltrim($urlStr, '/');
            }
        }

        return $urlStr;
    }
// ----------------------------LINK CORRECT FORMAT END---------------------------------


// ----------------------------SANITIZE FILE NAME START---------------------------------
    add_filter( 'sanitize_file_name', 'wp_sanitize_file_name', 10, 1 );
    function wp_sanitize_file_name( $filename ) {

        $sanitized_filename = remove_accents( $filename ); // Convert to ASCII

        // Standard replacements
        $invalid = array(' ' => '-','%20' => '-','_' => '-', 'ą' => 'a', 'č' => 'c', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', '�' => 's', 'ų' => 'u', 'ū' => 'u', 'ž' => 'z', 'Ą' => 'A', 'Č' => 'C', 'Ę' => 'E', 'Ė' => 'E', 'Į' => 'I', 'Š' => 'S', 'Ų' => 'U', 'Ū' => 'U', 'Ž' => 'Z');

        $sanitized_filename = str_replace( array_keys( $invalid ), array_values( $invalid ), $sanitized_filename );

        $sanitized_filename = preg_replace('/[^A-Za-z0-9-\. ]/', '', $sanitized_filename); // Remove all non-alphanumeric except .
        $sanitized_filename = preg_replace('/\.(?=.*\.)/', '', $sanitized_filename); // Remove all but last .
        $sanitized_filename = preg_replace('/-+/', '-', $sanitized_filename); // Replace any more than one - in a row
        $sanitized_filename = str_replace('-.', '.', $sanitized_filename); // Remove last - if at the end
        $sanitized_filename = strtolower( $sanitized_filename ); // Lowercase

        return $sanitized_filename;
    }

    function sanitize_post_data($_arr){

        unset($_arr['action']);

        foreach ($_arr as $key => $value) {
            $_arr[$key] = sanitize_text_field($value);
        }
        return $_arr;
    }
// ----------------------------SANITIZE FILE NAME END---------------------------------


function send_email($email, $subject, $message) {

    // Turn email list to array
    $email = explode(',', $email);


	// If no $headers sent
	// Add From: header
    $headers =  "From: info@pigeon.com" . "\r\n" .
                "Reply-To: info@pigeon.com" . "\r\n";

    // $headers .=  "MIME-Version: 1.0\r\n";
    $headers .=  "Content-type: text/html; charset=UTF-8\r\n";

	// Send Email
	if ( is_array($email) ) {
		foreach ($email as $e) {
			wp_mail($e, $subject, $message, $headers);
		}
	} else {
		wp_mail($email, $subject, $message, $headers);
	}
}

function send_email_multipart($email, $subject, $message) {

    // Turn email list to array
    $email = explode(',', $email);

    // Unique boundary
	$boundary = md5( uniqid() . microtime() );

    // Headers
    $headers = "MIME-Version: 1.1\r\n";
    $headers .= "From: info@pigeon.com" . "\r\n";
    $headers .= "Reply-To: info@pigeon.com" . "\r\n";
    $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
    // $headers .= "Content-Transfer-Encoding: 7bit\r\n";
    // $headers .= "Content-Transfer-Encoding: base64";

    // Plain text body
    $body = "\r\n\r\n--" . $boundary . "\r\n";
    $body .= "Content-type: text/plain;charset=ISO-8859-1\r\n";
    // $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $plane_message = str_replace("<br/>","\r\n",$message);
    $body .=  "strip_tags($plane_message)";

    $body .= "\r\n\r\n--" . $boundary . "\r\n";

    // Html body
    $body .= "Content-type: text/html;charset=ISO-8859-1\r\n";
    // $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $html_message = "<html><body>" . $message . "</body></html>";
    $body .=  $message;
    $body .= "\r\n\r\n--" . $boundary . "--";


	// Send Email

	if ( is_array($email) ) {
		foreach ($email as $e) {
			wp_mail($e, $subject, $body, $headers);
		}
	} else {
		wp_mail($email, $subject, $body, $headers);
	}


}
//
// add_filter( 'wp_mail_content_type', 'set_content_type' );
// function set_content_type( $content_type ) {
// 	return "multipart/alternativez";
// }

// add_filter( 'wp_mail_content_type', 'set_content_type' );
// function set_content_type( $content_type ) {
//     return 'multipart/alternative';
// }


function send_email_with_encoding($email, $subject, $message) {

    // Turn email list to array
    $email = explode(',', $email);

    // Unique boundary
	$boundary = md5( uniqid() . microtime() );

    // Headers
    $headers = "MIME-Version: 1.1\r\n";
    $headers .= "From: info@pigeon.com" . "\r\n";
    $headers .= "Reply-To: info@pigeon.com" . "\r\n";
    $headers .= "Content-Type: multipart/alternative;boundary=" . $boundary . "\r\n";
    // $headers .= "Content-Transfer-Encoding: 7bit\r\n";
    $headers .= "Content-Transfer-Encoding: base64";

    // Plain text body
    $body = "\r\n\r\n--" . $boundary . "\r\n";
    $body .= "Content-type: text/plain;charset=ISO-8859-1\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $plane_message = str_replace("<br/>","\r\n",$message);
    $body .= chunk_split( base64_encode( strip_tags($plane_message) ) );
    $body .= "\r\n\r\n--" . $boundary . "\r\n";

    // Html body
    $body .= "Content-type: text/html;charset=ISO-8859-1\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $html_message = "<html><body>" . $message . "</body></html>";
    $body .= chunk_split(base64_encode( $message ) );
    $body .= "\r\n\r\n--" . $boundary . "--";


	// Send Email

	if ( is_array($email) ) {
		foreach ($email as $e) {
			wp_mail($e, $subject, $body, $headers);
		}
	} else {
		wp_mail($email, $subject, $body, $headers);
	}


}

/**
 * Disable embeds
 */
function crave_disable_embeds() {

	// Remove the REST API endpoint.
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );

	// Turn off oEmbed auto discovery.
	add_filter( 'embed_oembed_discover', '__return_false' );

	// Don't filter oEmbed results.
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

	// Remove oEmbed discovery links.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	add_filter( 'tiny_mce_plugins', 'crave_disable_embeds_tiny_mce_plugin' );

	// Remove all embeds rewrite rules.
	add_filter( 'rewrite_rules_array', 'crave_disable_embeds_rewrites' );

	// Remove filter of the oEmbed result before any HTTP requests are made.
	remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
}
add_action( 'init', 'crave_disable_embeds', 9999 );

function crave_disable_embeds_tiny_mce_plugin($plugins) {
	return array_diff($plugins, array('wpembed'));
}

function crave_disable_embeds_rewrites($rules) {
	foreach($rules as $rule => $rewrite) {
		if(false !== strpos($rewrite, 'embed=true')) {
			unset($rules[$rule]);
		}
	}
	return $rules;
}




/**
* Disable the emoji's
*/
function crave_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'crave_disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'crave_disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'crave_disable_emojis' );

/**
* Filter function used to remove the tinymce emoji plugin.
*
* @param array $plugins
* @return array Difference betwen the two arrays
*/
function crave_disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

/**
* Remove emoji CDN hostname from DNS prefetching hints.

*/
function crave_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' == $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_diff( $urls, array( $emoji_svg_url ) );
	}
	return $urls;
}



/**
* Remove junk from head
*/
// remove WordPress version number
function crave_remove_version() {
	return '';
}
add_filter('the_generator', 'crave_remove_version');
remove_action('wp_head', 'wp_generator');

remove_action('wp_head', 'rsd_link'); // remove really simple discovery (RSD) link
remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)

remove_action('wp_head', 'feed_links', 2); // remove rss feed links (if you don't use rss)
remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links

remove_action('wp_head', 'index_rel_link'); // remove link to index page

remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 ); // remove shortlink

function wps_deregister_styles() {
    wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_print_styles', 'wps_deregister_styles', 100 );


// replacing request form assets with span, to load them later in home page


// // Our custom error handler
function custom_error_handling($number, $message, $file, $line, $vars) {

    date_default_timezone_set('Europe/Vilnius');

    $year = date("Y");
    $month = date('m');
    $time_stamp = date('Y-m-d H:i:s');

    $error_type = FriendlyErrorType($number);

    // Error information
    $email = "
        <p>Error time: $time_stamp (Timezone Europe/Vilnius)</p>
        <p>An error ($error_type) occurred on line
        <strong>$line</strong> and in the <strong>file: $file.</strong></p>
        <p> $message </p>";

    // detailed array of error report.
    $email .= "<pre>" . print_r($vars, 1) . "</pre>";

    $error_message = $error_type . ": " . $message . " in " . $file . " on line " . $line;

    //check last error and do not send email for the same error in the rows
    $debug_log_file = ABSPATH . "wp-content/debug.log";
    $data = file($debug_log_file);
    $text = $data[count($data)-1];
    $text = substr($text, strripos($text,']') + 1 );
    $text = (string)$text;

    // Ceck string similatiry
    $sim = similar_text($text, $error_message,  $perc);

    // Write error to log file
    error_log($error_message);

    // Send email only if the string aren't similar
    global $is_local_host;

    if ($perc < 90 && !$is_local_host){

        // Ignore wp-super-cache errors until plugin fix
        // Tgnore chunk size already defined errors
        if ( strpos($email, 'wp-super-cache\wp-cache-phase2.php') == false && strpos($email, 'Constant CHUNK_SIZE already defined') == false ) {
            // $email = '';
            // send_email($email, 'pigeon - error notification', $email);
        }

    }


    // Email the error to admin

    // Make sure that you decide how to respond to errors (on the user's side)
    // Either echo an error message, or kill the entire project. Up to you...
    // The code below ensures that we only "die" if the error was more than
    // just a NOTICE.
    // if ( ($number !== E_NOTICE) && ($number < 2048) ) {
    //     die("There was an error. Please try again later.");
    // }
}

// set_error_handler('custom_error_handling');

function FriendlyErrorType($type) {
    switch($type){
        case E_ERROR: // 1 //
            return 'E_ERROR';
        case E_WARNING: // 2 //
            return 'E_WARNING';
        case E_PARSE: // 4 //
            return 'E_PARSE';
        case E_NOTICE: // 8 //
            return 'E_NOTICE';
        case E_CORE_ERROR: // 16 //
            return 'E_CORE_ERROR';
        case E_CORE_WARNING: // 32 //
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR: // 64 //
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING: // 128 //
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR: // 256 //
            return 'E_USER_ERROR';
        case E_USER_WARNING: // 512 //
            return 'E_USER_WARNING';
        case E_USER_NOTICE: // 1024 //
            return 'E_USER_NOTICE';
        case E_STRICT: // 2048 //
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR: // 4096 //
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED: // 8192 //
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED: // 16384 //
            return 'E_USER_DEPRECATED';
    }
    return "";
}

// OPTIMIZATION
// ==================================================================================================================================================================
// ==================================================================================================================================================================
// ==================================================================================================================================================================
// ==================================================================================================================================================================
// ==================================================================================================================================================================


// ASYNC CSS
add_filter( 'style_loader_tag',  'styles_loading_later', 10, 4 );
function styles_loading_later( $html, $handle, $href, $media ){
    $_async_styles = array(
        'normalize',
        'rest-styles',
        'js_composer_front',
        'sumoselect-original-styles',
        'slick-style',
        'trial-sumo-select-style'
        // 'country-select-styles',
        // 'tel-input-css',
        // 'sumoselect-styles',
    );

    $_load_later_styles = array(
        'country-select-styles',
        'tel-input-css',
        'sumoselect-styles',
        // 'rest-styles',
    );

    // remove VC font-awesome
    if ($handle == "font-awesome"){
        $html = "";
    }

    if ( in_array($handle, $_async_styles) ){
        echo '<link rel="stylesheet" id="'.$handle.'" href="'.$href.'" ><noscript><link rel="stylesheet" id="'.$handle.'" href="'.$href.'"></noscript>';
    } else if (  in_array($handle, $_load_later_styles) ){
        echo '<span id="'.$handle.'" class="load-later" data-type="style" data-src="'.$href.'" style="display:none;"></span>';
    } else {
        return $html;
    }

}
//


add_action('admin_head', 'my_custom_fonts');
add_action('wp_head', 'my_custom_fonts');

function my_custom_fonts()
{
    echo '<style>

  .qtranxs-notice-ajax, #wpfooter, #dashboard_activity, #wpseo-dashboard-overview,
  #wp-admin-bar-wp-logo, #wp-admin-bar-updates, #wp-admin-bar-comments,
  #wp-admin-bar-wpseo-menu,
  #contextual-help-link-wrap{
    display: none!important;
  }
  .wp-list-table #wpseo-score,
  .wp-list-table #wpseo-score-readability,
  .wp-list-table #wpseo-metadesc,
  .wp-list-table #wpseo-focuskw,
  .wp-list-table #wpseo-links,
  .wp-list-table #wpseo-linked,
  .wp-list-table .wpseo-score.column-wpseo-score,
  .wp-list-table .wpseo-score-readability.column-wpseo-score-readability,
  .wp-list-table .wpseo-metadesc.column-wpseo-metadesc,
  .wp-list-table .wpseo-links.column-wpseo-links,
  .wp-list-table .wpseo-linked.column-wpseo-linked,
  .column-wpseo-score, .column-wpseo-score-readability, .column-wpseo-links, .column-wpseo-linked,
  #wpseo-filter, #wpseo-readability-filter, .column-wpseo-linked, #misc-publishing-actions #content-score, #misc-publishing-actions #keyword-score{
    display: none!important;
  }

  </style>';
}