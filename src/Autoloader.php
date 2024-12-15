<?php
/**
 * Main Autoloader Manager Class
 *
 * @package     ArrayPress/Autoloader
 * @copyright   Copyright (c) 2024, ArrayPress Limited
 * @license     GPL2+
 * @version     1.0.0
 * @author      David Sherlock
 */

declare( strict_types=1 );

namespace ArrayPress;

/**
 * Class AutoloaderManager
 *
 * Manages versioned autoloading for multiple ArrayPress libraries
 */
final class AutoloaderManager {

	/**
	 * Registered namespaces and their versions
	 *
	 * @var array<string, array{version: string, baseDir: string}>
	 */
	private static array $registered = [];

	/**
	 * Register a namespace with version checking
	 *
	 * @param string $namespace The namespace to register (e.g., 'ArrayPress\\Geocoding\\')
	 * @param string $version   The version of the library
	 * @param string $baseDir   The base directory for the library's classes
	 *
	 * @return void
	 */
	public static function register( string $namespace, string $version, string $baseDir ): void {
		// Normalize namespace
		$namespace = trim( $namespace, '\\' ) . '\\';
		$baseDir   = rtrim( $baseDir, '/\\' ) . DIRECTORY_SEPARATOR;

		// Check if we should register this version
		if ( ! self::should_register( $namespace, $version ) ) {
			return;
		}

		// Store registration info
		self::$registered[ $namespace ] = [
			'version' => $version,
			'baseDir' => $baseDir
		];

		// Register autoloader
		spl_autoload_register( function ( $class ) use ( $namespace, $baseDir ) {
			self::load_class( $class, $namespace, $baseDir );
		} );
	}

	/**
	 * Check if we should register this version
	 *
	 * @param string $namespace The namespace to check
	 * @param string $version   The version to check
	 *
	 * @return bool
	 */
	private static function should_register( string $namespace, string $version ): bool {
		if ( ! isset( self::$registered[ $namespace ] ) ) {
			return true;
		}

		return version_compare( $version, self::$registered[ $namespace ]['version'], '>' );
	}

	/**
	 * Load a class file
	 *
	 * @param string $class     The class to load
	 * @param string $namespace The namespace prefix
	 * @param string $baseDir   The base directory
	 *
	 * @return void
	 */
	private static function load_class( string $class, string $namespace, string $baseDir ): void {
		// Check if class uses this namespace
		if ( strpos( $class, $namespace ) !== 0 ) {
			return;
		}

		// Get the relative class name
		$relative_class = substr( $class, strlen( $namespace ) );

		// Convert namespace separators to directory separators
		$file = $baseDir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		// Load the file if it exists
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Get all registered namespaces and their versions
	 *
	 * @return array<string, string>
	 */
	public static function get_registered(): array {
		$registered = [];
		foreach ( self::$registered as $namespace => $data ) {
			$registered[ $namespace ] = $data['version'];
		}

		return $registered;
	}

	/**
	 * Check if a namespace is registered
	 *
	 * @param string $namespace The namespace to check
	 *
	 * @return bool
	 */
	public static function is_registered( string $namespace ): bool {
		return isset( self::$registered[ trim( $namespace, '\\' ) . '\\' ] );
	}

	/**
	 * Get version of a registered namespace
	 *
	 * @param string $namespace The namespace to check
	 *
	 * @return string|null
	 */
	public static function get_version( string $namespace ): ?string {
		$namespace = trim( $namespace, '\\' ) . '\\';

		return self::$registered[ $namespace ]['version'] ?? null;
	}

}