<?php
namespace cs\modules\Module_with_controller_routing\api;

class Controller {
	public static function index () {
		return __METHOD__;
	}
	public static function index_get () {
		var_dump(__METHOD__);
	}
	public static function _ () {
		var_dump(__METHOD__);
	}
	public static function __post () {
		var_dump(__METHOD__);
	}
	public static function level10_get () {
		var_dump(__METHOD__);
	}
	public static function level10_post () {
		var_dump(__METHOD__);
	}
	public static function level10_level21 () {
		var_dump(__METHOD__);
	}
	public static function level10_level21_level30_get () {
		var_dump(__METHOD__);
	}
	public static function level10_level21_level30_post () {
		var_dump(__METHOD__);
	}
}
