<?php
/**
 * @package   Shop
 * @category  modules
 * @author    Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright Copyright (c) 2014, Nazar Mokrynskyi
 * @license   MIT License, see license.txt
 */
namespace cs\modules\Shop;
use
	cs\Index,
	cs\Page;

$Index      = Index::instance();
$Page       = Page::instance();
$Categories = Categories::instance();
if (isset($_GET['ids'])) {
	$categories = $Categories->get(explode(',', $Index->route_ids[0]));
	if (!$categories) {
		error_code(404);
	} else {
		$Page->json($categories);
	}
} elseif (isset($Index->route_ids[0])) {
	$category = $Categories->get($Index->route_ids[0]);
	if (!$category) {
		error_code(404);
	} else {
		$Page->json($category);
	}
} else {
	$Page->json(
		$Categories->get(
			array_filter($Categories->get_all(), function ($category) {
				return $category['visible'];
			})
		)
	);
}
