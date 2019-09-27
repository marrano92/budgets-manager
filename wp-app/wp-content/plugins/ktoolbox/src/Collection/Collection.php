<?php

namespace KToolbox\Collection;

use \Countable;
use \ArrayAccess;
use \JsonSerializable;
use \IteratorAggregate;
use \KToolbox\Collection\Jsonable;
use \KToolbox\Collection\Arrayable;
use \KToolbox\DataNavigator\DataNavigator;
use phpDocumentor\Reflection\Types\Callable_;
use function PHPSTORM_META\type;

/**
 * Class Collection
 * @package KToolbox\Collection
 */
class Collection implements IteratorAggregate, Countable, ArrayAccess, JsonSerializable, Jsonable, Arrayable {

	/**
	 * @var array
	 */
	protected $items;


	/**
	 * Collection constructor.
	 *
	 * @param mixed $items
	 */
	public function __construct( $items = [] ) {
		$this->items = $this->force_array_conversion( $items );
	}

	/**
	 * Adds an item to the collection. Does not
	 * override existing keys.
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function add( string $key, $value ): self {
		if ( ! isset( $this->items[ $key ] ) ) {
			$this->items[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Return all the items
	 *
	 * @return array
	 */
	public function all(): array {
		return $this->items;
	}

	/**
	 * Computes the difference between the collection and a data set
	 *
	 * @param array|Collection $data
	 * @param Callable $callback
	 *
	 * @return Collection
	 */
	public function diff( $data, Callable $callback = null ): Collection {
		if ( ! isset( $callback ) ) {
			$callback = function ( $a, $b ) {
				return serialize( $a ) <=> serialize( $b );
			};
		}

		if ( $data instanceof self ) {
			$data = $data->all();
		}

		return new self( array_udiff_assoc( $this->items, $data, $callback ) );
	}

	/**
	 * Apply a function to each element of the collection.
	 * You can pass either a Callable or a string which is
	 * the name of a collection method
	 *
	 * @param mixed $instruction
	 * @param array $arguments
	 *
	 * @return Collection
	 */
	public function each( $instruction, array $arguments = [] ): Collection {
		$result = null;

		if ( is_callable( $instruction ) ) {
			$this->apply_callable( $instruction );
		} elseif ( is_string( $instruction ) ) {
			$result = $this->apply_method( $instruction, $arguments );
		}

		return $result ?? $this;
	}

	/**
	 * Excludes the given collection keys
	 *
	 * @param array $exclude
	 *
	 * @return Collection
	 */
	public function except( array $exclude ): Collection {
		$data = $this->items;
		foreach ( $exclude as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				unset( $data[ $key ] );
			}
		}

