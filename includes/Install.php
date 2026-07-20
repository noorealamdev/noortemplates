<?php
/**
 * Install, upgrade and deactivation routines.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates;

use NoorTemplates\Templates\Repository;
use NoorTemplates\Layouts\Split_Test_Stats;

defined( 'ABSPATH' ) || exit;

/**
 * Tracks the installed version and runs migrations between releases.
 */
class Install {

	/**
	 * Option storing the version the database was last migrated to.
	 */
	const VERSION_OPTION = 'noortemplates_version';

	/**
	 * Hooks the upgrade check.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_upgrade' ), 5 );
	}

	/**
	 * Runs on plugin activation.
	 *
	 * @return void
	 */
	public static function activate() {
		// Unconditional: get_migrations() never runs on a fresh install
		// (maybe_upgrade() only applies migrations when upgrading FROM a
		// known previous version), so the table must also be created here
		// directly. dbDelta() is safe to call repeatedly. The table itself
		// isn't Pro-gated — Split_Test's actual behavior is (see
		// Licensing\Gate) — so it's fine to always exist.
		Split_Test_Stats::create_table();

		self::maybe_upgrade();
	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * @return void
	 */
	public static function deactivate() {
		Repository::instance()->flush_caches();
	}

	/**
	 * Applies pending migrations when the plugin version changed.
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		$installed = (string) get_option( self::VERSION_OPTION, '' );

		if ( NOORTEMPLATES_VERSION === $installed ) {
			return;
		}

		foreach ( self::get_migrations() as $version => $callback ) {
			if ( '' !== $installed && version_compare( $installed, $version, '<' ) ) {
				call_user_func( $callback );
			}
		}

		update_option( self::VERSION_OPTION, NOORTEMPLATES_VERSION );

		/**
		 * Fires after the plugin migrated to a new version.
		 *
		 * @param string $installed Previous version, empty on first install.
		 */
		do_action( 'noortemplates/upgraded', $installed );
	}

	/**
	 * Returns the migration callbacks, keyed by the version introducing them.
	 *
	 * A migration runs once when a site upgrades from a version below its
	 * key. Example: '1.1.0' => array( __CLASS__, 'migrate_110' ).
	 *
	 * @return array<string, callable>
	 */
	private static function get_migrations() {
		return array(
			'1.1.0' => array( __CLASS__, 'migrate_110' ),
		);
	}

	/**
	 * Creates the split-test counters table for sites upgrading in place
	 * (e.g. via auto-update, which never re-fires register_activation_hook).
	 *
	 * @return void
	 */
	private static function migrate_110() {
		Split_Test_Stats::create_table();
	}
}
