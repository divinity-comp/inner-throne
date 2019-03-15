<?php
/**
 * Job Object
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro team
 * @license   GPL2+
 * @since     3.0
 */

namespace RCP\Utils\Batch;

use RCP\Base_Object;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Class Job
 *
 * @package RCP\Utils\Batch
 * @since   3.0
 */
final class Job extends Base_Object {

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @var string
	 */
	protected $queue = 'rcp_core';

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $description = '';

	/**
	 * @var string
	 */
	protected $callback = '';

	/**
	 * @var int
	 */
	protected $total_count = 0;

	/**
	 * @var int
	 */
	protected $current_count = 0;

	/**
	 * @var int
	 */
	protected $step = 0;

	/**
	 * @var string
	 */
	protected $status = 'incomplete';

	/**
	 * @var \RCP\Database\Queries\Queue
	 */
	private $queue_query;

	/**
	 * Job Config constructor.
	 *
	 * @param object $config Job row from the database.
	 *
	 * @since 3.0
	 */
	public function __construct( $config ) {

		try {
			$this->validate( $config );
		} catch ( \InvalidArgumentException $exception ) {

		}

		$config = $this->sanitize( $config );

		parent::__construct( $config );

		$this->queue_query = new \RCP\Database\Queries\Queue();
	}

	/**
	 * Get the job ID
	 *
	 * @since 3.0
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the associated queue
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_queue() {
		return $this->queue;
	}

	/**
	 * Get the job name
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the job description
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Get the callback class name
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_callback() {
		return $this->callback;
	}

	/**
	 * Get the callback object.
	 *
	 * @since 3.0
	 * @return Abstract_Job_Callback|false Callback class on success, false on failure.
	 */
	public function get_callback_object() {
		$class_name = $this->get_callback();

		if ( class_exists( $class_name ) ) {
			$object = new $class_name( $this );

			if ( method_exists( $object, 'execute' ) ) {
				return $object;
			}
		}

		return false;
	}

	/**
	 * Get the total number of items to be processed
	 *
	 * @since 3.0
	 * @return int
	 */
	public function get_total_count() {
		return $this->total_count;
	}

	/**
	 * Get the number of items processed so far
	 *
	 * @since 3.0
	 * @return int
	 */
	public function get_current_count() {
		return $this->current_count;
	}

	/**
	 * Set the total count
	 *
	 * @param int $count
	 *
	 * @since 3.0
	 */
	public function set_total_count( $count = 0 ) {
		$this->total_count = absint( $count );
		$this->save();
	}

	/**
	 * Add to the current count
	 *
	 * @param int $amount Amount to add.
	 *
	 * @since 3.0
	 */
	public function adjust_current_count( $amount = 0 ) {
		$this->current_count = $this->get_current_count() + (int) $amount;
		$this->save();
	}

	/**
	 * Get the current step
	 *
	 * @since 3.0
	 * @return int
	 */
	public function get_step() {
		return $this->step;
	}

	/**
	 * Set the next step
	 *
	 * @param int $step
	 *
	 * @since 3.0
	 */
	public function set_step( $step = 0 ) {
		$this->step = absint( $step );
		$this->save();
	}

	/**
	 * Get the job status
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Set the job status
	 *
	 * @since 3.0
	 *
	 * @param string $status
	 */
	public function set_status( $status = 'complete' ) {
		$this->status = sanitize_key( $status );
		$this->save();
	}

	/**
	 * Determines whether or not the job has been completed.
	 *
	 * @since 3.0
	 * @return boolean True if the job is completed, false if not.
	 */
	public function is_completed() {
		return ( ( $this->get_percent_complete() === 100 ) || ( 'complete' === $this->get_status() ) );
	}

	/**
	 * Returns the job completion percentage.
	 *
	 * @since 3.0
	 * @return int
	 */
	public function get_percent_complete() {
		$percent_complete = 0;
		$total_count      = $this->get_total_count();

		if ( $total_count > 0 ) {
			$percent_complete = (int) ( ( $this->get_current_count() / $total_count ) * 100 );
		}

		if ( $percent_complete > 100 ) {
			$percent_complete = 100;
		}

		return $percent_complete;
	}

	/**
	 * Process the next batch of this job.
	 *
	 * @since 3.0
	 * @return true|\WP_Error True on success, WP_Error object on failure.
	 */
	public function process_batch() {

		$object = $this->get_callback_object();

		if ( empty( $object ) ) {
			return new \WP_Error( 'invalid_job_callback', sprintf( __( 'Error processing %s - invalid callback.', 'rcp' ), $this->get_name() ) );
		}

		$object->execute();

		return true;

	}

	/**
	 * Save the job
	 *
	 * @since 3.0
	 */
	public function save() {
		$args = array(
			'queue'         => $this->get_queue(),
			'name'          => $this->get_name(),
			'description'   => $this->get_description(),
			'callback'      => $this->get_callback(),
			'total_count'   => $this->get_total_count(),
			'current_count' => $this->get_current_count(),
			'step'          => $this->get_step(),
			'status'        => $this->get_status()
		);

		$updated = $this->queue_query->update_item( $this->get_id(), $args );

		if ( $updated === false ) {
			rcp_log( sprintf( 'There was an error saving the job in %s. Job config: %s', __METHOD__, var_export( $args, true ) ), true );
		}
	}

	/**
	 * Validate the configuration to ensure all requirements are met.
	 *
	 * @param object $config
	 *
	 * @since 3.0
	 */
	private function validate( $config ) {

		$error = __( 'You must supply a valid name, description, and JobInterface callback when registering a batch job.', 'rcp' );

		if ( empty( $config ) ) {
			throw new \InvalidArgumentException( $error );
		}

		if ( empty( $config->name ) || empty( $config->description ) ) {
			throw new \InvalidArgumentException( $error );
		}

		if ( empty( $config->callback ) || ! class_exists( $config->callback ) ) {
			throw new \InvalidArgumentException( $error );
		}

		$interfaces = class_implements( $config->callback );

		if ( false === $interfaces || ! in_array( 'RCP\Utils\Batch\JobInterface', $interfaces, true ) ) {
			throw new \InvalidArgumentException( $error );
		}
	}

	/**
	 * Sanitize the configuration.
	 *
	 * @param object $config
	 *
	 * @since 3.0
	 * @return array
	 */
	private function sanitize( $config ) {
		return [
			'id'            => ! empty( $config->id ) ? absint( $config->id ) : false,
			'queue'         => ! empty( $config->queue ) ? sanitize_key( $config->queue ) : 'rcp_core',
			'name'          => sanitize_text_field( $config->name ),
			'description'   => wp_kses_post( $config->description ),
			'callback'      => $config->callback, // already validated to be a callable class
			'total_count'   => ! empty( $config->total_count ) ? absint( $config->total_count ) : 0,
			'current_count' => ! empty( $config->current_count ) ? absint( $config->current_count ) : 0,
			'step'          => ! empty( $config->step ) ? absint( $config->step ) : 0,
			'status'        => empty( $config->status ) ? 'incomplete' : $config->status,
		];
	}
}
