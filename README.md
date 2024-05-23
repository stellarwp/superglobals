# StellarWP SuperGlobals

A library that handles access to superglobals.

## Table of Contents

* [Installation](#installation)
* [Usage](#usage)
  * [`SuperGlobals::get_get_var( $var, $default = null )`](#get_get_var-var-default-null)
  * [`SuperGlobals::get_post_var( $var, $default = null )`](#get_post_var-var-default-null)
  * [`SuperGlobals::get_raw_superglobal( $var, $default = null )`](#get_raw_superglobal-var-default-null)
  * [`SuperGlobals::get_sanitized_superglobal( $var, $default = null )`](#get_sanitized_superglobal-var-default-null)
  * [`SuperGlobals::get_server_var( $var, $default = null )`](#get_server_var-var-default-null)
  * [`SuperGlobals::get_var( $var, $default = null )`](#get_var-var-default-null)
  * [`SuperGlobals::sanitize_deep( &$value )`](#sanitize_deep-value)

## Installation

It's recommended that you install SuperGlobals as a project dependency via [Composer](https://getcomposer.org/):

```bash
composer require stellarwp/superglobals
```

> We _actually_ recommend that this library gets included in your project using [Strauss](https://github.com/BrianHenryIE/strauss).
>
> Luckily, adding Strauss to your `composer.json` is only slightly more complicated than adding a typical dependency, so checkout our [strauss docs](https://github.com/stellarwp/global-docs/blob/main/docs/strauss-setup.md).

**An important note on namespaces:**

> The docs will in this repo all use `StellarWP\SuperGlobals` as the base namespace, however, if you are using [Strauss](#strauss)
> to prefix namespaces in your project, you will need to adapt the namespaces accordingly. (Example: `Boom\Shakalaka\StellarWP\SuperGlobals`)

## Usage

### `SuperGlobals::get_get_var( $var, $default = null )`

Get a `$_GET` value and recursively sanitize it using `SuperGlobals::sanitize_deep()`.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

// Get $_GET['post_id']
$var = SuperGlobals::get_get_var( 'post_id' );

// Provide a default value if the variable is not set.
$var = SuperGlobals::get_get_var( 'post_id', 12 );
```

### `SuperGlobals::get_post_var( $var, $default = null )`

Get a `$_POST` value and recursively sanitize it using `SuperGlobals::sanitize_deep()`.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

// Get $_POST['post_id']
$var = SuperGlobals::get_post_var( 'post_id' );

// Provide a default value if the variable is not set.
$var = SuperGlobals::get_post_var( 'post_id', 12 );
```

### `SuperGlobals::get_raw_superglobal( $var, $default = null )`

Gets the requested superglobal variable. Options are `ENV`, `GET`, `POST`, `REQUEST`, or `SERVER`.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

// Get $_ENV
$env     = SuperGlobals::get_raw_superglobal( 'ENV' );

// Get $_GET
$get     = SuperGlobals::get_raw_superglobal( 'GET' );

// Get $_POST
$post    = SuperGlobals::get_raw_superglobal( 'POST' );

// Get $_REQUEST
$request = SuperGlobals::get_raw_superglobal( 'REQUEST' );

// Get $_SERVER
$server  = SuperGlobals::get_raw_superglobal( 'SERVER' );
```

### `SuperGlobals::get_sanitized_superglobal( $var, $default = null )`

Gets the requested superglobal variable, sanitized. Options are `ENV`, `GET`, `POST`, `REQUEST`, or `SERVER`.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

// Get $_ENV
$env     = SuperGlobals::get_sanitized_superglobal( 'ENV' );

// Get $_GET
$get     = SuperGlobals::get_sanitized_superglobal( 'GET' );

// Get $_POST
$post    = SuperGlobals::get_sanitized_superglobal( 'POST' );

// Get $_REQUEST
$request = SuperGlobals::get_sanitized_superglobal( 'REQUEST' );

// Get $_SERVER
$server  = SuperGlobals::get_sanitized_superglobal( 'SERVER' );
```

### `SuperGlobals::get_server_var( $var, $default = null )`

Get a `$_SERVER` value and recursively sanitize it using `SuperGlobals::sanitize_deep()`.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

// Get $_SERVER['REQUEST_URI']
$var = SuperGlobals::get_server_var( 'REQUEST_URI' );

// Provide a default value if the variable is not set.
$var = SuperGlobals::get_server_var( 'REQUEST_URI', 'http://example.com' );
```

### `SuperGlobals::get_var( $var, $default = null )`

Gets a value from `$_REQUEST`, `$_POST`, or `$_GET` and recursively sanitizes it using `SuperGlobals::sanitize_deep()`.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

// Get $_REQUEST['post_id'] or $_POST['post_id'] or $_GET['post_id'], wherever it lives
$var = SuperGlobals::get_var( 'post_id' );

// Provide a default value if the variable is not set.
$var = SuperGlobals::get_var( 'post_id', 12 );
```

### `SuperGlobals::sanitize_deep( &$value )`

Sanitizes a value recursively using appropriate sanitization functions depending on the type of the value.

#### Example

```php
use StellarWP\SuperGlobals\SuperGlobals;

$var = SuperGlobals::sanitize_deep( $some_var );
```
