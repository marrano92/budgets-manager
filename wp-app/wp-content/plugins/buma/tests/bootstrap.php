<?php
/**
 * Created by PhpStorm.
 * User: riccardocedrola
 * Date: 18/04/17
 * Time: 11.03
 */

namespace Buma\Tests;

use \PHPUnit\Framework\TestCase;

// First we need to load the composer autoloader so we can use WP Mock
require_once __DIR__ . '/../vendor/autoload.php';

// Now call the bootstrap method of WP Mock
\WP_Mock::bootstrap();
\WP_Mock::passthruFunction( 'register_deactivation_hook' );
\WP_Mock::passthruFunction( 'wp_next_scheduled' );
\WP_Mock::passthruFunction( 'wp_schedule_event' );
\WP_Mock::passthruFunction( 'wp_clear_scheduled_hook' );
\WP_Mock::passthruFunction( 'has_action' );

/**
 * Now we include any plugin files that we need to be able to run the tests. This
 * should be files that define the functions and classes you're going to test.
 */
require_once __DIR__ . '/../buma.php';
require_once __DIR__ . '/HandleProtected.php';
require_once __DIR__ . '/WpdbMock.php';

define( 'MINUTE_IN_SECONDS', 60 );
//define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS ); @todo remove the define form buma.php since it's defined by wordpress
define( 'DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS );
define( 'WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS );
define( 'MONTH_IN_SECONDS', 30 * DAY_IN_SECONDS );
define( 'YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS );
define( 'DOMAIN_CURRENT_SITE', '127.0.0.2' );

abstract class BUMA_Framework_TestCase extends TestCase {

	//setting mocks in the setUp might become a problem as times goes.
	public function setUp():void {
		parent::setUp();
		\WP_Mock::setUp();

		\WP_Mock::passthruFunction( 'home_url' );
		\WP_Mock::passthruFunction( 'get_query_var' );
		\WP_Mock::passthruFunction( 'sanitize_title' );
		\WP_Mock::passthruFunction( 'number_format_i18n' );
	}

	public function get_options( array $values = [] ) {
		$mock = \Mockery::mock( \Buma\PluginOptions::class );

		if ( count( $values ) > 0 ) {
			foreach ( $values as $key => $value ) {
				$mock->$key = $value;
			}
		}

		$mock->shouldReceive( 'get' )->andReturn( $values )->zeroOrMoreTimes();

		return $mock;
	}

	public function tearDown():void {
		parent::tearDown();
		\WP_Mock::tearDown();
	}
}
