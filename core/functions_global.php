<?php
/**
 * @package   CleverStyle CMS
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2015, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
/**
 * Base system functions, do not edit this file, or make it very carefully
 * otherwise system workability may be broken
 *
 * This particular file contains functions that work with global state (cookies, headers, status codes, etc.)
 */
use
	cs\Config;
/**
 * Function for setting cookies on all mirrors and taking into account cookies prefix. Parameters like in system function, but $path, $domain and $secure
 * are skipped, they are detected automatically, and $api parameter added in the end.
 *
 * @param string $name
 * @param string $value
 * @param int    $expire
 * @param bool   $httponly
 *
 * @return bool
 */
function _setcookie ($name, $value, $expire = 0, $httponly = false) {
	static $path, $domain, $prefix, $secure;
	if (!isset($prefix)) {
		$Config = Config::instance(true);
		$prefix = '';
		/**
		 * @var \cs\_SERVER $_SERVER
		 */
		$secure = $_SERVER->secure;
		$domain = $_SERVER->host;
		$path   = '/';
		if ($Config) {
			$prefix = $Config->core['cookie_prefix'];
			$domain = $Config->core['cookie_domain'][$Config->server['mirror_index']];
			$path   = $Config->core['cookie_path'][$Config->server['mirror_index']];
		}
	}
	if ($value === '') {
		unset($_COOKIE[$prefix.$name]);
	} else {
		$_COOKIE[$prefix.$name] = $value;
	}
	return setcookie(
		$prefix.$name,
		$value,
		$expire,
		$path,
		$domain,
		$secure,
		$httponly
	);
}

/**
 * Function for getting of cookies, taking into account cookies prefix
 *
 * @param $name
 *
 * @return bool
 */
function _getcookie ($name) {
	static $prefix;
	if (!isset($prefix)) {
		$prefix = Config::instance(true)->core['cookie_prefix'] ?: '';
	}
	return isset($_COOKIE[$prefix.$name]) ? $_COOKIE[$prefix.$name] : false;
}

/**
 * Function that is used to define errors by specifying error code, and system will account this in its operation
 *
 * @param int|null $code
 *
 * @return int                <b>0</b> if no errors, error code otherwise
 */
function error_code ($code = null) {
	static $stored_code = 0;
	if (
		$code !== null &&
		(
			!$stored_code || $code == 0 //Allows to reset error code, but not allows to redefine by other code directly
		)
	) {
		$stored_code = $code;
	}
	return $stored_code;
}

/**
 * Is current path from administration area?
 *
 * @param bool|null $admin_path
 *
 * @return bool
 */
function admin_path ($admin_path = null) {
	static $stored_admin_path = false;
	if ($admin_path !== null) {
		$stored_admin_path = $admin_path;
	}
	return $stored_admin_path;
}

/**
 * Is current path from api area?
 *
 * @param bool|null $api_path
 *
 * @return bool
 */
function api_path ($api_path = null) {
	static $stored_api_path = false;
	if ($api_path !== null) {
		$stored_api_path = $api_path;
	}
	return $stored_api_path;
}

/**
 * Name of currently used module (for generation of current page)
 *
 * @param null|string $current_module
 *
 * @return bool
 */
function current_module ($current_module = null) {
	static $stored_current_module = '';
	if ($current_module !== null) {
		$stored_current_module = $current_module;
	}
	return $stored_current_module;
}

/**
 * Is current page a home page?
 *
 * @param bool|null $home_page
 *
 * @return bool
 */
function home_page ($home_page = null) {
	static $stored_home_page = false;
	if ($home_page !== null) {
		$stored_home_page = $home_page;
	}
	return $stored_home_page;
}

/**
 * Sends header with string representation of http status code, for example "404 Not Found" for corresponding server protocol
 *
 * @param int $code Status code number
 *
 * @return null|string String representation of status code code
 */
function code_header ($code) {
	$string_code = null;
	switch ($code) {
		case 201:
			$string_code = '201 Created';
			break;
		case 202:
			$string_code = '202 Accepted';
			break;
		case 301:
			$string_code = '301 Moved Permanently';
			break;
		case 302:
			$string_code = '302 Found';
			break;
		case 303:
			$string_code = '303 See Other';
			break;
		case 307:
			$string_code = '307 Temporary Redirect';
			break;
		case 400:
			$string_code = '400 Bad Request';
			break;
		case 403:
			$string_code = '403 Forbidden';
			break;
		case 404:
			$string_code = '404 Not Found';
			break;
		case 405:
			$string_code = '405 Method Not Allowed';
			break;
		case 409:
			$string_code = '409 Conflict';
			break;
		case 429:
			$string_code = '429 Too Many Requests';
			break;
		case 500:
			$string_code = '500 Internal Server Error';
			break;
		case 501:
			$string_code = '501 Not Implemented';
			break;
		case 503:
			$string_code = '503 Service Unavailable';
			break;
	}
	if ($string_code) {
		header("$_SERVER[SERVER_PROTOCOL] $string_code", true, (int)$code);
	}
	return $string_code;
}

/**
 * Send a raw HTTP header
 *
 * @param string $string             There are two special-case header calls. The first is a header that starts with the string "HTTP/" (case is not significant),
 *                                   which will be used to figure out the HTTP status code to send. For example, if you have configured Apache to use a PHP script
 *                                   to handle requests for missing files (using the ErrorDocument directive),
 *                                   you may want to make sure that your script generates the proper status code.
 * @param bool   $replace            The optional replace parameter indicates whether the header should replace a previous similar header,
 *                                   or add a second header of the same type. By default it will replace
 * @param null   $http_response_code Forces the HTTP response code to the specified value
 */
function _header ($string, $replace = true, $http_response_code = null) {
	header($string, $replace, $http_response_code);
}

