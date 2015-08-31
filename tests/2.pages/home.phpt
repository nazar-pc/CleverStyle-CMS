--TEST--
Home page rendering
--FILE--
<?php
namespace cs;
include __DIR__.'/../custom_loader.php';
include __DIR__.'/../_SERVER.php';
// Simulate regular initialization
Language::instance();
Index::instance();
shutdown_function(true);
shutdown_function();
?>
--EXPECTF--
<!doctype html>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
	<title>Web-site | Home</title>
	<meta charset="utf-8">
	<meta content="CleverStyle CMS by Mokrynskyi Nazar" name="generator">
	<base href="http://cscms.travis/">
	<link href="/themes/CleverStyle/img/favicon.ico" rel="shortcut icon">
	<script class="cs-config" target="cs" type="application/json">{"base_url":"http:\/\/cscms.travis","current_base_url":"http:\/\/cscms.travis\/System","public_key":"%s","module":"System","in_admin":0,"is_admin":0,"is_user":0,"is_guest":1,"password_min_length":4,"password_min_strength":%d,"debug":0,"cookie_prefix":"","cookie_domain":"cscms.travis","cookie_path":"\/","protocol":"http","route":[],"route_path":["blank"],"route_ids":[]}</script>
	<script class="cs-config" target="cs.rules_text" type="application/json">""</script>
	<link href="/storage/pcache/_CleverStyle_en.css?%s" rel="stylesheet" shim-shadowdom>
	<meta content="Web-site | Home" property="og:title">
	<meta content="http://cscms.travis" property="og:url">
	<meta content="Web-site" property="og:site_name">
	<meta content="website" property="og:type">
</head>

<body unresolved>

	<header>
		<div>
			<a href="/" class="cs-text-lead">Web-site</a>
			<nav>
				<a href="/">Home</a>
			</nav>
			<aside>
				<img alt="" src="/includes/img/guest.svg">
				<div class="cs-header-guest-form active">
					<b>Hello, Guest!</b>
					<div>
						<button class="cs-header-sign-in-slide" icon="sign-in" is="cs-button" type="button">Sign in</button>
						<button class="cs-header-registration-slide" icon="pencil" is="cs-button" tooltip="Quick registration form" type="button">Sign up</button>
					</div>
				</div>
				<div class="cs-header-restore-password-form">
					<input autocapitalize="off" autocorrect="off" class="cs-header-restore-password-email" placeholder="Login or e-mail" tabindex="1" type="text">
					<br>
					<button class="cs-header-restore-password-process" icon="question" is="cs-button" tabindex="2" type="button">Restore password</button>
					<button class="cs-header-back" icon="chevron-down" is="cs-button" tooltip="Back" type="button">&nbsp;</button>
				</div>
				<div class="cs-header-registration-form">
					<input autocapitalize="off" autocorrect="off" class="cs-header-registration-email" placeholder="Email" type="email">
					<br>
					<button class="cs-header-registration-process" icon="pencil" is="cs-button" type="button">Sign up</button>
					<button class="cs-header-back" icon="chevron-down" is="cs-button" tooltip="Back" type="button">&nbsp;</button>
				</div>
				<form class="cs-header-sign-in-form" method="post">
					<input autocapitalize="off" autocorrect="off" class="cs-header-sign-in-email" placeholder="Login or e-mail" type="text">
					<input class="cs-header-user-password" placeholder="Password" type="password">
					<br>
					<button icon="sign-in" is="cs-button" type="submit">Sign in</button>
					<button class="cs-header-back" icon="chevron-down" is="cs-button" tooltip="Back" type="button">&nbsp;</button>
					<button class="cs-header-restore-password-slide" icon="question" is="cs-button" tooltip="Restore password" type="button">&nbsp;</button>
					<input name="session" type="hidden" value="%s">
				</form>
			</aside>
		</div>
		<hr>
		%w
	</header>
	<div id="body">
		<aside id="left_blocks">

		</aside>
		<aside id="right_blocks">

		</aside>
		<aside id="top_blocks">

		</aside>
		<div id="main_content">
			<div>

			</div>
		</div>
		<aside id="bottom_blocks">

		</aside>
	</div>
	<footer>
		<div>
			Page generated in %f seconds, %d query(s) in DB in %f seconds, memory consumption %f MB<sup>%f MB</sup>
		</div>
		<div>
			© Powered by <a target="_blank" href="http://cleverstyle.org/cms" title="CleverStyle CMS">CleverStyle CMS</a>
		</div>
	</footer>
	<script src="/storage/pcache/_CleverStyle_en.js?%s"></script>
	<link href="/storage/pcache/_CleverStyle_en.html?%s" rel="import">

</body>
