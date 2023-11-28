<?php
namespace Theater\Generic_Post_Types\Admin\Event;

use Theater\Generic_Post_Types\Setup\Post_Types as Post_Types;

add_action( 'add_meta_boxes', __NAMESPACE__.'\register_meta_boxes' );

function register_meta_boxes( ) {
	\add_meta_box( 'event-dates', 'Dates', __NAMESPACE__.'\display_event_dates_metabox', Post_Types\get_event_post_type_key() );
}

function display_event_dates_metabox( $event_post ) {
	?>kkk<?php
}
	