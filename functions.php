<?php
/**
 * UnderStrap functions and definitions
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/jetpack.php',                         // Load Jetpack compatibility file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567
	'/woocommerce.php',                     // Load WooCommerce functions.
	'/editor.php',                          // Load Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
);

foreach ( $understrap_includes as $file ) {
	require_once get_template_directory() . '/inc' . $file;
}


// Handle our custom rewrite rule for Featured Companies page
// flush_rewrite_rules();
function custom_rewrite_rule() {
    add_rewrite_rule('^featured-companies/?([^/]+)/?$','index.php?page_id=49&symbol=$matches[1]','top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);

add_filter('query_vars', function($vars) {
    $vars[] = "symbol";
    return $vars;
});


//Generate 404
function generate_404() {
	// 1. Ensure `is_*` functions work
	 global $wp_query;
	 $wp_query->set_404();

	 // 2. Fix HTML title
	 add_action( 'wp_title', function () {
			 return '404: Not Found';
	 }, 9999 );

	 // 3. Throw 404
	 status_header( 404 );
	 nocache_headers();

	 // 4. Show 404 template
	 require get_404_template();

	 // 5. Stop execution
	 exit;
}

function currencyFormat($num) {
    if( $num > 1000 ) {
    		$x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('K', 'M', 'B', 'T', 'Q');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
				$x_afterDec = substr(round($x_array[1][0] . $x_array[1][1] . $x_array[1][2], -1), 0, 2);
        $x_display = $x_array[0] . '.' . $x_afterDec ;
        $x_display .= $x_parts[$x_count_parts - 1];
        return $x_display;
    }
    return $num;
}

function wp_register_widgets() {
	register_sidebar( array(
		'name' => __( 'Stock Recommendations on Featured Company Page', 'wp' ),
		'id' => 'widget-stock-recommendations',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle  mt-3">',
		'after_title' => '</h3>'
	));

	register_sidebar( array(
		'name' => __( 'Related Articles on Featured Company Page', 'wp' ),
		'id' => 'widget-related-articles',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle mt-3">',
		'after_title' => '</h3>'
	));

}
add_action( 'widgets_init', 'wp_register_widgets' );
