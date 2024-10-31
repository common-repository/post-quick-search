<?php
/*
Plugin Name: Admin Post Quick Search
Plugin URI:  http://wordpress.org/plugins
Description: Adds an ajaxy search box to the post list page
Version:     0.1.4
Author:      Adam Silverstein
Author URI:
License:     GPLv2+
Domain Path: /languages
*/

/**
 * Copyright (c) 2014 Adam Silverstein (email : adam@10up.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using grunt-wp-plugin
 * Copyright (c) 2013 10up, LLC
 * https://github.com/10up/grunt-wp-plugin
 */

// Useful global constants
define( 'WPPOSTQUICKSEARCH_VERSION', '0.1.0' );
define( 'WPPOSTQUICKSEARCH_URL',     plugin_dir_url( __FILE__ ) );
define( 'WPPOSTQUICKSEARCH_PATH',    dirname( __FILE__ ) . '/' );

/**
 * Default initialization for the plugin:
 * - Registers the default textdomain.
 */
function wppostquicksearch_init() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'wppostquicksearch' );
	load_textdomain( 'wppostquicksearch', WP_LANG_DIR . '/wppostquicksearch/wppostquicksearch-' . $locale . '.mo' );
	load_plugin_textdomain( 'wppostquicksearch', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	add_action( 'admin_enqueue_scripts', 'wppostquicksearch_scripts' );
	add_action( 'wp_ajax_post_search', 'wp_ajax_wppostquicksearch' );

}

function wppostquicksearch_scripts( $hook ) {
	if( 'edit.php' != $hook ) {
		return;
	}

	wp_enqueue_script( 'wppostquicksearch_script', plugin_dir_url( __FILE__ ) . 'assets/js/src/wordpress_post_quick_search.js', array( 'jquery', 'jquery-ui-autocomplete' ) );
	wp_localize_script( 'wppostquicksearch_script', '_ajax_nonce', wp_create_nonce( 'wp_ajax_wppostquicksearch' )  );
}
/**
 * Ajax handler for post search.
 *
 * @since 0.1.0
 */
function wp_ajax_wppostquicksearch() {
	check_ajax_referer( 'wp_ajax_wppostquicksearch' );
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error();
	}
	if ( isset( $_GET['term'] ) ) {
		$term = sanitize_key( $_GET['term'] );
	} else {
		wp_send_json_error();
	}
	$term = trim( $term );
	// Require 2 chars for matching
	if ( function_exists( 'mb_strlen' ) ) {
		if ( mb_strlen( $term ) < 2 ) {
			wp_send_json_error();
		}
	} elseif ( strlen( $term ) < 2 ) {
		wp_send_json_error();
	}

	$results = array();
	$args = array(
		's'         => $term,
		'post_type' => sanitize_key( $_GET['typenow'] )
	);
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$results[] = array(
				'label' => get_the_title(),
				'value' => get_edit_post_link( get_the_ID() ),
			);
		}
	}
	wp_reset_postdata();
	wp_send_json( $results );
}

/**
 * Activate the plugin
 */
function wppostquicksearch_activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	wppostquicksearch_init();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wppostquicksearch_activate' );

/**
 * Deactivate the plugin
 * Uninstall routines should be in uninstall.php
 */
function wppostquicksearch_deactivate() {

}
register_deactivation_hook( __FILE__, 'wppostquicksearch_deactivate' );

// Wireup actions
add_action( 'init', 'wppostquicksearch_init' );

// Wireup filters

// Wireup shortcodes
