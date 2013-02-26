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
	 * Succeeded assertions
	 * @var array
	 */
	private $validationResults = array();
	
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
	 * Reset assertions failures/results
	 * @return \ascertain\Assert
	 */
	function resetErrors() {
		$this->validationResults = array();
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
	 * @param mixed &$r
	 * @param string $v
	 * @param string $c
	 * @return bool
	 */
	function test(&$r, $v, $c) {
		$r = !strcmp($v, $c);
		return $r;
	}
	
	/**
	 * !strlen($v)
	 * @param mixed &$r
	 * @param string $v
	 * @return bool
	 */
	function testNothing(&$r, $v) {
		$r = !strlen($v);
		return $r;
	}
	
	/**
	 * is_numeric($v)
	 * @param mixed &$r
	 * @param string $v
	 * @return bool
	 */
	function testNumeric(&$r, $v) {
		$r =  is_numeric($v);
		return $r;
	}
	
	/**
	 * Test whether the argument is scalar
	 * @param mixed &$r
	 * @param mixed $v
	 * @param bool $strictNulls
	 * @return bool
	 */
	function testScalar(&$r, $v, $strictNulls = false) {
		$r = is_scalar($v) && (!$strictNulls || !isset($v));
		return $r;
	}
	
	/**
	 * Test wheter the argument constists only of printable characters
	 * @param mixed &$r
	 * @param string $v
	 * @return bool
	 */
	function testPrintable(&$r, $v) {
		return preg_match("/^[[:print:]\\P{Cc}]*\$/u", $v, $r);
	}
	
	/**
	 * Test wheter the string length is between a certain range
	 * @param mixed &$r
	 * @param string $v
	 * @param int $min
	 * @param int $max
	 * @return bool
	 */
	function testLen(&$r, $v, $min, $max) {
		return $this->testRange($r, function_exists("mb_strlen") ? mb_strlen($v) : strlen($v), $min, $max);
	}
	
	/**
	 * Test wheter a value is between a certain range
	 * @param mixed &$r
	 * @param mixed $v
	 * @param mixed $min
	 * @param mixed $max
	 * @return bool
	 */
	function testRange(&$r, $v, $min, $max) {
		$r = $v >= $min && $v <= $max;
		return $r;
	}

	/**
	 * Test for a valid integer with FILTER_VALIDATE_INT
	 * @param mixed &$r
	 * @param mixed $v
	 * @param array $options
	 * @return bool
	 */
	function testInteger(&$r, $v, array $options = null) {
		$r = filter_var($v, FILTER_VALIDATE_INT, $options);
		return $r !== false;
	}
	
	/**
	 * Test for a valid boolean with FILTER_VALIDATE_BOOLEAN
	 * @param mixed &$r
	 * @param type $v
	 * @param array $options
	 * @return type
	 */
	function testBoolean(&$r, $v, array $options = null) {
		$options["flags"] = isset($options["flags"]) ? $options["flags"]|FILTER_NULL_ON_FAILURE : FILTER_NULL_ON_FAILURE;
		$r = filter_var($v, FILTER_VALIDATE_BOOLEAN, $options);
		return isset($r);
	}
	
	/**
	 * Test for a valid float with FILTER_VALIDATE_FLOAT
	 * @param mixed &$r
	 * @param mixed $v
	 * @param array $options
	 * @return bool
	 */
	function testFloat(&$r, $v, array $options = null) {
		$r = filter_var($v, FILTER_VALIDATE_FLOAT, $options);
		return $r !== false;
	}
	
	/**
	 * Test for a valid regular expression with FILTER_VALIDATE_REGEXP
	 * @param mixed &$r
	 * @param string $v
	 * @param array $options
	 * @return bool
	 */
	function testRegexp(&$r, $v, array $options = null) {
		$r = filter_var($v, FILTER_VALIDATE_REGEXP, $options);
		return $r !== false;
	}

	/**
	 * Test for a valid URL with FILTER_VALIDATE_URL
	 * @param mixed &$r
	 * @param string $v
	 * @param array $options
	 * @return bool
	 */
	function testUrl(&$r, $v, array $options = null) {
		$r = filter_var($v, FILTER_VALIDATE_URL, $options);
		return $r !== false;
	}
	
	/**
	 * Test for a valid email address with FILTER_VALIDATE_EMAIL
	 * @param mixed &$r
	 * @param string $v
	 * @param array $options
	 * @return bool
	 */
	function testEmail(&$r, $v, array $options = null) {
		$r = filter_var($v, FILTER_VALIDATE_EMAIL, $options);
		return $r !== false;
	}
	
	/**
	 * Test for a valid IP address with FILTER_VALIDATE_IP
	 * @param mixed &$r
	 * @param string $v
	 * @param array $options
	 * @return bool
	 */
	function testIp(&$r, $v, array $options = null) {
		$r = filter_var($v, FILTER_VALIDATE_IP, $options);
		return $r !== false;
	}

	/**
	 * Test whether a string contains another string
	 * @param mixed &$r
	 * @param type $v haystack
	 * @param type $n needle
	 * @param bool $cs case-sensitive
	 * @return bool
	 */
	function testContains(&$r, $v, $n, $cs = true) {
		if (is_array($v)) {
			if (!$cs) {
				$v = array_change_key_case($v);
				$n = strtolower($n);
			}
			if (array_key_exists($n, $v)) {
				$r = $v[$n];
				return true;
			}
			return $r = false;
		} else {
			if ($cs) {
				$r = strstr($v, $n);
			} else {
				$r = stristr($v, $n);
			}
			return $r !== false;
		}
	}
	
	/**
	 * Thest if a regular expression matches
	 * @param mixed &$r
	 * @param string $v
	 * @param stirng $regex
	 * @param int $flags
	 * @return bool
	 */
	function testMatching(&$r, $v, $regex, $flags = 0) {
		return preg_match($regex, $v, $r, $flags) > 0;
	}
	
	/**
	 * Semantic is(Not) wrapper to the assertions
	 * @param string $method
	 * @param array $args
	 * @return \ascertain\Assert
	 * @throws InvalidArgumentException (or rahter the configured exception)
	 */
	function __call($method, $args) {
		$match = null;
		if ($this->inspectCondition && preg_match("/^is(Not)?(.*)\$/i", $method, $match)) {
			list(, $not, $test) = $match;
			
			$result = null;
			$error = array_pop($args);
			array_unshift($args, $this->properties[$this->inspectedProperty]);
			array_unshift($args, null);
			$args[0] = &$result;
			if (call_user_func_array(array($this, "test$test"), $args) === !!$not) {
				$this->validationResults[$this->inspectedProperty][] = $args[0];
				$this->validationErrors[$this->inspectedProperty][] = $error;
				if (($exception = $this->exceptionClass)) {
					throw new $exception("$this->inspectedProperty $error");
				}
			}
		}
		return $this;
	}
	
}
