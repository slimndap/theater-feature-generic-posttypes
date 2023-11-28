<?php
/**
 * Event and date hierarchy
 * 
 * This file contains functions and hooks related to the management of an event taxonomy.
 * It defines mechanisms to handle the association between events and their dates, 
 * including the creation, update, and deletion of these associations.
 * 
 * Specifically, it manages a 'shadow taxonomy' where terms are used to represent events 
 * and their association with posts.
 * 
 * @since 1.0
 * @requires Theater\Generic_Post_Types\Setup\Post_Types
 */
 
namespace Theater\Generic_Post_Types\Setup\Post_Hierarchy;

use Theater\Generic_Post_Types\Setup\Post_Types as Post_Types;

// Manage association between events and dates.
add_action( 'init', __NAMESPACE__.'\register_event_taxonomy' );
add_action( 'wp_insert_post', __NAMESPACE__.'\create_event_term', 10, 3 );
add_action( 'before_delete_post', __NAMESPACE__.'\delete_event_term' );

/**
 * Create a new event term association.
 *
 * @param int    $event_post_id Post ID number.
 * @param object $event_post    The WP Post object.
 *
 * @return bool|int Term ID if created or false if an error occurred.
 */
function create_event_term_association( $event_post_id, $event_post ) {
	
	$new_event_term = \wp_insert_term( $event_post->post_title, get_event_taxonomy_key(), [ 'slug' => $event_post->post_name ] );

	if ( \is_wp_error( $new_event_term ) ) {
		return false;
	}

	\update_term_meta( $new_event_term['term_id'], 'event_post_id', $event_post_id );
	\update_post_meta( $post_id, 'event_term_id', $new_event_term['term_id'] );

	return $new_term;
}

/**
 * Create or update the event term based on the post.
 *
 * @param int     $event_post_id Post ID number.
 * @param object  $event_post    The WP Post object.
 * @param bool    $update        Whether the post is being updated.
 *
 * @return void
 */
 function create_event_term( $event_post_id, $event_post, $update ) {
	
	if ( $event_post->post_type !== Post_Types\get_event_post_type_key() ) {
		return false;
	}

	if ( 'auto-draft' === $event_post->post_status ) {
		return false;
	}

	$event_term = get_associated_event_term( $event_post_id );

	if ( ! $event_term ) {

		$event_term = \wp_insert_term( $event_post->post_title, get_event_taxonomy_key(), [ 'slug' => $event_post->post_name ] );
	
		if ( \is_wp_error( $event_term ) ) {
			\wp_die( $event_term );
		}
	
		\update_term_meta( $event_term['term_id'], 'event_post_id', $event_post_id );
		\update_post_meta( $post_id, 'event_term_id', $event_term['term_id'] );
	
		return;
	
	}
	
	if ( $update ) {

		if ( event_already_in_sync( $event_term, $event_post ) ) {
			return;
		}

		\wp_update_term(
			$event_term->term_id,
			get_event_taxonomy_key(),
			[
				'name' => $event_post->post_title,
				'slug' => $event_post->post_name,
			]
		);

		return;
		
	}
	
}

/**
 * Delete the event term associated with a post.
 *
 * @param int $event_post_id The ID of the post.
 *
 * @return void
 */
function delete_event_term( $event_post_id ) {

	$term = get_associated_event_term( $event_post_id );

	if ( ! $term ) {
		return false;
	}

	wp_delete_term( $term->term_id, get_event_taxonomy_key() );

}

/**
 * Get the associated event term ID of a given event post.
 *
 * @param object $event_post WP Post object.
 *
 * @return bool|int Returns the term_id or false if no associated term was found.
 */
function get_associated_event_term_id( $event_post ) {
	return get_post_meta( $event_post->ID, 'event_term_id', true );
}

/**
 * Retrieve the event taxonomy key.
 *
 * @return string The event taxonomy key.
 */
function get_event_taxonomy_key() {

	$key = sprintf( '%s_tax',  Post_Types\get_event_post_type_key() );
	
	return $key;
	
}

/**
 * Check if the current term and its associated post have the same title and slug.
 *
 * @param object $event_term The term object.
 * @param object $event_post The $_POST array.
 *
 * @return bool True if a match is found, or false if no match is found.
 */
function event_already_in_sync( $event_term, $event_post ) {
	if ( isset( $event_term->slug ) && isset( $event_post->post_name ) ) {
		if ( $event_term->name === $event_post->post_title && $event_term->slug === $event_post->post_name ) {
			return true;
		}
	} else {
		if ( $event_term->name === $event_post->post_title ) {
			return true;
		}
	}

	return false;
}

/**
 * Get the associated event post ID of a given term.
 *
 * @param object $event_term WP Term object.
 *
 * @return bool|int Returns the post_id or false if no associated post is found.
 */
function get_associated_event_post_id( $event_term ) {
	return get_term_meta( $event_term->term_id, 'event_post_id', true );
}

/**
 * Retrieve the associated post object for a given term.
 *
 * @param object $term     WP Term object.
 * @param string $post_type Post type name.
 *
 * @return bool|object Returns the associated post object or false if no post is found.
 */
function get_associated_event_post( $event_term, $post_type ) {

	if ( empty( $event_term ) ) {
		return false;
	}

	$event_post_id = get_associated_event_post_id( $event_term );

	if ( empty( $event_post_id ) ) {
		return false;
	}

	return get_post( $event_post_id );
}

/**
 * Retrieve the associated term object for a given post.
 *
 * @param object|int $event_post     WP Post object or Post ID.
 *
 * @return bool|object Returns the associated term object or false if no term is found.
 */
function get_associated_event_term( $event_post ) {

	if ( is_int( $event_post ) ) {
		$post = get_post( $event_post );
	}

	if ( empty( $event_post ) ) {
		return false;
	}

	$term_id = get_associated_event_term_id( $event_post );
	return get_term_by( 'id', $term_id, get_event_taxonomy_key() );
}

/**
 * Register the event taxonomy.
 *
 * @return void
 */
function register_event_taxonomy() {
	register_taxonomy(
		get_event_taxonomy_key(),
		Post_Types\get_date_post_type_key(),
		array(
			'label' => 'Event',
			'rewrite' => false,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'public' => false,
		)
	);	
}