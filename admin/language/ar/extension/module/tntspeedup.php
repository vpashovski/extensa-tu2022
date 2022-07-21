<?php
// TNT - Speed UP
$_['heading_title']         = 'TNT - Speed UP Settings';

$_['text_module']           = 'Modules';
$_['text_success']          = 'Success: You have modified TNT - Speed UP module!';
$_['text_edit']             = 'Edit TNT - Speed UP Module';
$_['text_on']               = 'On';
$_['text_off']              = 'Off';
$_['entry_status']          = 'Status';
$_['entry_minify_css']      = 'Minify CSS <br><span class="note">Enable For Compress CSS File And Decrease HTTP Request.</span>';
$_['entry_minify_js']       = 'Minify JS <br><span class="note">Enable For Compress JS File And Decrease HTTP Request.</span>';
$_['entry_minify_html']     = 'Minify HTML <br><span class="note">Enable For Compress And Minify HTML.</span>';
$_['entry_clear_cache']     = 'Clear Cache';
$_['entry_clear_cache_tip'] = 'Clear Cache <br><span class=\'note\'>Clear JS/CSS\'s Compress And Minify Cache</span>';
$_['entry_image_dimensions']= 'Specify Image Dimensions';
$_['entry_optimize_table']  = 'Optimize Database Table <br><span class=\'note\'>Click And Optimize Database Table For Fast gathering Data.</span>';
$_['entry_compress_css_js'] = 'Optimize CSS Or JS Files <br><span class=\'note\'>Click And Optimize CSS Or JS Files For Fast loading Site.</span>';
$_['entry_image_lazyload']  = 'Image LazyLoad';
$_['entry_product_count']   = 'Count Product For Menu <br><span class="note">Disabled To Load Fast Header.</span>';
$_['entry_url_alias_cache'] = 'URL Alias Cache <br><span class="note">Enable To Generate Fast URL.</span>';
$_['entry_default_time_zone'] = 'Default Time Zone';
$_['entry_compression']     = 'Output Compression Level <br><span class=\'note\'>GZIP for more efficient transfer to requesting clients. Compression level must be between 0 - 9.</span>';
$_['entry_defer']     		= 'Above The Fold Content <br><span class=\'note\'>Eliminate render-blocking JavaScript and CSS in above-the-fold content.</span>';
$_['entry_notfound_page']   = 'Not Found Page';
$_['entry_db_cache']   		= 'Database Cache';
$_['entry_page_cache_expire_time'] = 'Cache Expire Time <br>(In Second)';
$_['entry_page_cache']   	= 'Page Cache';
$_['entry_clear_db_cache']  = 'Clear Database Cache';
$_['entry_clear_page_cache']= 'Clear Page Cache';
$_['button_save_stay']      = 'Save And Stay here';
$_['button_save_exit']      = 'Save And Exit';
$_['error_permission']      = 'Warning: You do not have permission to modify TNT - Speed UP module!';
$_['tab_compress']      	= 'Compress/Minify';
$_['tab_image']      		= 'Images';
$_['tab_database']      	= 'Database';
$_['tab_speed_test']      	= 'Speed Test';
$_['tab_cache']      		= 'Cache';

