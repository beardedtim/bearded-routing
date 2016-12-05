<?php
use PHPUnit\Framework\TestCase;

class BeardedRouteTest extends TestCase{
	public function testSettingRoute(){
		$route = new BeardedRoute('/api');
		$this->assetEquals(
			$route->route,
			'/api'
		);
	}
}