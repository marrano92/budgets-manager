<?php
/**
 * Autoloader
 *
 * The Autoloader does all the magic when it comes to include a file
 */

namespace Buma;

/**
 * Class Autoloader
 * @package Buma
 */
class Autoloader {

	/**
	 * Private class vars
	 *
	 * @var string $_fileExtension
	 * @var string $_namespace
	 * @var string $_includePath
	 * @var string $_namespaceSeparator
	 */
	private
		$_fileExtension = '.php',
		$_namespace,
		$_includePath,
		$_namespaceSeparator = '\\';

	/**
	 * Factory for the class autoloader
	 *
	 * @param string $ns
	 * @param string $includePath
	 *
	 * @return static
	 */
	public static function create( $ns = '', $includePath = '' ) {
		$obj = new static( $ns, $includePath );

		$obj->setNamespace( $ns );
		$obj->setIncludePath( $includePath );

		return $obj;
	}

	/**
	 * Sets the namespace of a class
	 *
	 * @param string $ns Namespace
	 */
	public function setNamespace( $ns ) {
		$this->_namespace = $ns;
	}


	/**
	 * Sets the namespace separator used by classes in the namespace of this class loader.
	 *
	 * @param string $sep The separator to use.
	 */
	public function setNamespaceSeparator( $sep ) {
		$this->_namespaceSeparator = $sep;
	}

	/**
	 * Gets the namespace seperator used by classes in the namespace of this class loader.
	 *
	 * @return string
	 */
	public function getNamespaceSeparator() {
		return $this->_namespaceSeparator;
	}

	/**
	 * Sets the base include path for all class files in the namespace of this class loader.
	 *
	 * @param string $includePath
	 */
	public function setIncludePath( $includePath ) {
		$this->_includePath = $includePath;
	}

	/**
	 * Gets the base include path for all class files in the namespace of this class loader.
	 *
	 * @return string $includePath
	 */
	public function getIncludePath() {
		return $this->_includePath;
	}

	/**
	 * Sets the file extension of class files in the namespace of this class loader.
	 *
	 * @param string $fileExtension
	 */
	public function setFileExtension( $fileExtension ) {
		$this->_fileExtension = $fileExtension;
	}

	/**
	 * Gets the file extension of class files in the namespace of this class loader.
	 *
	 * @return string $fileExtension
	 */
	public function getFileExtension() {
		return $this->_fileExtension;
	}

	/**
	 * Installs this class loader on the SPL autoload stack.
	 * @codeCoverageIgnore
	 */
	public function register() {
		spl_autoload_register( [ $this, 'loadClass' ] );
	}

	/**
	 * Uninstalls this class loader from the SPL autoloader stack.
	 * @codeCoverageIgnore
	 */
	public function unregister() {
		spl_autoload_unregister( [ $this, 'loadClass' ] );
	}

	/**
	 * Loads the given class or interface.
	 *
	 * @codeCoverageIgnore
	 * @param string $className The name of the class to load.
	 * @return void
	 */
	public function loadClass( $className ) {
		if ( null === $this->_namespace || $this->_namespace . $this->_namespaceSeparator === substr( $className, 0, strlen( $this->_namespace . $this->_namespaceSeparator ) ) ) {
			$fileName = '';
			if ( false !== ( $lastNsPos = strripos( $className, $this->_namespaceSeparator ) ) ) {
				$namespace = substr( $className, 0, $lastNsPos );
				$className = substr( $className, $lastNsPos + 1 );
				$fileName  = str_replace( $this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
			}
			$fileName .= str_replace( '_', DIRECTORY_SEPARATOR, $className ) . $this->_fileExtension;
			$includePath = ( $this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '' ) . $fileName;

			/** @noinspection PhpIncludeInspection */
			require $includePath;
		}
	}

}
