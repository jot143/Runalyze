<?php

namespace Runalyze\Model\Trackdata;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-11-04 at 10:19:10.
 */
class PauseTest extends \PHPUnit_Framework_TestCase {

	public function testEmpty() {
		$P = new Pause();

		$this->assertEquals( 0, $P->time() );
		$this->assertEquals( 0, $P->duration() );
		$this->assertFalse( $P->hasHeartRateInfo() );
		$this->assertNull( $P->hrStart() );
		$this->assertNull( $P->hrEnd() );
	}

	public function testConstructor() {
		$P = new Pause(60, 10, 120, 100);

		$this->assertEquals( 60, $P->time() );
		$this->assertEquals( 10, $P->duration() );
		$this->assertTrue( $P->hasHeartRateInfo() );
		$this->assertEquals( 120, $P->hrStart() );
		$this->assertEquals( 100, $P->hrEnd() );
		$this->assertEquals( 20, $P->hrDiff() );
	}

	public function testArray() {
		$array = array(
			Pause::TIME => 60,
			Pause::DURATION => 10,
			Pause::HR_START => 120,
			Pause::HR_END => 100
		);

		$P = new Pause();
		$P->fromArray($array);

		$this->assertEquals( 60, $P->time() );
		$this->assertEquals( 10, $P->duration() );
		$this->assertTrue( $P->hasHeartRateInfo() );
		$this->assertEquals( 120, $P->hrStart() );
		$this->assertEquals( 100, $P->hrEnd() );
		$this->assertEquals( 20, $P->hrDiff() );

		$this->assertEquals( $array, $P->asArray() );
	}

}
