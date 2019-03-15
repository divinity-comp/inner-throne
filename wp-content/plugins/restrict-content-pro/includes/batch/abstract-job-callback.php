<?php
/**
 * Abstract Job Callback
 *
 * @package   restrict-content-pro
 * @copyright Copyright (c) 2018, Restrict Content Pro team
 * @license   GPL2+
 * @since     3.0
 */

namespace RCP\Utils\Batch;


/**
 * Class Abstract_Job_Callback
 *
 * @package RCP\Utils\Batch
 */
abstract class Abstract_Job_Callback implements Job_Callback_Interface {

	/**
	 * Maximum number of seconds the job can run during each pass
	 *
	 * @var int $time_limit
	 */
	protected $time_limit = 15;

	/**
	 * The time this job started.
	 *
	 * @var int $start_time
	 */
	protected $start_time;

	/**
	 * The job object
	 *
	 * @var Job $job
	 */
	protected $job;

	/**
	 * The offset to use when querying records on each pass.
	 *
	 * @var int $offset
	 */
	protected $offset;

	/**
	 * The number of records to query on each pass.
	 *
	 * @var int $size
	 */
	protected $size = 20;

	/**
	 * Abstract_Job_Callback constructor.
	 *
	 * @param Job $job
	 *
	 * @since 3.0
	 */
	public function __construct( $job ) {
		$this->job = $job;
	}

	/**
	 * Executes the job.
	 *
	 * @since 3.0
	 */
	public function execute() {
		/**
		 * Make sure to call this method via parent::execute() in your extending class
		 * to update the offset during each batch.
		 */
		$this->offset = absint( $this->size * ( $this->get_job()->get_step() ) );
	}

	/**
	 * Runs any tasks required to finish a job.
	 *
	 * @since 3.0
	 * @return mixed|void
	 */
	abstract public function finish();

	/**
	 * Get the associated job object.
	 *
	 * @since 3.0
	 * @return Job
	 */
	public function get_job() {
		return $this->job;
	}

	/**
	 * Message to display upon job completion.
	 *
	 * @since 3.0
	 * @return string
	 */
	public function get_complete_message() {
		return __( 'Job completed successfully. You may now leave the page.', 'rcp' );
	}

	/**
	 * Determines if the job has exceeded its allowed time limit.
	 *
	 * @since 3.0
	 * @return bool
	 */
	protected function time_exceeded() {
		return ( ( time() - $this->start_time ) >= $this->time_limit );
	}

}