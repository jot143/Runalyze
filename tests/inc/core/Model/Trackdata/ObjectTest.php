<?php

namespace Runalyze\Model\Trackdata;

/**
 * Generated by hand
 */
class ObjectTest extends \PHPUnit_Framework_TestCase {

	protected function simpleObjectByStrings() {
		$P = new Pauses();
		$P->add(new Pause(20, 10, 140, 120));

		return new Object(array(
			Object::ACTIVITYID => 1,
			Object::TIME => '20'.Object::ARRAY_SEPARATOR.'40',
			Object::DISTANCE => '0.1'.Object::ARRAY_SEPARATOR.'0.2',
			Object::PACE => '3:20'.Object::ARRAY_SEPARATOR.'3:20',
			Object::HEARTRATE => '140'.Object::ARRAY_SEPARATOR.'120',
			Object::PAUSES => $P->asString()
		));
	}

	public function testEmptyObject() {
		$T = new Object();

		foreach ($T->properties() as $key) {
			$this->assertFalse($T->has($key));
		}

		$this->assertEquals(0, $T->num());
		$this->assertFalse($T->hasPauses());
		$this->assertTrue($T->pauses()->isEmpty());
	}

	public function testClearing() {
		$T = $this->simpleObjectByStrings();
		$T->clear();

		foreach ($T->properties() as $key) {
			$this->assertFalse($T->has($key));
		}

		$this->assertEquals(0, $T->num());
		$this->assertFalse($T->hasPauses());
	}

	public function testCreationWithString() {
		$T = $this->simpleObjectByStrings();

		$this->assertEquals(1, $T->activityID());
		$this->assertEquals(array(20, 40), $T->time());
		$this->assertEquals(array(0.1, 0.2), $T->distance());
		$this->assertEquals(array('3:20', '3:20'), $T->pace());
		$this->assertEquals(array(140, 120), $T->heartRate());

		$this->assertEquals(2, $T->num());
		$this->assertTrue($T->hasPauses());
		$this->assertEquals(1, $T->pauses()->num());
		$this->assertEquals(20, $T->pauses()->at(0)->hrDiff());

		$this->assertEquals(40, $T->totalTime());
		$this->assertEquals(0.2, $T->totalDistance());
	}

	public function testCreatingWithArrays() {
		$T = new Object(array(
			Object::CADENCE => array(180, 185),
			Object::POWER => array(200, 250),
			Object::TEMPERATURE => array(25, 24),
			Object::GROUNDCONTACT => array(200, 250),
			Object::VERTICAL_OSCILLATION => array(8.0, 7.5),
		));

		$this->assertEquals(2, $T->num());
		$this->assertEquals(array(180, 185), $T->cadence());
		$this->assertEquals(array(200, 250), $T->power());
		$this->assertEquals(array( 25,  24), $T->temperature());
		$this->assertEquals(array(200, 250), $T->groundcontact());
		$this->assertEquals(array(8.0, 7.5), $T->verticalOscillation());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testWrongArraySizes() {
		new Object(array(
			Object::TIME => array(1, 2, 3),
			Object::DISTANCE => array(0.01, 0.03)
		));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testWrongArraySizeViaSet() {
		$T = new Object(array(
			Object::TIME => array(1, 2, 3)
		));

		$T->set(Object::DISTANCE, array(0.01, 0.03));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSettingPausesDirectly() {
		$P = new Pauses();

		$T = new Object();
		$T->set(Object::PAUSES, $P->asString());
	}

	public function testDirectAccess() {
		$T = new Object(array(
			Object::TIME => array(1, 2, 3, 5, 10, 20)
		));

		$this->assertEquals( 1, $T->at(0, Object::TIME));
		$this->assertEquals( 2, $T->at(1, Object::TIME));
		$this->assertEquals( 3, $T->at(2, Object::TIME));
		$this->assertEquals( 5, $T->at(3, Object::TIME));
		$this->assertEquals(10, $T->at(4, Object::TIME));
		$this->assertEquals(20, $T->at(5, Object::TIME));
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function testInvalidAccessIndex() {
		$T = new Object(array(
			Object::TIME => array(1)
		));

		$T->at(2, Object::TIME);
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function testInvalidAccessKey() {
		$T = new Object(array(
			Object::TIME => array(1)
		));

		$T->at(0, Object::DISTANCE);
	}

	public function testDefectActivitiesFromHRMandGPXimport() {
		$T = new Object(array(
			Object::DISTANCE => array(0.05, 1.0, 1.5, 2.0),
			Object::TIME => array(10, 20, 30, 40),
			Object::HEARTRATE => array(120, 125, 130, 135, 140)
		));

		$this->assertEquals(4, $T->num());
	}

}
