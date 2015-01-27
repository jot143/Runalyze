<?php

namespace Runalyze\Calculation\JD;

use Runalyze\Configuration\Category;
use PDO;

class ShapeFake extends Shape {
	public function calculate() {
		$this->Value = 50;
	}
}

class CategoryFake extends Category\Vdot {
	public function __construct($vdotDays = 30, $elevationCorrector = false) {
		parent::__construct();

		$this->object('VDOT_DAYS')->set($vdotDays);
		$this->object('VDOT_USE_CORRECTION_FOR_ELEVATION')->set($elevationCorrector);
	}
}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-11-30 at 16:27:55.
 */
class ShapeTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \PDO
	 */
	protected $PDO;

	protected function setUp() {
		$this->PDO = new PDO('sqlite::memory:');
		$this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->PDO->exec('CREATE TABLE IF NOT EXISTS `'.PREFIX.'training` (
			`accountid` int(10),
			`sportid` int(10),
			`time` int(10),
			`s` int(10),
			`use_vdot` tinyint(1),
			`vdot` decimal(5,2),
			`vdot_with_elevation` decimal(5,2)
			);
		');

		VDOTCorrector::setGlobalFactor(1.0);
	}
	protected function tearDown() {
		$this->PDO->exec('DROP TABLE `'.PREFIX.'training`');
	}

	public function testWithoutData() {
		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake());
		$Shape->calculate();

		$this->assertEquals(0, $Shape->value());
	}

	public function testSimpleCalculation() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 50, 60)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 2, 1, 60, 70)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 80, 80)');

		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake());
		$Shape->calculate();

		$this->assertEquals(62.5, $Shape->value());
	}

	public function testSimpleCalculationWithElevation() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 50, 60)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 2, 1, 60, 70)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 80, 80)');

		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake(5, true));
		$Shape->calculate();

		$this->assertEquals(70, $Shape->value());
	}

	public function testIDs() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 50, 0)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 2, '.time().', 1, 1, 80, 0)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(2, 1, '.time().', 1, 1, 80, 0)');

		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake());
		$Shape->calculate();

		$this->assertEquals(50, $Shape->value());
	}

	public function testVDOTdays() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 50, 0)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.(time() - 10*DAY_IN_S).', 1, 1, 80, 0)');

		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake(5));
		$Shape->calculate();

		$this->assertEquals(50, $Shape->value());
	}

	public function testUseFlag() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 1, 50, 0)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` VALUES(1, 1, '.time().', 1, 0, 80, 0)');

		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake());
		$Shape->calculate();

		$this->assertEquals(50, $Shape->value());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCallWithoutCalculation() {
		$Shape = new Shape($this->PDO, 1, 1, new CategoryFake());
		$Shape->value();
	}

	public function testCorrector() {
		$Shape = new ShapeFake($this->PDO, 1, 1, new CategoryFake());
		$Shape->setCorrector(new VDOTCorrector(0.9));
		$Shape->calculate();

		$this->assertEquals(50, $Shape->uncorrectedValue());
		$this->assertEquals(45, $Shape->value());
	}

}
