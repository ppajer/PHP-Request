# PHP-Request

Intuitive and fluent wrappers for making cURL and multi-cURL requests.

## Installation

To start using this library, you have two options: add it to your project's dependencies and use `composer install`, or download it manually and include either the `autoload.php` file or the class files directly.

## Usage

### Single requests - `class.Request.php`

The `Request` class represents a single request to a resource, and its settings. It provides a simple fluent API to create and edit requests, and some built-in constants for widespread options to save you the time of having to look up User Agent strings and content types for the most common uses. Additional convenience constants will be added over time.

#### Properties

```(php)
class Request {

	const UA_MOZILLA;
	const UA_CHROME;
	const UA_SAFARI;
	const UA_EXPLORER;
	const UA_EDGE;
	const UA_OPERA;

	const CONTENT_TYPE_XML;
	const CONTENT_TYPE_XHTML;
	const CONTENT_TYPE_TXT;
	const CONTENT_TYPE_TTF;
	const CONTENT_TYPE_MJS;
	const CONTENT_TYPE_JSONLD;
	const CONTENT_TYPE_JSON;
	const CONTENT_TYPE_JS;
	const CONTENT_TYPE_CSV;
	const CONTENT_TYPE_CSS;

	public $URL; // Default: null
	public $UA; // Default: UA_SAFARI
	public $method; // Default: "GET"
	public $content; // Default: null
	public $headers; // Default: null
	public $contentType; // Default: CONTENT_TYPE_TXT
}
```

#### Methods

All methods except `response` return their instance of `Request` to allow method chaining.

```(php)
class Request {

	public function __construct(String $url);
	
	public function URL(String $url) : Request // Sets the URL of the request.

	public function userAgent(String $UA) : Request // Sets the userAgent of the request.

	public function method(String $method) : Request // Sets the method of the request.

	public function content(Array $content) : Request // Sets the content of the request.

	public function headers(Array $headers) : Request // Sets the headers of the request.

	public function contentType(String $contentType) : Request // Sets the contentType of the request.

	public function send() : Request // Processes the request.

	public function response() : String // Fetches the response body.
}
```

### Multiple requests - `class.ParallelRequest.php`

The `ParallelRequest` class allows you to fetch a number of resources asynchronously, providing a large performance boost over making sequential requests.

#### Methods

Most of the API is the same as for a regular request, except for the constructor taking an array of request options. The array must consist of keys matching the parameters of the class - if provided, these keys will be used as settings for the individual requests, with the missing values being filled with class defaults.

```(php)
class ParallelRequest {
	
	public function __construct(Array $requestList);

	public function awaitAll() : ParallelRequest // Process all requests and wait for them to finish

	public function response() : Array // Gets the results of the batch. Each response is found under the same key in the resulting array as the one used to mark its request in the request options array passed to the constructor. Example below.
}
```

##### Constructor request options example

```(php)
$opts = [
	'someKey' => [
		'URL' => 'https://...',
		'method' => 'POST',
		'content' => [
			'foo' => 'bar'
		]
	],
	'someOtherKey' => [
		'URL' => 'https://...'
	]
];

/*

Will return:
[
	'someKey' => '<!DOCTYPE html...', // Response from the URL with `?foo=bar` sent via POST
	'someOtherKey' => '...' // Response from the URL with default GET
]

*/
```

## Full example

```(php)

require 'php-request/autoload.php';

// Single
$url = 'http://some.api.com';

$singleRequest = new Request($url);
$singleRequest->userAgent(Request::UA_CHROME)
			->contentType(Request::CONTENT_TYPE_JSON)
			->content(json_encode($someData))
			->method('POST')
			->send();

$response = $singleRequest->response();

// Multiple
$opts = [
	'A' => [
		'URL' => '...'
	],
	'B' => [
		'URL' => '...'
	],
	...
];

$parallel = new ParallelRequest($opts);
$response = $parallel->awaitAll()->response();
```