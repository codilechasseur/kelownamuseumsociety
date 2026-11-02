<?php

/**
 * Class Tribe__Events__Pro__iCal
 *
 * @since 4.7.5
 */
class Tribe__Events__Pro__iCal {

	/**
	 * Attach hooks.
	 *
	 * @since 4.7.5
	 */
	public function hook() {
		add_filter( 'tribe_ical_feed_item', [ $this, 'filter_ical_feed_item' ], 10, 2 );
	}

	/**
	 * Filter on tribe_ical_feed_item to add the data information about the lat, lng if present
	 * on the Event.
	 *
	 * @since 4.7.5
	 * @since 7.8.5 Only add the geo information when the item has a location and the venue is visible.
	 *
	 * @param $item Array An array with the data of the item that is updated.
	 * @param $event \Wp_Post The Post object being modified.
	 *
	 * @return array
	 */
	public function filter_ical_feed_item( $item, $event ) {
		/** @var \Tribe__Events__Pro__Geo_Loc $geo */
		$geo  = Tribe__Events__Pro__Geo_Loc::instance();
		$long = $geo->get_lng_for_event( $event->ID );
		$lat  = $geo->get_lat_for_event( $event->ID );


		if ( empty( $long ) || empty( $lat ) ) {
			return $item;
		}

		$location = $this->find_location( $item );

		// Another plugin may have written the LOCATION line, so the venue itself decides whether it is visible.
		if ( ! $location || ! $this->is_venue_visible( $event->ID ) ) {
			return $item;
		}

		$current = join( '', $item );
		if ( false === strpos( $current, 'GEO:' ) ) {
			$item[] = sprintf( 'GEO:%s;%s', $lat, $long );
		}

		$title = str_replace(
			[ ',', "\n" ],
			[ '\,', '\n' ],
			html_entity_decode( tribe_get_address( $event->ID ), ENT_QUOTES )
		);

		if ( ! $title ) {
			return $item;
		}

		if ( false === strpos( $current, 'X-APPLE-STRUCTURED-LOCATION;' ) ) {
			$item[] = sprintf(
				"X-APPLE-STRUCTURED-LOCATION;VALUE=URI;X-ADDRESS=%s;X-APPLE-RADIUS=500;X-TITLE=%s:geo:%s,%s",
				str_replace( '\,', '', trim( $location ) ),
				trim( $title ),
				$long,
				$lat
			);
		}

		return $item;
	}

	/**
	 * Get the LOCATION value from the $item
	 *
	 * @since 4.7.5
	 * @since 7.8.5 Only reads a line that is the LOCATION property, not one that mentions it, such as the description.
	 *
	 * @param $item array The item where to look for the LOCATION value.
	 *
	 * @return string
	 */
	public function find_location( $item ) {
		$results = array_map(
			function ( $row ) {
				return substr( $row, strlen( 'LOCATION:' ) );
			},
			array_filter(
				$item,
				function ( $row ) {
					return 0 === strpos( $row, 'LOCATION:' );
				}
			)
		);

		$location = reset( $results );
		return empty( $location ) ? '' : $location;
	}

	/**
	 * Whether the current user may see the event's venue.
	 *
	 * A venue is visible when it is published, or the user can read it, and its password, if any, was supplied.
	 * This is checked here because older versions of The Events Calendar export the venue address unchecked.
	 *
	 * @since 7.8.5
	 *
	 * @param int $event_id The event post ID.
	 *
	 * @return bool Whether the event has a venue the current user may see.
	 */
	private function is_venue_visible( int $event_id ): bool {
		$venue_id = absint( tribe_get_venue_id( $event_id ) );
		// get_post() falls back to the global post for an empty ID.
		$venue = $venue_id ? get_post( $venue_id ) : null;

		if ( ! $venue instanceof WP_Post ) {
			return false;
		}

		if ( 'publish' !== $venue->post_status && ! current_user_can( 'read_post', $venue->ID ) ) {
			return false;
		}

		return ! post_password_required( $venue );
	}
}
