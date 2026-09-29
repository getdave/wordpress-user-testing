<?php
/**
 * Seeds the Canvas block user-testing site.
 *
 * Runs once from the blueprint after Canvas and Twenty Twenty-Five are installed.
 */

require '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// Run as the admin so kses doesn't strip block markup.
wp_set_current_user( 1 );

$content_dir = '/wordpress/wp-content/user-testing/content';

// Site identity and pretty permalinks.
update_option( 'blogname', 'Bluebell & Bloom' );
update_option( 'blogdescription', 'Seasonal flowers from a small studio' );
global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
$wp_rewrite->flush_rules();

// Apply the Twenty Twenty-Five "Morning" style variation.
$variation = wp_json_file_decode( get_theme_file_path( 'styles/06-morning.json' ), array( 'associative' => true ) );
$user_cpt  = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );
wp_update_post(
	array(
		'ID'           => $user_cpt['ID'],
		'post_content' => wp_slash(
			wp_json_encode(
				array(
					'version'                     => $variation['version'],
					'isGlobalStylesUserThemeJSON' => true,
					'settings'                    => $variation['settings'] ?? array(),
					'styles'                      => $variation['styles'] ?? array(),
				)
			)
		),
	)
);

// Remove the default post and Sample Page so the page list stays clean.
wp_delete_post( 1, true );
wp_delete_post( 2, true );

// Import Canvas's bundled photos into the Media Library.
$images = array(
	'bluebells' => array( 'image-1.jpg', 'Bluebells', 'Blue harebell flowers on slender stems' ),
	'meadow'    => array( 'image-2.jpg', 'Meadow', 'Yellow wildflowers in a meadow below rocky mountains' ),
	'hydrangea' => array( 'image-3.jpg', 'Hydrangea', 'A hand holding a hydrangea, silhouetted against a sunlit window' ),
);

$tokens = array();
foreach ( $images as $slug => list( $file, $title, $alt ) ) {
	$tmp = wp_tempnam( $file );
	copy( WP_PLUGIN_DIR . '/canvas/images/' . $file, $tmp );
	$id = media_handle_sideload( array( 'name' => $slug . '.jpg', 'tmp_name' => $tmp ), 0, $title );
	if ( is_wp_error( $id ) ) {
		throw new Exception( 'Could not import ' . $file . ': ' . $id->get_error_message() );
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );

	$key                       = strtoupper( $slug );
	$tokens[ "%%{$key}_ID%%" ]  = $id;
	$tokens[ "%%{$key}_URL%%" ] = wp_get_attachment_image_url( $id, 'large' );
	$tokens[ "%%{$key}_ALT%%" ] = esc_attr( $alt );
}

// Pages. Fixed IDs so the blueprint's landingPage can open Home in the editor.
$pages = array(
	array( 100, 'Home', 'home.html', 'page-no-title' ),
	array( 101, 'About', 'about.html', '' ),
	array( 102, 'Workshops', 'workshops.html', '' ),
	array( 103, 'Contact', 'contact.html', '' ),
);

foreach ( $pages as $order => list( $id, $title, $file, $template ) ) {
	$result = wp_insert_post(
		array(
			'import_id'     => $id,
			'post_type'     => 'page',
			'post_status'   => 'publish',
			'post_title'    => $title,
			'menu_order'    => $order,
			'page_template' => $template,
			'post_content'  => wp_slash( strtr( file_get_contents( "$content_dir/$file" ), $tokens ) ),
		),
		true
	);
	if ( is_wp_error( $result ) || $id !== $result ) {
		throw new Exception( "Could not create the $title page with ID $id." );
	}
}

// Static front page.
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', 100 );

// Skip the editor welcome guides.
update_user_meta(
	1,
	$GLOBALS['wpdb']->get_blog_prefix() . 'persisted_preferences',
	array(
		'core/edit-post' => array( 'welcomeGuide' => false ),
		'core/edit-site' => array( 'welcomeGuide' => false ),
		'_modified'      => gmdate( 'c' ),
	)
);
