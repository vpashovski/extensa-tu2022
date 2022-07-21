<?php
//
// Copyright (c) 2014 Kerry Schwab & BudgetNeon.com. All rights reserved.
// This program is free software; you can redistribute it and/or modify it
// under the terms of the the FreeBSD License .
// You may obtain a copy of the full license at:
//     http://www.freebsd.org/copyright/freebsd-license.html
//
//
?>
<?php
$_['speedup_cache_return']='Return';
$_['speedup_cache_not_exist']='does not exist of is not a file';
$_['speedup_cache_not_readable']='is not readable (permissions)';
$_['speedup_cache_readable']='is readable';
$_['speedup_cache_not_writeable']='is not writeable (permissions)';
$_['speedup_cache_writeable']='is writeable';
$_['speedup_cache_php_compat']='supported (PHP 5.4 or greater)';
$_['speedup_cache_php_not_compat']='unsupported (PHP 5.4 or greater recommended)';
$_['speedup_cache_sapi_mod_php']='supported (pagecache tested with mod_php)';
$_['speedup_cache_sapi_fcgi']='supported (pagecache tested with fastcgi under PHP 5.4 or greater)';
$_['speedup_cache_sapi_fcgi_oldphp']='unsupported (known issues with fastcgi with PHP version < 5.4)';
$_['speedup_cache_sapi_litespeed']='supported (we work around broken http_response_code() in litespeed)';
$_['speedup_cache_sapi_not_tested']='unsupported (pagecache has not been tested with this SAPI)';
$_['speedup_cache_text_status']='Status';
$_['speedup_cache_only_2_2_supported']='This module is only for version 2.0.x through 2.2.x of opencart.  Opencart 2.3+ uses a different module';
$_['speedup_cache_err_topmarker']='Cannot find top marker in index.php';
$_['speedup_cache_err_bottommarker']='Cannot find bottom marker in index.php';
$_['speedup_cache_pagecache_disabled']='Pagecache is disabled in index.php';
$_['speedup_cache_pagecache_enabled']='Pagecache is enabled in index.php';
$_['speedup_cache_count_error']='Count of pagecache related lines is %s,should be %s. Edit %s  manually and fix!';
$_['speedup_cache_already_enabled']='Pagecache is already enabled';
$_['speedup_cache_enable_error']='Can\'t enable, status: ';
$_['speedup_cache_already_disabled']='Pagecache is already disabled';
$_['speedup_cache_disable_error']='Can\'t disable, status: ';
$_['speedup_cache_purged']='Purged %s files';
$_['speedup_cache_wait']='wait...';
$_['speedup_cache_label_compat']='Compatibility:';
$_['speedup_cache_label_status']='Status:';
$_['speedup_cache_header_cachestat']='Cached File Statistics';
$_['speedup_cache_td_cf']='Cached Files';
$_['speedup_cache_td_total']='Total # of Files';
$_['speedup_cache_td_space']='Space Used';
$_['speedup_cache_td_valid']='Valid';
$_['speedup_cache_td_expired']='Expired';
$_['speedup_cache_td_total']='Total';
$_['speedup_cache_btn_refresh']='Refresh Stats';
$_['speedup_cache_btn_purge']='Clear Entire Cache';
$_['speedup_cache_btn_purgeexp']='Clear Expired Files Only';
$_['speedup_cache_header_ops']='Operations';
$_['speedup_cache_header_settings']='Current Settings';
$_['speedup_cache_settings_note']='Note: Settings for reference only, changes must be made manually';
$_['speedup_cache_td_setting']='Setting';
$_['speedup_cache_td_value']='Value';
$_['speedup_cache_td_detail']='Detail';
$_['speedup_cache_expire_note']='expire time, in seconds';
$_['speedup_cache_lang_note']='default language';
$_['speedup_cache_currency_note']='default currency';
$_['speedup_cache_cachefolder_note']='the directory where the cache files are kept';
$_['speedup_cache_cachebydevice_note']='Caching by device...false means no per device cache files, otherwise, indicates method (mobiledetect or categorizr).';
$_['speedup_cache_addcomment_note']='Whether to add an html comment to the bottom of the cached file.';
$_['speedup_cache_wrapcomment_note']='Whether to wrap the html comment so it isn\'t stripped by an html minifier, like CloudFlare\'s.';
$_['speedup_cache_end_flush_note']='If true, we call ob_end_flush() in a loop before serving a cached page.  Improves performance, but creates issues in some environments';
$_['speedup_cache_skipurls_note']='A list of url patterns that should not be cached.';
$_['speedup_cache_enable_warn']='<b>Warning</b>: Enabling and disabling the cache modifies your main index.php file. <em>Have a backup copy of your main index.php for safety.</em><br><span>See <a target="_blank" href="http://github.com/budgetneon/v2pagecache">the documentation</a> for more info.</span>';
$_['speedup_cache_btn_disable']='Disable Cache';
$_['speedup_cache_btn_enable']='Enable Cache';
?>
