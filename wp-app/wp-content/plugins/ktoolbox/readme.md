# Index
1. [FilterInput](#filter-input)
2. [MetaBox](#metabox)
3. [MultipartFormData](#multi-part-form-data)
4. [Url](#url)
5. [Collection](#collection)
6. [DataNavigator](#data-navigator)
7. [DataFilterer](#data-filterer)

# Project description
This is a set of utilities you can use to develop your plugins. It does nothing by himself and it does not registers 
options, nor adds filters.

## <a name="filter-input">FilterInput</a>
This class is a wrapper around PHP's [filter functions](https://www.php.net/manual/en/ref.filter.php) and will help you
get data form the super globals. You can get an instance of this class by providing the input type and the requested 
variable name:
```
// Supported types: INPUT_POST, INPUT_GET, REQUEST
$filter_input = new \KToolbox\FilterInput( INPUT_POST, 'var_name' );
```

You can set flags and filters which will be used by the underlying function, as wel as getting them. When retrieving the set, you 
can optionally specify a `defualt` value to be returned in your set:
```
$filter_input = new \KToolbox\FilterInput( INPUT_POST, 'var_name' );

$filter_input->set_filter( FILTER_DEFAULT );        // FILTER_DEFAULT = 516
$filter_input->set_flags( FILTER_FLAG_STRIP_LOW );  // FILTER_FLAG_STRIP_LOW = 4

// When retrieving the options, we will get an array containing your custom value if sepcified, and the flags.
// The filters won't be returned.

$options = $filter_input->get_options( 'my_custom_option' );
// [ 'options' => 'my_default_option', 'flags' => 4 ]

$options = $filter_input->get_options();
// [ 'flags' => 4 ]
```

Once you've prepared your data filters and flags, you can get the value. You can specify a fallback value if the requested
input is not there:
```
$filter_input = new \KToolbox\FilterInput( INPUT_POST, 'var_which_exists' );
$filter_input->get( false );    // 'value_which_exists'

$filter_input = new \KToolbox\FilterInput( INPUT_POST, 'var_which_does_NOT_exists' );
$filter_input->get( false );    // false
$filter_input->get();           // null
```

If the variable you seek is an array, you can use the `get_arr` method instead. You can pass as argument an array with 
filters and flags which will be applied to any variable inside the desired one:
```
$_POST = [
    'my_var' => [
        'var1' => [ 'VALUE1', 1 ],
        'var2' => [ 'VALUE2"', '`a_value' ],
    ];
];

$options = [
    'var1' => [ 'filters' => FILTER_VALIDATE_INT, 'flags' => FILTER_FORCE_ARRAY ],
    'var2' => [ 'filters' => FILTER_SANITIZE_MAGIC_QUOTES, 'flags' => FILTER_FLAG_STRIP_BACKTICK ],
];

$filter_input = new \KToolbox\FilterInput( INPUT_POST, 'my_var' );
$filter_input->get_arr( $options );
// [ 'var1 => [ false, 1 ], 'var2' => [ 'VALUE2\"', 'a_value' ]
```

You can compare a variable to a given value:
```
// $_POST['my_var'] = 1;
$filter_input = new \KToolbox\FilterInput( INPUT_POST, 'my_var' );
$filter_input->equals( 1 ); // true
```

Finally, you can get the current request uri with a handy static method:
```
// We don't really care passing a variable name here, an empty string will suffice
$uri = \KToolbox\FilterInput::get_request_uri();
// /my/current/page
```

## <a name="metabox">MetaBox</a>
This class helps you create custom fields for your custom post types or similar kind of posts. Given an array 
representing the post with his fields, you can easily populate custom WP pages:
```
$options = [
    'my_post_type' => [
        'id'       => 'my_post_type-meta-box',
        'title'    => 'My title',
        'page'     => 'my_page',
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => [
            [
                'name' => 'field name',
                'id'   => 'field-id',
                'type' => 'textarea',
                'std'  => '',
                'desc' => ''
            ],
        ],
    ]
];

$metabox = Metabox::create();
$metabox->add_config( $options );
```

WordPress will manage everything by his own, but if you are in need to further control your boxes you can do so:
```
$metabox->has_config( 'my_post_type' ); // true
$metabox->show_box();                   // Will print boxes HTML for the current post

// The following methods require a post id
$metabox->save_data( 1 );               // Saves the meta of the posts with ID 1
$metabox->get_fields( 1 );              // Gets an associative array [ 'field' => 'value' ] containing the meta fields 
                                        // of the post with ID 1

```

## <a name="multi-part-form-data">MultiPartFormData</a>
This class will help you creating the data you need to create a multipart/form-data request. We have a set of pretty 
straightforward methods:
```
$form = new \KToolbox\MultipartFormData();
$form->get_boundary();              // Returns 24
$form->create_content_type();       // Returns 'multipart/form-data; boundary=24'
$form->create_header();             // Returns [ 'Content-Type' => 'multipart/form-data; boundary=24' ]
```

As for the body, you can pass in an associative array of inputs and an associative array of file paths which will be 
read and appended to the form. Both of this parameters are not mandatory, and if specified should be arrays:
```
$fields = [ 'field' => 'value' ];
$files  = [ 'filename' => '/path/to/file' ];

$form = new \KToolbox\MultipartFormData();
$form->create_body( $fields, $files );  // Returns a string which is the body of the request itself.
```

## <a name="url">Url</a>
This class has only one function, which returns the url query. In the construct you can pass `true` as second parameter 
to get the given URL/URI. Otherwise you will only analyze the provided string.
```
$url = new \KToolbox\Url( '/my/page?arg=value&foo=bar', true );
$url->parse_param();    // Returns 'arg=value&foo=bar'
```

## <a name="collection">Collection</a>
This class allows you to group several items and perform a series of operations on each of them or on the list itself.
It accepts an arguments which can be:
* an array
* a `\KToolbox\Collection\Collection` instance
* an `\KToolbox\Collection\Arrayable` instance
* a `\KToolbox\Collection\Jsonable` instance
* a `\JsonSerializable` instance
* an `\IteratorAggregate` instance

If any of this checks fails, the class will attempt to convert to array the given argument.

### Collection interfaces
While [JsonSerializable](https://www.php.net/manual/en/class.jsonserializable.php) and 
[IteratorAggregate](https://www.php.net/manual/en/class.iteratoraggregate.php) are standard PHP interfaces, this package 
gives you the possibility to extend `\KToolbox\Collection\Arrayable` and `\KToolbox\Collection\Jsonable`, which will
make you implement the `to_array` and `to_json` methods respectively:
```
// The following classes are examples of a really basic interface implementation

class ArrayableClass extends \KToolbox\Collection\Arrayable {
    /**
     * @var \stdClass
     */
    public $item;
    
    public function to_array() {
        return (array) $item;
    }
}

class JsonableClass extends \KToolbox\Collection\Jsonable {
    /**
     * @var array
     */
    public $items;
    
    public function to_array() {
        return json_encode( $items );
    }
}
```

### Available methods
#### add
Add a method to the collection as a key-value pair:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1' ] );
$collection->add( [ 'key2' => 'value2' ] );     // [ 'key1' => 'value1', 'key2' => 'value2' ]
```

### all
Get all the elements in the collection:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1' ] );
$items = $collection->all(); // [ 'key1' => 'value1' ]
```

### count
You can count the element which are registered in the collection:
```
$collection = new \KToolbox\Collection\Collection( [ 'a', 'b', 'c' ] );
$count = $collection->count();  // 3
```

### diff
Returns the difference between the Collection items and a given array or another collection instance. This method can
perform accurate comparison between objects too, but if you want to provide a custom value comparison criteria, you can 
pass a closure as second parameter:
```
$collection = new \KToolbox\Collection\Collection( [ 'a', 'b', 'c' ] );
$filtered = $collection->diff( [ 'b', 'c' ] );      // [ 'a' ]

/**
 * Here we pass a clallback which will allow us to keep in the collection the element in the first data set providing
 * that its value is lower than the one in the second dataset. Remember that tis is a diff, so only values from the 
 * first dataset will be preserved.
 */
$collection = new \KToolbox\Collection\Collection( [ 0, 1, 2 ] );
$filtered = $collection->diff( [ -1, 2, 3 ], function( $a, $b ) {
    return $a < $b;
} ); // [ 1, 2 ]
```
___
Please note that, in case of indexed arrays, the objects enumeration may be different from what you expect since this 
function does NOT reset the array keys. In this case you may want to perform an `array_values()` on the resulting 
subset.
___

### each
You can apply a collection method to each of the collection element, by passing the method name and an array with the 
method arguments:
```
$data = [
    [ 'key1' => 'value1' ],
    [ 'key2' => 'value2' ],
];
$collection = new \KToolbox\Collection\Collection( $data );
$collection->each( 'add', [ 'key3', 'value3' ] );
$result = $collection->all();
/**
 * Will return:
 *  [
 *      [ 'key1' => 'value1', 'key3' => 'value3' ],
 *      [ 'key2' => 'value2', 'key3' => 'value3' ],
 *  ]
 */
 ```
 You can also pass a closure which needs to accept an item:
 ```
$data = [
    [ 'key1' => 'value1' ],
    [ 'key2' => 'value2' ],
];
$collection = new \KToolbox\Collection\Collection( $data );
$collection->each( function( $item ) {
   $item['key3'] = 'value3';
   
   return $item;
} );
$result = $collection->all();
/**
 * Will return:
 *  [
 *      [ 'key1' => 'value1', 'key3' => 'value3' ],
 *      [ 'key2' => 'value2', 'key3' => 'value3' ],
 *  ]
 */
```
If you have a collection of classes, you can also specify a method name available within those classes. Assume we have a 
"Test" object which accepts an integer in the constructor and has a "sum" method which adds the given number:
```
$data = [
    new Test( 2 ),
    new Test( 4 ),
];
$collection = new \KToolbox\Collection\Collection( $data );
$collection->each( 'sum', [ 2 ] );
$result = $collection->all();   // [ 4, 6 ]
```
When using the `each` method you should keep in mind that:
* if in the Collection there are other instances of `\Ktoolbox\Collection\Collection`, those will be converted to arrays;
* if a method name is provided, the collection is checked before the element itself, so in case of conflicting method 
names the Collection instance will prevail. If that's your case, you can pass in a closure instead of the method name;
* if an element is an instance of `\stdClass` it will be converted to array, the operation will be performed and it will
be converted back into an object.

### except
Unset the given keys from the Collection:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3' ] );
$result = $collection->except([ 'key1', 'key2' ]);
$result->all();     // [ 'key3' => 'value3' ] 
```

### filter
Applies `array_filter` to the collection. You can optionally pass flags as second parameter:
```
$collection = new \KToolbox\Collection\Collection( [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4 ] );
$result = $collection->filter( function ( $value, $key ) {
    return $value < 4 && $key !== 'c';
}, ARRAY_FILTER_USE_BOTH );
$result->all();     // [ 'a' => 1, 'b' => 2 ]
```

### flatten
Reduces the collection to a mono-dimensional array. It will try to convert to array any supported interface (i.e an 
object implementing `\KToolbox\Collection\Arrayable`). Array elements with a numeric (default) key will be duplicated,
while array elements which have a custom key will be replaced by the most nested one. You can optionally pass `true` as 
a parameter to preserve the keys (if the keys themselves were not default integers):
```
$data = [
    [ 'key1' => [ 'subkey1' => 'subvalue1' ] ],
    [ 'key2' => [ 'subkey2' => [ 'subkey3' => 'subvalue3' ] ] ],
];
$collection = new \KToolbox\Collection\Collection( $data );
$result = $collection->flatten()->all();                    // [ 'subvalue1', 'subvalue3' ]
$result_with_keys = $collection->flatten( true )->all();    // [ 'subkey1' => 'subvalue1', 'subkey3' => 'subvalue3' ]
```

### forget
Removes a given key from the collection, if it exists:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1', 'key2' => 'value2' ] );
$collection->forget( 'key1' )->all();   // [ 'key2' => 'value2' ]
```

### intersect
Intersects a given array or collection with the current items:
```
// Simple intersection
$collection_1 = new \KToolbox\Collection\Collection( [ 'val1' => 1, 'val2' => 2 ] );
$collection_2 = new \KToolbox\Collection\Collection( [ 'val2' => 2, 'val3' => 3 ] );
$result = $collection_1->intersect( $collection_2 )->all();      // [ 'val2' => 2 ]
```
You can pass a closure as second parameter, which will be used for values comparison. If the closure returns false, the 
element from the first collection will be preserved, otherwise we would have an empty array as a result:
```
$collection_3 = new \KToolbox\Collection\Collection( [ 'val1' => 1, 'val2' => 2 ] );
$collection_4 = new \KToolbox\Collection\Collection( [ 'val2' => 3, 'val3' => 3 ] );
$result = $collection_1->intersect( $collection_2, function( $item_from_collection_3, $item_from_collection_4 ) {
    return $item_from_collection_3 > $item_from_collection_4;
} )->all();                                                     // [ 'val2' => 2 ]
```

### join
Adds a given key/value pair to each element of the collection:
```
$data = [
    'arr' => [ 'val' => 'aaa' ],
    'obj' => (object) [ 'val' => 'bbb' ],
];

$collection = new \KToolbox\Collection\Collection( $data );
$collection->join( 'added_value', 'added' )->all();
/**
 * [
 *  'arr' => [ 'val' => 'aaa', 'added_value' => 'added' ],
 *  'obj' => (object) [ 'val' => 'bbb', 'added_value' => 'added' ],
 * ]
 */
```
Instead of a value, you can provide a closure which will accept the collection item and the prop name as parameters:
```
$data = [
    [ 'value' => 1 ],
    [ 'value' => 2 ],
];
$collection = new \KToolbox\Collection\Collection( $data );
$collection->join( 'added_value', function( $item, $prop ) {
    return $item['value'] * 2;
} )->all();
/**
 * [
 *     [ 'value' => 1, 'added_value' => 2 ],
  *    [ 'value' => 2, 'added_value' => 4 ],
 * ]
 */
```

### where
You can query your collection and return a subset which matches your request. A simple example would be:
```
$data = [
    [ 'value' => 1 ],
    [ 'value' => 2 ],
];
$collection = new \KToolbox\Collection\Collection( $data );
$result = $collection->where( [ 'value', '=', 2 ] )->all();     // [ [ 'value' => 2 ] ]
```
This method uses the `\KToolbox\DataNavigator\Datanavigator` class in order to filter your data. Consult its section of 
the documentation for more advanced usage examples.

### join_where
You can perform a join on a specific subset of items. You'll need to provide the new data name, its value and the query 
which will filter your data:
```
$data = [
    [ 'val1' => 1 ],
    [ 'val1' => 2 ],
];
$collection = new \KToolbox\Collection\Collection( $data );
$result = $collection->where( 'added', 'value', [ 'value', '=', 2 ] )->all();
/**
 * Will return:
 * [
 *      [ 'val1' => 1 ],
 *      [ 'val1' => 2, 'added' => 'value' ],
 * ]
 */
```

### limit
You can keep the first X element of the collection, and exclude the rest:
```
$collection = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );
$result = $collection->limit( 2 )->all();   // [ 1, 2 ]
```

### map
You can apply a closure to each element of the array Unlike `each`, this will perform an `array_map` on the collection
elements:
```
$collection = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );
$result = $collection->map( function( $item ) {
    return $item * 2;
} )->all();         // [ 2, 4, 6 ]
```

### merge
You can merge another array/object/Collection inside the current one. This method will try to convert the collection 
interfaces into an array. Please note that, if you provide some custom object/class, it will be type-casted as an array:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1, 'key2' => 'value2' ] );
$collection->merge( [ 'key2' => 'value2', 'key3' => 'value3' ] )->all();
// [ 'key1' => 'value1, 'key2' => 'value2', 'key3' => 'value3' ]
```
If you pass `true` as second parameter, the collection will only contain unique values.

### only
Given a set of data, you can create a subset which contains only the given key values and excludes the rest. If 
you provide a key which is not contained in the collection, you won't find it in the result:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1, 'key2' => 'value2' ] );
$result = $collection->only( [ 'key1', 'key3' ] );      // [ 'value1' ]
```
You can optionally pass `true` as second parameter to make the elements in the result unique.

### pick
Create a new collection which contains only the elements with the specified keys:
```
$collection = new \KToolbox\Collection\Collection( [ 'key1' => 'value1, 'key2' => 'value2', 'key3' => 'value3' ] );
$result = $collection->pick( [ 'key1', 'key3' ] )->all();      // [ 'key1' => 'value1, 'key3' => 'value3' ]
```

### pluck
Returns a subset with the specified keys, similar to `only`. You can optionally pass a key name, which value will be 
used as the new key:
```
$data = [
    [ 'id' => 'a', 'value' => 'val_a' ],
    [ 'id' => 'b', 'value' => 'val_b' ]
];
$collection = new \KToolbox\Collection\Collection( $data );
$result = $collection->pluck( 'value' );            // [ 'val_a', 'val_b' ]
$result = $collection->pluck( 'value', 'id );       // [ 'a' => val_a', 'b' => val_b' ]
```

### push
Add an item at the end of the collection:
```
$collection = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );
$collection->push( 0 );     // Items are now. [ 1, 2, 3, 0 ]
```

### limit
You can remove the first X element of the collection, and keep the rest:
```
$collection = new \KToolbox\Collection\Collection( [ 1, 2, 3 ] );
$result = $collection->skip( 2 )->all();   // [ 3 ]
```

### sort
Sort the items of your collection in `ASC` or `DESC` order. This will use `asort()` or `arsort()`, so you may pass flags
as second parameter:
```
$collection = new \KToolbox\Collection\Collection( [ '1', '2', '3' ] );
$collection->sort( 'DESC', SORT_NUMERIC );  // [ '3', '2', '1' ]
```
You can also provide a callback, but in this case no flag will be used:
```
$data = [
    [ 'val' => 1000 ],
    [ 'val' => 100 ],
    [ 'val' => 10 ],
    [ 'val' => 1 ],
];

$collection = new \KToolbox\Collection\Collection( $data );
$collection->sort( function ( $a, $b ) {
    return $a['val'] <=> $b['val'];
} );
/**
 * Items are now:
 * [
 *    [ 'val' => 1 ],
 *    [ 'val' => 10 ],
 *    [ 'val' => 100 ],
 *    [ 'val' => 1000 ],
  * ];
```

### split
You can split your items in chunks by providing the desired chunks number. Keys will be preserved, but if you don't 
wish so you may pass `false` as second parameter:
```
$collection = new \KToolbox\Collection\Collection( [ 'value1' => 1, 'value2' => 2, 'value3' => 3 ] );
$result = $collection->split( 2, false )->all();    // [ [ 1, 2 ], [ 3 ] ]
```

### unique
You can remove duplicate elements onside of your collection:
```
$collection = new \KToolbox\Collection\Collection( [ 1, 1, 2, 2, 3, 3 ] );
$result = $collection->unique()->all();    // [ 1, 2, 3 ]
```

### where_instance_of
You can keep only the collection elements which are instances of a given namespace:
```
$collection = new \KToolbox\Collection\Collection( [ [], new \stdClass() ] );
$result = $collection->where_instance_of( \stdClass::class )->all();    // This will only contain the empty \stdClass
```


## <a name="data-navigator">DataNavigator</a>
This class allows you to navigate an array/object and grab a specific nested value.It will ingest the data in the 
constructor. The path is represented by a string and obtained by the `get` method, and properties should be chained 
with a `.` or a `->`. You may specify a second parameter which will be returned if the provided path is invalid or if 
the requested value is not found:
```
$data     = [
    'elem1' => (object) [ 'key' => 'value1' ],
    'elem2' => [ 'value2' ],
];
$navigtor = new \KToolbox\DataNavigator\DataNavigator( $data );

$result = $navigator->get( 'elem1' );              // Returns (object) [ 'key' => 'value1' ]
$result = $navigator->get( 'elem1.key' );          // Returns 'value1'
$result = $navigator->get( 'elem2.0' );            // Returns 'value2'
$result = $navigator->get( 'elem5.5', false );     // Returns false
```
In the specific case that an requested element is an instance of `\KToolbox\Collection\Collection`, you can use `*` to 
grab the collection items:
```
$data     = [
    'elem1' => new \KToolbox\Collection\Collection( [ 1, 2, 3 ] )
];
$navigtor = new \KToolbox\DataNavigator\DataNavigator( $data );

$result = $navigator->get( 'elem1.*' );     // Returns [ 1, 2, 3 ]
```


## <a name="data-filterer">DataFilterer</a>
This class, which internally uses `\KToolbox\DataNavigator\DataNavigator`, will allow you to filter your data based on 
one or multiple given conditions. You may provide both an array and a `\stdCLass`. For example:
```
$data = (object) [
    'elem1' => [ 'value' => 1 ],
    'elem2' => [ 'value' => 2 ],
    'elem3' => [ 'value' => 3 ],
];

$filterer = new \KToolbox\DataFilterer\DataFilterer( $data, [ [ 'value', '>=', 2 ] ] );
$result = $filterer->execute();
/**
 * Will return:
 * (object) [
 *    'elem2' => [ 'value' => 2 ],
 *    'elem3' => [ 'value' => 3 ],
 * ];
 */
```

### Query building
A query is an array containing other arrays which themselves are made by three items. The first is the key or the path 
to the key you are referring to, the second is a comparison operator and the third the expected value. THe expected 
value may also be a key or a path. Every path will be resolved by the DataNavigator. Here some examples:
```
// Elements where 'value' is equal or bigger than 2.
$query = [ 
    [ 'value', '>=', 2 ] 
];

// Elements where 'value' is equal or bigger than 2, and equal or lower than 6.
$query = [
    [ [ 'value', '>=', 2 ], 'AND', [ 'value', '<=', 6 ] ]
];

// Elements where 'value' is equal or bigger than 2, and equal or lower than 6.
// Also take every element where 'value' is lower than -2.
$query = [
    [ [ 'value', '>=', 2 ], 'AND',  [ 'value', '<=', 6 ] ],
    [ 'value', '<', -2 ] 
];

// Elements where 'value' is equal or bigger than 2, and equal or lower than 6.
//  Also take every element where 'value' is lower than -2 or bigger than 100
$query = [
    [ [ 'value', '>=', 2 ], 'AND', [ 'value', '<=', 6 ] ],
    [ [ 'value', '<', -2 ], 'OR', [ 'value', '>', 100 ] ],
];
```
You can add as many conditions as you want. Just note that:
* You can perform a logical operation(`AND`, `OR`) between 2 sets of conditions;
* Multiple sets of conditions are implicitly tied with an `AND` logical operator.

### Operators
As anticipated, you have two logical operators (`AND` and `OR`) which will act as constrains between two "comparison" 
queries. Those "comparison" queries supports the following operators:
* `=` or `==`: loose comparison
* `===`: strict comparison
* `!=`: not equal
* `!==`: not identical
* `>`: bigger than
* `>=`: bigger or equal to
* `<`: lower than
* `<=`: lower or equal to
Any invalid operator will return `false`. `<`, `<=`, `>` and `>=` operators ony compare numeric values:
```
[ 'value', '<', 2 ],          // good
[ 'value', '<', '2' ],        // good
[ 'value', '<', 'foo' ],      // bad
```

### Complex example
```
$data = (object) [
    'data_1' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'a' ],
    'data_2' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'b' ],
    'data_3' => (object) [ 'prop1' => 1, 'prop2' => 2, 'prop3' => 3, 'prop4' => 4 ],
    'data_4' => (object) [ 'prop1' => 3, 'prop2' => 4, 'prop3' => 5, 'prop4' => 6 ],
    'data_5' => (object) [ 'prop1' => [ 'a', 'b' ], 'prop2' => [ [ 'a', 'b' ] ], 'prop3' => [ 'c', 'd' ], 'prop4' => [ [ 'c', 'd' ] ] ],
];
$query = [ 
    [ [ 'prop1', '=', 'prop4' ], 'OR', [ 'prop2', '===', 'prop4' ]  ],
    [ [ 'prop1', '=', 'prop2.0' ], 'AND', [ 'prop3', '===', 'prop4.0' ]  ],
];

$filterer = new \KToolbox\DataFilterer\DataFilterer( $data, $query );
$result = $filterer->execute();
/**
 * Will return:
 * (object) [
 *     'data_1' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'a' ],
 *     'data_2' => (object) [ 'prop1' => 'a', 'prop2' => 'b', 'prop3' => 'c', 'prop4' => 'b' ],
 *     'data_5' => (object) [ 'prop1' => [ 'a', 'b' ], 'prop2' => [ [ 'a', 'b' ] ], 'prop3' => [ 'c', 'd' ], 'prop4' => [ [ 'c', 'd' ] ] ],
 * ];
 */
```