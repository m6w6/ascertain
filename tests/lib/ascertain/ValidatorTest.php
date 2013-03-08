<?php

namespace ascertain;

include __DIR__."/../../setup.inc";

class Test implements Testable
{
	use Validator;
	
	public $good;
	
	function __construct($good) {
		$this->good = $good;
	}
	
	function export() {
		return array_map(function($v) {
			return $v[(int)$this->good];
		}, array(
			"any"         => [0,1],
			"boolean"     => ["nay", true],
			"containing"  => ["im 1", "im 2"],
			"containing2" => [[1], [1,2]],
			"email"       => ["@nirvana", "mike@php.net"],
			"float"		  => ["foo", 123.123],
			"integer"     => [123.1, 123],
			"ip"          => ["543.234.123.000", "123.234.98.0"],
			"len"         => ["foo","foobar"],
			"matching"    => ["foo","foo"],
			"nothing"     => [0, ""],
			"numeric"     => ["123foo123", "123.123"],
			"printable"   => ["\r\n", "easy test"],
			"scalar"      => [null, 1],
			"url"         => ["this-::is a h#rd one", "http://because/probably?everything=valid#here"],
		));
	}
}

class ValidatorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \ascertain\Test
	 */
	protected $good;
	
	/**
	 * @var \ascertain\Test
	 */
	protected $bad;

	protected function setUp() {
		$this->good = new Test(true);
		$this->bad  = new Test(false);
	}
	
	public function testTestNothing() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("nothing")->isNothing("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("nothing")->isNotNothing("error");
		$this->assertEquals(1, $good->hasErrors());
		$this->assertSame(array("nothing"=>array("error")), $good->getErrors());
		$good->resetErrors();
		$this->assertSame(array(), $good->getErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("nothing")->isNotNothing("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("nothing")->isNothing("error");
		$this->assertEquals(1, $bad->hasErrors());
		$this->assertSame(array("nothing"=>array("error")), $bad->getErrors());
		$bad->resetErrors();
		$this->assertSame(array(), $bad->getErrors());
	}

	public function testTestNumeric() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("numeric")->isNumeric("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("numeric")->isNotNumeric("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("numeric")->isNotNumeric("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("numeric")->isNumeric("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestScalar() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("scalar")->isScalar("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("scalar")->isNotScalar("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("scalar")->isNotScalar("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("scalar")->isScalar("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestPrintable() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("printable")->isPrintable("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("printable")->isNotPrintable("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("printable")->isNotPrintable("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("printable")->isPrintable("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestLen() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("len")->isLen(4, "error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("len")->isNotLen(4, "error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("len")->isNotLen(4, "error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("len")->isLen(4, "error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestInteger() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("integer")->isInteger("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("integer")->isNotInteger("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("integer")->isNotInteger("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("integer")->isInteger("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestBoolean() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("boolean")->isBoolean("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("boolean")->isNotBoolean("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("boolean")->isNotBoolean("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("boolean")->isBoolean("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestFloat() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("float")->isFloat("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("float")->isNotFloat("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("float")->isNotFloat("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("float")->isFloat("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestUrl() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("url")->isUrl("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("url")->isNotUrl("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("url")->isNotUrl("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("url")->isUrl("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestEmail() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("email")->isEmail("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("email")->isNotEmail("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("email")->isNotEmail("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("email")->isEmail("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestIp() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("ip")->isIp("error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("ip")->isNotIp("error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("ip")->isNotIp("error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("ip")->isIp("error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestContaining() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("containing")->isContaining(2, "error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("containing")->isNotContaining(2, "error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("containing")->isNotContaining(2, "error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("containing")->isContaining(2, "error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestContaining2() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("containing2")->isContaining(1, "error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("containing2")->isNotContaining(1, "error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("containing2")->isNotContaining(1, "error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("containing2")->isContaining(1, "error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestMatching() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("matching")->isMatching("/^\w+\$/", "error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("matching")->isNotMatching("/^\w+\$/", "error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("matching")->isNotMatching("/^\$/", "error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("matching")->isMatching("/^\$/", "error");
		$this->assertEquals(1, $bad->hasErrors());
	}

	public function testTestAny() {
		$good = $this->good->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $good);
		$good->that("any")->isAny(array(1,2), "error");
		$this->assertEquals(0, $good->hasErrors());
		$good->that("any")->isNotAny(array(1,2), "error");
		$this->assertEquals(1, $good->hasErrors());
		
		$bad = $this->bad->assert(false);
		$this->assertInstanceOf("\\ascertain\\Assert", $bad);
		$bad->that("any")->isNotAny(array(1,2), "error");
		$this->assertEquals(0, $bad->hasErrors());
		$bad->that("any")->isAny(array(1,2), "error");
		$this->assertEquals(1, $bad->hasErrors());
	}
}
