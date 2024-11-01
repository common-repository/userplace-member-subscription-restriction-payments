<?php

namespace Userplace;

class CustomTables
{

	public static function create_all_custom_tables()
	{
		global $wpdb;
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}
		$schema = "CREATE TABLE {$wpdb->prefix}userplace_cards (
			id bigint(200) NOT NULL auto_increment,
			card_id tinytext NOT NULL,
			user tinytext NOT NULL,
			last4 tinytext NOT NULL,
			card_name tinytext NOT NULL,
			card_brand tinytext NOT NULL,
			is_default boolean DEFAULT 0 NOT NULL,
			deleted boolean DEFAULT 0 NOT NULL,
			expired_at tinytext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $collate;
		
		CREATE TABLE {$wpdb->prefix}userplace_invoices (
			id bigint(200) NOT NULL auto_increment,
			customer tinytext NOT NULL,
			amount tinytext NOT NULL,
			currency tinytext NOT NULL,
			date tinytext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta($schema);
	}
}
