<?php
namespace WFV\Artisan;

use WFV\Artisan\Director;
use WFV\Artisan\FormArtisan;
use WFV\Composite\Form;

class DirectorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Instance of WFV\Artisan\Director.
	 *
	 * @access protected
	 * @var WFV\Artisan\Director $director
	 */
	protected static $director;

	/**
	 * Instantiate Director
	 *
	 */
	protected function setUp() {
		self::$director = new Director( 'entity' );
	}

	/**
	 * Reset
	 *
	 */
	protected function tearDown() {
		self::$director = null;
	}

	/**
	 * Does describe method return an instance of this director?
	 *
	 */
	public function test_director_describe_return_self_instance() {
		$result = self::$director
			->describe( 'phpunit', 'tested' );
		$this->assertInstanceOf( 'WFV\Artisan\Director', $result );
	}

	/**
	 * Does with method return an instance of this director?
	 *
	 */
	public function test_director_with_return_self_instance() {
		$result = self::$director
			->with( 'phpunit', array('lorem', 'ipsum') );
		$this->assertInstanceOf( 'WFV\Artisan\Director', $result );
	}
}