// Compress
$_['image_crusher_example'] = 'Example Extra Text';
$_['image_crusher_intro']    = 'This is the module to configure the jpeg image crusher.';
$_['image_crusher_title']    = 'New Images via Image Manager';
$_['image_crusher_image_manager_text']    = 'This section configures the settings for all future images uploaded via the image manager. Once turned on, all jpeg images uploaded through the image manager interface will be automatically optmised.';
$_['image_crusher_excluded_filetypes']    = 'Any other image type will be ignored.';
$_['image_crusher_switch_title']    = 'Switch On/Off';
$_['image_crusher_switch_text']    = 'Turn the jpeg compressor on or off by selecting one of radio buttons below.';
$_['image_crusher_switch_on']    = 'On';
$_['image_crusher_switch_off']    = 'Off';
$_['image_crusher_compression_level_title']    = 'Compression Level';
$_['image_crusher_compression_level_text']    = 'Select the level of compression you want to apply to your images. The higher the compression level, the smaller the file size.';
$_['image_crusher_compression_level_label']    = 'Level ';
$_['image_crusher_popup_success']    = 'Success: Image Crusher uploaded and reduced your file size by ';
$_['image_crusher_default_compression_level']    = '5';
$_['image_crusher_existing_image_compression_level']    = '5';
$_['image_crusher_existing_images_title']    = 'Crush Existing Images';
$_['image_crusher_existing_images_text']    = 'This feature allows you to compress images that you have previously uploaded to the image folder on your site. You simply enter the path of a folder and click Crush. The Crusher will then scan this folder and any subfolders and compress all of the jpeg images that it contains.';
$_['image_crusher_existing_images_warning_title']    = 'BE CAREFUL';
$_['image_crusher_existing_images_warning_text']    = 'This is a very powerful feature and cannot be undone. Once the images have been compressed the old version cannot be retrieved. You should try this on one folder to see if you are happy with the results before Crushing all of the images on your site.';
$_['image_crusher_existing_images_image_folder_text']    = 'Enter the path of a folder inside the image directory or select folder using folder manager e.g. catalog/demo';
$_['image_crusher_existing_images_image_folder_placeholder_text']    = 'catalog/demo';
$_['image_crusher_existing_images_submit_button']    = 'Crush Images';
$_['image_crusher_existing_images_image_folder_submit_button']    = 'Crush Images';
$_['image_crusher_existing_images_popup_text_1']    = 'Once this process is started, all the images in the specified folder will be crushed. This action cannot be paused or reversed.';
$_['image_crusher_existing_images_popup_text_2']    = 'Are you sure?';


// Speed Test
$_['text_api_key']			 			= 'Your API Key';
$_['text_url']			 				= 'Page URL';
$_['text_filter_third_party_resource'] 	= 'Filter Third Party Resources';
$_['text_locale'] 						= 'Locale';
$_['text_rule'] 						= 'Rule';
$_['text_screenshot'] 					= 'Screenshot';
$_['text_strategy'] 					= 'Strategy';
$_['text_fields']				 		= 'Fields';
$_['text_true']				 			= 'True';
$_['text_false']				 		= 'False';
$_['text_desktop']				 		= 'Desktop';
$_['text_mobile']				 		= 'Mobile';

//Note
$_['note_api_key']			 			= 'Enter <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="dev-console">Your Google API Key

<img src="https://developers.google.com/+/mobile/images/OpenInNewWindow14x14.png">.</a>';
$_['note_url']			 				= 'The URL to fetch and analyze.';
$_['note_filter_third_party_resource'] 	= 'Indicates if third party resources should be filtered out before PageSpeed analysis.';
$_['note_locale'] 						= 'The locale used to localize formatted results.';
$_['note_rule'] 						= 'A PageSpeed rule to run; if none are given, all rules are run.';
$_['note_screenshot'] 					= 'Indicates if binary data containing a screenshot should be included.';
$_['note_strategy'] 					= 'The analysis strategy to use.';
$_['note_fields']				 		= 'Selector specifying which fields to include in a partial response.';

//Button
$_['btn_execute']        		= 'Execute';

// Help
$_['help_product']     			= '(Autocomplete)';

// Error
$_['error_permission'] 			= 'Warning: You do not have permission to modify HIT Page Speed Test module!';
$_['error_name']       			= 'Module Name must be between 3 and 64 characters!';
$_['error_api_key']      		= 'API Key Required!';
$_['error_url']      			= 'URL Required!';


// Text
$_['text_result_text'] 			= 'PageSpeed Insights';
$_['text_possible_optimisation']= 'Possible Optimisation';
$_['text_optimisation_found']	= 'Optimisation Found';
$_['text_download']				= 'Download optimized image, JavaScript, and CSS resources for this page';
$_['text_table_size']			= 'Content size by content type';
$_['text_field_content_type']	= 'Content Type';
$_['text_field_content_size']	= 'Size';
$_['text_table_request']		= 'Requests by content type';
$_['text_field_content_request']= 'Requests';