		return new self( $data );
	}

	/**
	 * Apply array_filter to the collection
	 *
	 * @param Callable $function
	 * @param int $flag
	 *
	 * @return Collection
	 */
	public function filter( Callable $function, int $flag = 0 ): Collection {
		return new self( array_filter( $this->items, $function, $flag ) );
	}

	/**
	 * Flatten the items. Leaves the non-array items untouched
	 *
	 * @param bool $preserve_keys
	 *
	 * @return Collection
	 */
	public function flatten( bool $preserve_keys = false ): Collection {
		$result = [];
		$items  = $this->convert_leaves_to_arrays( $this->items );
		array_walk_recursive( $items, function ( $item, $key ) use ( &$result ) {
			if ( is_int( $key ) ) {
				$result[] = $item;
			} else {
				$result[ $key ] = $item;
			}
		} );
		if ( ! $preserve_keys ) {
			$result = array_values( $result );
		}

		return new self( $result );
	}

	/**
	 * Returns a subset of items, removing the required one given its key
	 *
	 * @param mixed $key
	 *
	 * @return Collection
	 */
	public function forget( $key ): Collection {
		if ( $this->offsetExists( $key ) ) {
			unset( $this->items[ $key ] );
		}

		return new self( $this->items );
	}

	/**
	 * Intersect the collection with the given array/Collection
	 * A callback may be provided
	 *
	 * @param mixed $data
	 * @param Callable $callback
	 *
	 * @return Collection
	 */
	public function intersect( $data, Callable $callback = null ): Collection {
		if ( $data instanceof self ) {
			$data = $data->all();
		}

		if ( isset( $callback ) ) {
			$new_data = array_uintersect_assoc( $this->items, $data, $callback );
		} else {
			$new_data = array_intersect( $this->items, $data );
		}

		return new self( $new_data );
	}

	/**
	 * Joins an object/array inside each element of the collection, if applicable
	 *
	 * @param string $prop
	 * @param mixed $data
	 *
	 * @return self
	 */
	public function join( string $prop, $data ): self {
		return $this->map( function ( $item ) use ( $prop, $data ) {
			if ( is_callable( $data ) ) {
				$data = $data( $item, $prop );
			}

			if ( is_object( $item ) ) {
				$item->$prop = $data;
			}
			if ( is_array( $item ) ) {
				$item[ $prop ] = $data;
			}

			return $item;
		} );
	}

	/**
	 * Joins an object/array inside each element of the collection, if applicable.
	 * A filtering is applied
	 *
	 * @param string $prop
	 * @param mixed $data
	 * @param array $filters
	 *
	 * @return Collection
	 */
	public function join_where( string $prop, $data, array $filters ): Collection {
		$filtered  = $this->where( ... $filters );
		$leftovers = $this->diff( $filtered );

		$transformed = $filtered->join( $prop, $data );

		return $leftovers->merge( $transformed );
	}

	/**
	 * Return a subset of elements, given a desired amount
	 *
	 * @param int $limit
	 *
	 * @return Collection
	 */
	public function limit( int $limit = null ): Collection {
		return is_null( $limit ) ? $this : new self( array_slice( $this->items, 0, $limit ) );
	}

	/**
	 * Apply array_map to the collection
	 *
	 * @param Callable $function
	 *
	 * @return Collection
	 */
	public function map( Callable $function ): Collection {
		return new self( array_map( $function, $this->items ) );
	}

	/**
	 * Merge the current items with new data
	 *
	 * @param array|Collection $data
	 *
	 * @return self
	 */
	public function merge( $data ): self {
		$data = $this->force_array_conversion( $data );

		$this->items = array_merge( $this->items, $data );

		return $this;
	}

	/**
	 * Keeps only the values of a given key set
	 *
	 * @param mixed $keep
	 * @param bool unique
	 *
	 * @return array
	 */
	public function only( $keep, bool $unique = false ): array {
		$data  = [];
		$cache = new \RecursiveIteratorIterator( new \RecursiveArrayIterator( $this->items ) );
		foreach ( $cache as $key => $value ) {
			if ( in_array( $key, $keep ) ) {
				$data[] = $value;
			}
		}

		if ( $unique ) {
			$data = array_unique( $data );
		}

		return array_values( $data );
	}

	/**
	 * Returns a subset of items, given the keys of the required ones
	 *
	 * @param array $keys
	 *
	 * @return Collection
	 */
	public function pick( array $keys ): Collection {
		return new self( wp_array_slice_assoc( $this->items, $keys ) );
	}

	/**
	 * Pluck a field
	 *
	 * @param string $field
	 * @param mixed $index_key
	 *
	 * @return array
	 *
	 * @codeCoverageIgnore
	 */
	public function pluck( $field, $index_key = null ): array {
		return wp_list_pluck( $this->items, $field, $index_key );
	}

	/**
	 * Add an item at the end of the collection
	 *
	 * @param mixed $item
	 *
	 * @return self
	 */
	public function push( $item ): self {
		$this->items[] = $item;

		return $this;
	}

	/**
	 * Return a subset of elements, given an amount to skip
	 *
	 * @param int $skip
	 *
	 * @return Collection
	 */
	public function skip( int $skip = 0 ): Collection {
		return new self( array_slice( $this->items, $skip ) );
	}

	/**
	 * Sort the collection
	 *
	 * @param string|Callable $order
	 * @param int $flags
	 *
	 * @return self
	 */
	public function sort( $order = null, int $flags = 0 ): self {
		if ( 'ASC' === $order ) {
			$this->collection_asort( $flags );
		} elseif ( 'DESC' === $order ) {
			$this->collection_arsort( $flags );
		} elseif ( is_callable( $order ) ) {
			$this->collection_usort( $order );
		}

		$this->reset_items_keys();

		return $this;
	}

	/**
	 * Return smaller subsets of data
	 *
	 * @param int $chunks_number
	 * @param bool $preserve_keys
	 *
	 * @return Collection
	 */
	public function split( int $chunks_number = 1, bool $preserve_keys = true ): Collection {
		return new self( array_chunk( $this->items, $chunks_number, $preserve_keys ) );
	}

	/**
	 * Returns a collection of unique items
	 *
	 * @return Collection
	 */
	public function unique(): Collection {
		return new self( array_unique( $this->items ) );
	}

	/**
	 * Returns a collection given the search query
	 *
	 * @param mixed
	 *
	 * @return Collection
	 */
	public function where(): Collection {
		return new self( ( new \KToolbox\DataFilterer\DataFilterer( $this->items, func_get_args() ) )->execute() );
	}

	/**
	 * Returns a subset of the collection given the namespace to filter
	 *
	 * @param string $namespace
	 *
	 * @return Collection
	 */
	public function where_instance_of( string $namespace ): Collection {
		return $this->filter( function ( $item ) use ( $namespace ) {
			return $item instanceof $namespace;
		} );
	}

	/**
	 * IteratorAggregate - getIterator
	 * Allows to treat the collection as an array.
	 * This object is foreach-able
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator(): \ArrayIterator {
		return new \ArrayIterator( $this->items );
	}

	/**
	 * Countable - count()
	 * Counts the items
	 *
	 * @return int
	 */
	public function count(): int {
		return count( $this->items );
	}

	/**
	 * ArrayAccess - Determine if an item exists given the key.
	 *
	 * @param mixed $key
	 *
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->items );
	}

	/**
	 * ArrayAccess - Get an item at a given the key.
	 *
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->items[ $key ];
	}

	/**
	 * ArrayAccess - Set the item at a given the key.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 *
	 * @return void
	 */
	public function offsetSet( $key, $value ) {
		if ( is_null( $key ) ) {
			$this->items[] = $value;
		} else {
			$this->items[ $key ] = $value;
		}
	}

	/**
	 * ArrayAccess - Unset the item at a given the key.
	 *
	 * @param string $key
	 *
	 * @return void
	 */
	public function offsetUnset( $key ) {
		unset( $this->items[ $key ] );
	}

	/**
	 * JsonSerializable - Allow to serialize the items
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return array_map( function ( $value ) {
			if ( $value instanceof JsonSerializable ) {
				return $value->jsonSerialize();
			} elseif ( $value instanceof Jsonable ) {
				return json_decode( $value->to_json(), true );
			} elseif ( $value instanceof Arrayable ) {
				return $value->to_array();
			}

			return $value;
		}, $this->items );
	}

	/**
	 * Jsonable - Convert to json
	 *
	 * @param int $options
	 *
	 * @return string
	 */
	public function to_json( int $options = 0 ) {
		return json_encode( $this->jsonSerialize(), $options );
	}

	/**
	 * Arrayable - convert to array
	 *
	 * @return array
	 */
	public function to_array() {
		return array_map( function ( $value ) {
			return $value instanceof Arrayable ? $value->to_array() : $value;
		}, $this->items );
	}

	/**
	 * Dynamically access collection.
	 *
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->items[ $key ] ?? null;
	}

	/**
	 * Dynamically set collection data.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 *
	 * @return self
	 */
	public function __set( $key, $value ): self {
		$this->add( $key, $value );

		return $this;
	}

	/**
	 * Dynamically unset collection data.
	 *
	 * @param mixed $key
	 *
	 * @return self
	 */
	public function __unset( $key ): self {
		$this->forget( $key );

		return $this;
	}

	/**
	 * Tries to get an array from the provided value.
	 * If it fails, tries to typecast an array out of it.
	 *
	 * @param mixed $items
	 *
	 * @return array
	 */
	protected function force_array_conversion( $items ) {
		$items = $this->interfaces_to_array_conversion_attempt( $items );

		return is_array( $items ) ? $items : (array) $items;
	}

	/**
	 * Tries to get an array from the provided value.
	 * It will convert standard classes to array, but leave the other objects alone
	 *
	 * @param mixed $items
	 *
	 * @return mixed
	 */
	protected function try_to_get_array( $items ) {
		$items = $this->interfaces_to_array_conversion_attempt( $items );
		if ( $items instanceof \stdClass ) {
			$items = (array) $items;
		}

		return $items;
	}

	/**
	 * Attempt to convert to array an element, it only checks the supported interfaces
	 *
	 * @param mixed $items
	 *
	 * @return mixed
	 */
	protected function interfaces_to_array_conversion_attempt( $items ) {
		if ( is_array( $items ) ) {
			return $items;
		} elseif ( $items instanceof self ) {
			return $items->all();
		} elseif ( $items instanceof Arrayable ) {
			return $items->to_array();
		} elseif ( $items instanceof Jsonable ) {
			return json_decode( $items->to_json(), true );
		} elseif ( $items instanceof JsonSerializable ) {
			return $items->jsonSerialize();
		} elseif ( $items instanceof IteratorAggregate ) {
			return iterator_to_array( $items );
		}

		return $items;
	}

	/**
	 * Recursively inspects a multidimensional array, search for leaves which can be
	 * transformed in arrays and processes them
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	protected function convert_leaves_to_arrays( array $items ): array {
		while ( true ) {
			$done = true;
			foreach ( $items as $key => $value ) {
				$value = $this->try_to_get_array( $value );
				if ( is_array( $value ) ) {
					$converted = $this->convert_leaves_to_arrays( $value );
					if ( $value !== $converted ) {
						$done = false;
					}
					$items[ $key ] = $converted;
				}
			}
			if ( $done ) {
				break;
			}
		}

		return $items;
	}

	/**
	 * Apply a callable to each element of the collection
	 *
	 * @param Callable $callable
	 *
	 * @return void
	 */
	protected function apply_callable( Callable $callable ) {
		foreach ( $this->items as $key => $item ) {
			$this->items[ $key ] = $callable( $item );
		}
	}

	/**
	 * Tries to apply a method on a collection ite, if possible.
	 * Otherwise it will look for the method in the collection itself
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return Collection
	 */
	protected function apply_method( string $method, array $arguments = [] ): Collection {
		return method_exists( $this, $method ) ? $this->apply_collection_method( $method, $arguments ) : $this->maybe_apply_item_method( $method, $arguments );
	}

	/**
	 * Apply a default collection method to each item
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return Collection
	 */
	protected function apply_collection_method( string $method, array $arguments = [] ): Collection {
		$new_collection = new self();
		foreach ( $this->items as $item ) {
			$typecast         = is_object( $item ) && ! ( $item instanceof self );
			$proxy_collection = new self( $item );
			$transformed      = $this->force_array_conversion( $proxy_collection->$method( ...$arguments ) );
			if ( is_array( $transformed ) && $typecast ) {
				$transformed = (object) $transformed;
			}
			$new_collection->push( $transformed );
		}

		return $new_collection;
	}

	/**
	 * Tries to apply an item method to each item of the collection
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return Collection
	 */
	protected function maybe_apply_item_method( string $method, array $arguments = [] ): Collection {
		$new_collection = new self();
		foreach ( $this->items as $item ) {
			$new_collection->push( is_object( $item ) && method_exists( $item, $method ) ? $item->$method( ...$arguments ) : $item );
		}

		return $new_collection;
	}

	/**
	 * asort wrapper
	 *
	 * @param int $flags
	 */
	protected function collection_asort( int $flags ) {
		asort( $this->items, $flags );
	}

	/**
	 * arsort wrapper
	 *
	 * @param int $flags
	 */
	protected function collection_arsort( int $flags ) {
		arsort( $this->items, $flags );
	}

	/**
	 * usort wrapper
	 *
	 * @param Callable $callback
	 */
	protected function collection_usort( Callable $callback ) {
		usort( $this->items, $callback );
	}

	/**
	 * Resets the item keys
	 */
	protected function reset_items_keys() {
		$this->items = array_merge( $this->items, [] );
	}

	/**
	 * Get a list of supported arrayable classes
	 *
	 * @return array
	 */
	protected function get_supported_interfaces(): array {
		return [
			get_class( $this ),
			Arrayable::class,
			Jsonable::class,
			JsonSerializable::class,
			IteratorAggregate::class,
		];
	}

}