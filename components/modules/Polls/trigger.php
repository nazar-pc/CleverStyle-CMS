<?php
/**
 * @package        Polls
 * @category       modules
 * @author         Nazar Mokrynskyi <nazar@mokrynskyi.com>
 * @copyright      Copyright (c) 2014, Nazar Mokrynskyi
 * @license        MIT License, see license.txt
 */
namespace cs;

Trigger::instance()->register(
	'System/Index/construct',
	function () {
		switch (Config::instance()->module('Polls')->active()) {
			case 0:
			case 1:
				if (!ADMIN) {
					return;
				}
				require __DIR__.'/trigger/installed.php';
		}
	}
);
