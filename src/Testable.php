<?php

namespace ascertain;

/**
 * Implement this interface to use the \ascertain\Validator trait
 */
interface Testable {
	/**
	 * @returns array of properties
	 */
	function export();
}
