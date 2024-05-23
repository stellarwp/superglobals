<?php

namespace StellarWP\SuperGlobals\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StellarWP\SuperGlobals\SuperGlobals;

final class SuperGlobalsTest extends TestCase {
	/**
	 * @test
	 */
	public function it_should_get_server_var() {
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->assertEquals( $_SERVER['REQUEST_METHOD'], SuperGlobals::get_server_var( 'REQUEST_METHOD', 'default' ) );
		$this->assertNotEquals( 'POST', SuperGlobals::get_server_var( 'REQUEST_METHOD', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_server_var( 'REQUEST_URI', 'default' ) );

		unset( $_SERVER['REQUEST_METHOD'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_var_from_request() {
		$_REQUEST['bork'] = 'moo';

		$this->assertEquals( $_REQUEST['bork'], SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertEquals( $_REQUEST['bork'], SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_var( 'hello', 'default' ) );

		unset( $_REQUEST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_var_from_get() {
		$_REQUEST['bork'] = 'moo';
		$_GET['bork'] = 'moo';

		$this->assertEquals( $_GET['bork'], SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertEquals( $_GET['bork'], SuperGlobals::get_get_var( 'bork', 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_get_var( 'bork', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_var( 'hello', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_get_var( 'hello', 'default' ) );

		unset( $_REQUEST['bork'] );
		unset( $_GET['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_var_from_post() {
		$_GET['bork'] = 'blarg';
		$_POST['bork'] = 'moo';

		$this->assertEquals( $_POST['bork'], SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertEquals( $_POST['bork'], SuperGlobals::get_post_var( 'bork', 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_post_var( 'bork', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_var( 'hello', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_post_var( 'hello', 'default' ) );

		unset( $_GET['bork'] );
		unset( $_POST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_var_from_request_when_available() {
		$_REQUEST['bork'] = 'moo';
		$_GET['bork'] = 'blarg';
		$_POST['bork'] = 'yay';

		$this->assertEquals( $_REQUEST['bork'], SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_var( 'hello', 'default' ) );

		unset( $_REQUEST['bork'] );
		unset( $_GET['bork'] );
		unset( $_POST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_nested_var_from_request_when_available() {
		$_REQUEST['bork'] = [
			'word' => 'moo',
		];

		$this->assertEquals( $_REQUEST['bork']['word'], SuperGlobals::get_var( [ 'bork', 'word' ], 'default' ) );
		$this->assertNotEquals( 'whee', SuperGlobals::get_var( [ 'bork', 'word' ], 'default' ) );
		$this->assertEquals( 'default', SuperGlobals::get_var( [ 'bork', 'another' ], 'default' ) );

		unset( $_REQUEST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_sanitize_string() {
		$_REQUEST['bork'] = '<script>alert("hello");</script>';

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', SuperGlobals::get_var( 'bork', 'default' ) );
		$this->assertNotEquals( $_REQUEST['bork'], SuperGlobals::get_var( 'bork', 'default' ) );

		unset( $_REQUEST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_sanitize_deeply() {
		$_REQUEST['bork'] = [
			'thing' => [
				'dirty' => '<script>alert("hello");</script>',
			],
		];

		$var = SuperGlobals::get_var( 'bork', 'default' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['thing']['dirty'] );
		$this->assertNotEquals( $_REQUEST['bork'], $var['thing']['dirty'] );

		unset( $_REQUEST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_cookie_superglobal() {
		$dirty = '<script>alert("hello");</script>';
		$_COOKIE['bork'] = [
			'thing' => [
				'dirty' => $dirty,
			],
		];

		$var = SuperGlobals::get_raw_superglobal( 'COOKIE' );

		$this->assertNotEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertEquals( $dirty, $var['bork']['thing']['dirty'] );

		$var = SuperGlobals::get_sanitized_superglobal( 'COOKIE' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertNotEquals( $dirty, $var['bork']['thing']['dirty'] );

		unset( $_COOKIE['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_env_superglobal() {
		$dirty = '<script>alert("hello");</script>';
		$_ENV['bork'] = [
			'thing' => [
				'dirty' => $dirty,
			],
		];

		$var = SuperGlobals::get_raw_superglobal( 'ENV' );

		$this->assertNotEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertEquals( $dirty, $var['bork']['thing']['dirty'] );

		$var = SuperGlobals::get_sanitized_superglobal( 'ENV' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertNotEquals( $dirty, $var['bork']['thing']['dirty'] );

		unset( $_ENV['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_get_superglobal() {
		$dirty = '<script>alert("hello");</script>';
		$_GET['bork'] = [
			'thing' => [
				'dirty' => $dirty,
			],
		];

		$var = SuperGlobals::get_raw_superglobal( 'GET' );

		$this->assertNotEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertEquals( $dirty, $var['bork']['thing']['dirty'] );

		$var = SuperGlobals::get_sanitized_superglobal( 'GET' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertNotEquals( $dirty, $var['bork']['thing']['dirty'] );

		unset( $_GET['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_post_superglobal() {
		$dirty = '<script>alert("hello");</script>';
		$_POST['bork'] = [
			'thing' => [
				'dirty' => $dirty,
			],
		];

		$var = SuperGlobals::get_raw_superglobal( 'POST' );

		$this->assertNotEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertEquals( $dirty, $var['bork']['thing']['dirty'] );

		$var = SuperGlobals::get_sanitized_superglobal( 'POST' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertNotEquals( $dirty, $var['bork']['thing']['dirty'] );

		unset( $_POST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_request_superglobal() {
		$dirty = '<script>alert("hello");</script>';
		$_REQUEST['bork'] = [
			'thing' => [
				'dirty' => $dirty,
			],
		];

		$var = SuperGlobals::get_raw_superglobal( 'REQUEST' );

		$this->assertNotEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertEquals( $dirty, $var['bork']['thing']['dirty'] );

		$var = SuperGlobals::get_sanitized_superglobal( 'REQUEST' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertNotEquals( $dirty, $var['bork']['thing']['dirty'] );

		unset( $_REQUEST['bork'] );
	}

	/**
	 * @test
	 */
	public function it_should_get_server_superglobal() {
		$dirty = '<script>alert("hello");</script>';
		$_SERVER['bork'] = [
			'thing' => [
				'dirty' => $dirty,
			],
		];

		$var = SuperGlobals::get_raw_superglobal( 'SERVER' );

		$this->assertNotEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertEquals( $dirty, $var['bork']['thing']['dirty'] );

		$var = SuperGlobals::get_sanitized_superglobal( 'SERVER' );

		$this->assertEquals( '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt;', $var['bork']['thing']['dirty'] );
		$this->assertNotEquals( $dirty, $var['bork']['thing']['dirty'] );

		unset( $_SERVER['bork'] );
	}
}
