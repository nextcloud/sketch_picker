<?php

namespace OCA\SketchPicker\Tests;

use OCA\SketchPicker\AppInfo\Application;

class SketchServiceTest extends \PHPUnit\Framework\TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('sketch_picker', $app::APP_ID);
	}
}
