<?php

namespace ascertain;

/**
 * Be sure to always pass an error string as last argument to the assertion:
 * <code>
 *	$testable->assert()
 *		->that("name")->isLen(3, 40, "must be between 3 and 40 characters long")
 *		->that("email")->isEmail("is not valid");
 * </code>
 */
class Assert
{
	/**
	 * Testable properties
	 * @var array
	 */
	private $properties;
	
	/**
	 * The name of the currently inspected property
	 * @var string
	 */
	private $inspectedProperty;
	
	/**
	 * Whether the argument to when() was true
	 * @var bool
	 */
	private $inspectCondition = true;
	
	/**
	 * Whether assertions cause exceptions of this type
	 * @var string
	 */
	private $exceptionClass;
	
	/**
	 * Failed assertions
	 * @var array
	 */
	private $validationErrors = array();
	
	/**
	 * @param \ascertain\Testable $testable
	 * @param string $exceptionClass
	 */
	function __construct(Testable $testable, $exceptionClass = null) {
		$this->properties = $testable->export();
		$this->exceptionClass = $exceptionClass;
	}
	
	/**
	 * @return int count of failed assertions
	 */
	function hasErrors() {
		return count($this->validationErrors);
	}
	
	/**
	 * @return array of failed assertions
	 */
	function getErrors() {
		return $this->validationErrors;
	}
	
	/**
	 * Reset failed assertions
	 * @return \ascertain\Assert
	 */
	function resetErrors() {
		$this->validationErrors = array();
		return $this;
	}
	
	/**
	 * Set the currently inspected property
	 * @param string $property
	 * @return \ascertain\Assert
	 */
	function that($property) {
		$this->inspectCondition = true;
		$this->inspectedProperty = $property;
		return $this;
	}
	
	/**
	 * The following assertions are only tested if the argument is true
	 * @param bool $condition
	 * @return \ascertain\Assert
	 */
	function when($condition) {
		$this->inspectCondition = $condition;
		return $this;
	}
	
	/**
	 * !strcmp($v, $c)
	 * @param string $v
	 * @param string $c
	 * @return bool
	 */
	function test($v, $c) {
		return !strcmp($v, $c);
	}
	
	/**
	 * !strlen($v)
	 * @param string $v
	 * @return bool
	 */
	function testNothing($v) {
		return !strlen($v);
	}
	
	/**
	 * is_numeric($v)
	 * @param string $v
	 * @return bool
	 */
	function testNumeric($v) {
		return is_numeric($v);
	}
	
	/**
	 * Test wheter string representations of original and the int-cast equal
	 * @param mixed $v
	 * @return bool
	 */
	function testInteger($v) {
		return ((string) $v) === ((string)(int) $v);
	}
	
	/**
	 * Test whether the argument is scalar
	 * @param mixed $v
	 * @param bool $strictNulls
	 * @return bool
	 */
	function testScalar($v, $strictNulls = false) {
		return is_scalar($v) && (!$strictNulls || !isset($v));
	}
	
	/**
	 * Test wheter the argument constists only of printable characters
	 * @param string $v
	 * @return bool
	 */
	function testPrintable($v) {
		return preg_match("/^[[:print:]\\P{Cc}]*\$/u", $v) > 0;
	}
	
	/**
	 * Test wheter the string length is between a certain range
	 * @param string $v
	 * @param int $min
	 * @param int $max
	 * @return bool
	 */
	function testLen($v, $min, $max) {
		return $this->testRange(function_exists("mb_strlen") ? mb_strlen($v) : strlen($v), $min, $max);
	}
	
	/**
	 * Test wheter a value is between a certain range
	 * @param mixed $v
	 * @param mixed $min
	 * @param mixed $max
	 * @return bool
	 */
	function testRange($v, $min, $max) {
		return $v >= $min && $v <= $max;
	}
	
	/**
	 * Test for a valid email address with FILTER_VALIDATE_EMAIL
	 * @param stting $v
	 * @param int $options
	 * @return bool
	 */
	function testEmail($v, $options = null) {
		return filter_var($v, FILTER_VALIDATE_EMAIL, $options) !== false;
	}
	
	/**
	 * Test for a valid URL with FILTER_VALIDATE_URL
	 * @param string $v
	 * @param int $options
	 * @return bool
	 */
	function testUrl($v, $options = null) {
		return filter_var($v, FILTER_VALIDATE_URL, $options) !== false;
	}
	
	/**
	 * Test whether a string contains another string
	 * @param type $v haystack
	 * @param type $n needle
	 * @param bool $ci case-sensitive
	 * @return bool
	 */
	function testContains($v, $n, $ci = true) {
		return ($ci ? strpos($v, $n) : stripos($v, $n)) !== false;
	}
	
	/**
	 * Thest if a regular expression matches
	 * @param string $v
	 * @param stirng $regex
	 * @param int $flags
	 * @return int
	 */
	function testRegex($v, $regex, $flags = 0) {
		return preg_match($regex, $v, null, $flags);
	}
	
	/**
	 * Semantic is(Not) wrapper to the assertions
	 * @param string $method
	 * @param array $args
	 * @return \ascertain\Assert
	 * @throws InvalidArgumentException (or rahter the configured exception)
	 */
	function __call($method, $args) {
		if ($this->inspectCondition && preg_match("/^is(Not)?(.*)\$/i", $method, $match)) {
			list(, $not, $test) = $match;
			
			$error = array_pop($args);
			array_unshift($args, $this->properties[$this->inspectedProperty]);
			if (call_user_func_array(array($this, "test$test"), $args) == !!$not) {
				if (($exception = $this->exceptionClass)) {
					throw new $exception("$this->inspectedProperty $error");
				}
				$this->validationErrors[$this->inspectedProperty][] = $error;
			}
		}
		return $this;
	}
	
}
