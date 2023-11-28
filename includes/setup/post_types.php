<?php
/**
 * Custom Post Types for Theater: Events and Dates
 * 
 * This file introduces custom post types (CPT) for managing events and their associated dates.
 * Two CPTs are primarily defined: "Dates" and "Events". Dates represent individual occurrences 
 * of events, while Events represent the broader concept or occurrence that may have multiple dates.
 * 
 * Functions within provide mechanisms to:
 * - Define the post type details.
 * - Generate the post type keys.
 * - Register the custom post types to WordPress.
 * 
 * @since 1.0
 */

namespace Theater\Generic_Post_Types\Setup\Post_Types;
	
// Register custom post types for events and dates.
add_action( 'init', __NAMESPACE__.'\register_post_types' );

/**
 * Define post type parameters for dates and events.
 *
 * @return array Associative array of post type keys and their arguments.
 */
function get_post_type_definitions() {
	
	$posttype_definitions = array(
		
		get_date_post_type_key() => array(
			'label' => 'Dates',
			'description' => 'Event date',
			'public' => true,
			'show_in_rest' => true,
			'supports' => array(
				'title',
			),
			'rewrite' => false,
		),
		get_event_post_type_key() => array(
			'label' => 'Events',
			'description' => 'Event',
			'public' => true,
			'show_in_rest' => true,
			'supports' => array(
				'title', 'editor', 'thumbnail',
			),
		),
		
	);
	
	return $posttype_definitions;
	
}

/**
 * Provide the post type key for dates.
 *
 * @return string Post type key for dates.
 */
function get_date_post_type_key() {
	return 'date';
}

/**
 * Provide the post type key for events.
 *
 * @return string Post type key for events.
 */
function get_event_post_type_key() {
	return 'event';
}

/**
 * Register custom post types defined in the get_post_type_definitions function.
 *
 * @return void
 */
function register_post_types() {
	
	$posttype_definitions = get_post_type_definitions();
	
	foreach( $posttype_definitions as $post_type => $args ) {
		
		\register_post_type( $post_type, $args );
		
	}
	
}