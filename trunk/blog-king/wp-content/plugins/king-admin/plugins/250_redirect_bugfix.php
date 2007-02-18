<?php
/*
Plugin Name:250 redirect bugfix
Version: 0.7
Plugin URI:

*/
# http://txfx.net/code/wordpress/wordpress-tuneup/
if ( '2.0.5' == get_bloginfo('version') ) {

if ( !function_exists('wp_redirect') ) :
function wp_redirect($location, $status = 302) {
    global $is_IIS;

    $location = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%]|i', '', $location);

    $strip = array('%0d', '%0a');
    $location = str_replace($strip, '', $location);

    if ( $is_IIS )
        header("Refresh: 0;url=$location");
    else
        header("Location: $location");
}
endif;

}

?>