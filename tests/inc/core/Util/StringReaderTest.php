<?php

namespace Runalyze\Util;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2015-01-15 at 10:53:14.
 */
class StringReaderTest extends \PHPUnit_Framework_TestCase {

	public function testFindDemandedPace() {
		$Reader = new StringReader();

		$this->assertEquals(0, $Reader->setString('No pace here')->findDemandedPace());
		$this->assertEquals(0, $Reader->setString('Wrong pattern for 3:20')->findDemandedPace());
		$this->assertEquals(200, $Reader->setString('Wrong pattern for 3:20')->findDemandedPace(' for '));
		$this->assertEquals(200, $Reader->setString('Correct pattern in 3:20')->findDemandedPace());
		$this->assertEquals(17, $Reader->setString('Whats about 17 seconds?')->findDemandedPace('about '));
		$this->assertEquals(3600 + 23*60 + 45, $Reader->setString('And with hours in 1:23:45?')->findDemandedPace());
		$this->assertEquals(200, $Reader->setString('And multiple times in 3:20 and 4:20?')->findDemandedPace());
	}

}
