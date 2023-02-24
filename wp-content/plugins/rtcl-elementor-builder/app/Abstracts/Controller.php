<?php
/**
 * Abstract Class for Controller.
 *
 * @package RTCL_Elementor_Builder
 */

namespace RtclElb\Abstracts;

use RtclElb\Helpers\Fns;

/**
 * Abstract Class for Controller.
 */
abstract class Controller {
	/**
	 * Classes to include.
	 *
	 * @return array
	 */
	abstract protected function classes();

	/**
	 * Initializes the class.
	 *
	 * @return void
	 */
	public function init() {
		if ( empty( $this->classes() ) ) {
			return;
		}

		Fns::instances( $this->classes() );
	}
}
