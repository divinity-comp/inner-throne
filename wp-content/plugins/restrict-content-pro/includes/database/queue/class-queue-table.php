<?php
/**
 * Queue Table.
 *
 * @package     RCP
 * @subpackage  Database\Tables
 * @copyright   Copyright (c) 2018, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

namespace RCP\Database\Tables;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use RCP\Database\Table;

/**
 * Setup the "rcp_queue" database table
 *
 * @since 3.0
 */
final class Queue extends Table {

	/**
	 * @var string Table name
	 */
	protected $name = 'queue';

	/**
	 * @var string Database version
	 */
	protected $version = 201810131;

	/**
	 * Queue constructor.
	 *
	 * @access public
	 * @since  3.0
	 * @return void
	 */
	public function __construct() {

		$this->global = is_plugin_active_for_network( plugin_basename( RCP_PLUGIN_FILE ) );

		parent::__construct();

	}

	/**
	 * Setup the database schema
	 *
	 * @access protected
	 * @since  3.0
	 * @return void
	 */
	protected function set_schema() {
		$this->schema = "id int unsigned NOT NULL AUTO_INCREMENT,
			queue varchar(20) NOT NULL DEFAULT '',
			name varchar(50) NOT NULL DEFAULT '',
			description varchar(256) NOT NULL DEFAULT '',
			callback varchar(512) NOT NULL DEFAULT '',
			total_count bigint(20) unsigned NOT NULL DEFAULT 0,
			current_count bigint(20) unsigned NOT NULL DEFAULT 0,
			step bigint(20) unsigned NOT NULL DEFAULT 0,
			status varchar(20) NOT NULL DEFAULT 'incomplete',
			uuid varchar(100) NOT NULL default '',
			PRIMARY KEY (id),
			UNIQUE KEY name_queue (name,queue),
			KEY queue (queue),
			KEY status (status)";
	}


}