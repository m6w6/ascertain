<?php

namespace ascertain;

/**
 * Use this trait in an \ascertain\Testable class
 */
trait Validator {
	/**
	 * @param string $e Exception class name
	 * @return \ascertain\Assert
	 */
	function assert($e = "\\InvalidArgumentException") {
		return new Assert($this, $e);
	}
}
