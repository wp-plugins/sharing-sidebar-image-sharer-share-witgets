<?php
/* 
* +--------------------------------------------------------------------------+
* | Copyright (c) ShemOtechnik Profitquery Team shemotechnik@profitquery.com |
* +--------------------------------------------------------------------------+
* | This program is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by     |
* | the Free Software Foundation; either version 2 of the License, or        |
* | (at your option) any later version.                                      |
* |                                                                          |
* | This program is distributed in the hope that it will be useful,          |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
* | GNU General Public License for more details.                             |
* |                                                                          |
* | You should have received a copy of the GNU General Public License        |
* | along with this program; if not, write to the Free Software              |
* | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
* +--------------------------------------------------------------------------+
*/
/**
* Plugin Name: Sharing Sidebar + Image Sharer | Share Widget
* Plugin URI: http://profitquery.com/sharing_witgets.html
* Description: Simply widgets for growth 3x website shares, referrals from social network and all for free.
* Version: 2.0.1
*
* Author: Profitquery Team <support@profitquery.com>
* Author URI: http://profitquery.com/?utm_campaign=subscribe_widgets_wp
*/

//update_option('profitquery', array());
$profitquery = get_option('profitquery');

if (!defined('PROFITQUERY_SHARE_WIDGETS_PLUGIN_NAME'))
	define('PROFITQUERY_SHARE_WIDGETS_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('PROFITQUERY_SHARE_WIDGETS_PAGE_NAME'))
	define('PROFITQUERY_SHARE_WIDGETS_PAGE_NAME', 'profitquery_share_widgets');

if (!defined('PROFITQUERY_SHARE_WIDGETS_ADMIN_CSS_PATH'))
	define('PROFITQUERY_SHARE_WIDGETS_ADMIN_CSS_PATH', 'css/');

if (!defined('PROFITQUERY_SHARE_WIDGETS_ADMIN_JS_PATH'))
	define('PROFITQUERY_SHARE_WIDGETS_ADMIN_JS_PATH', 'js/');

if (!defined('PROFITQUERY_SHARE_WIDGETS_ADMIN_IMG_PATH'))
	define('PROFITQUERY_SHARE_WIDGETS_ADMIN_IMG_PATH', 'images/');

if (!defined('PROFITQUERY_SHARE_WIDGETS_ADMIN_IMG_PREVIEW_PATH'))
	define('PROFITQUERY_SHARE_WIDGETS_ADMIN_IMG_PREVIEW_PATH', 'preview/');

$pathParts = pathinfo(__FILE__);
$path = $pathParts['dirname'];

if (!defined('PROFITQUERY_SHARE_WIDGETS_FILENAME'))
	define('PROFITQUERY_SHARE_WIDGETS_FILENAME', $path.'/profitquery_share_widgets.php');



require_once 'profitquery_share_widgets_class.php';
$ProfitQueryShareWidgetsClass = new ProfitQueryShareWidgetsClass();



add_action('init', 'profitquery_share_widgets_init');



function profitquery_share_widgets_init(){
	global $profitquery;	
	if ( !is_admin() && $profitquery[apiKey] && !$profitquery['errorApiKey'] && !$profitquery['aio_widgets_loaded']){
		add_action('wp_head', 'profitquery_share_widgets_hack_for_cach_code');
		wp_register_script('lite_profitquery_lib', plugins_url().'/'.PROFITQUERY_SHARE_WIDGETS_PLUGIN_NAME.'/js/lite.profitquery.min.js?apiKey='.$profitquery[apiKey]);		
		wp_enqueue_script('lite_profitquery_lib');		
		add_action('wp_footer', 'profitquery_share_widgets_insert_code');
	}
}

function profitquery_share_widgets_hack_for_cach_code(){
	global $profitquery;
	if($profitquery[apiKey]){
		echo '<script>var profitqueryLiteAPIKey="'.$profitquery[apiKey].'";</script>';
	}
}

/* Adding action links on plugin list*/
function profitquery_share_wordpress_admin_link($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="options-general.php?page=profitquery_share_widgets">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}


function profitquery_share_widgets_insert_code(){
	global $profitquery;	
	global $ProfitQueryShareWidgetsClass;	
	
	$profitquerySmartWidgetsStructure = array();	
	$profitquerySmartWidgetsStructure['followUsFloatingPopup'] = array(
		'disabled'=>1		
	);
	if((int)$profitquery[subscribe_widgets_loaded] == 0){
		$profitquerySmartWidgetsStructure['subscribeBarOptions'] = array(
			'disabled'=>1		
		);
		$profitquerySmartWidgetsStructure['subscribeExitPopupOptions'] = array(
			'disabled'=>1		
		);
	} else {
		$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[subscribeBar]);
		if($preparedObject[animation] && $preparedObject[animation] != 'fade') $preparedObject[animation] = 'pq_animated '.$preparedObject[animation];
		$profitquerySmartWidgetsStructure['subscribeBarOptions'] = array(
			'title'=>stripslashes($preparedObject[title]),		
			'disabled'=>(int)$preparedObject[disabled],
			'afterProfitLoader'=>$preparedObject[afterProceed],
			'typeWindow'=>'pq_bar '.stripslashes($preparedObject[position]).' '.stripslashes($preparedObject[background]).' '.stripslashes($preparedObject[button_color]).' '.stripslashes($preparedObject[animation]),		
			'inputEmailTitle'=>stripslashes($preparedObject[inputEmailTitle]),
			'buttonTitle'=>stripslashes($preparedObject[buttonTitle]),
			'formAction'=>stripslashes($profitquery[subscribeProviderUrl])
		);
		
		$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[subscribeExit]);
		if($preparedObject[animation] && $preparedObject[animation] != 'fade') $preparedObject[animation] = 'pq_animated '.$preparedObject[animation];
		$profitquerySmartWidgetsStructure['subscribeExitPopupOptions'] = array(
			'title'=>stripslashes($preparedObject[title]),
			'sub_title'=>stripslashes($preparedObject[sub_title]),		
			'img'=>stripslashes($preparedObject[img]),
			'disabled'=>(int)$preparedObject[disabled],
			'afterProfitLoader'=>$preparedObject[afterProceed],
			'typeWindow'=>stripslashes($preparedObject[typeWindow]).' '.stripslashes($preparedObject[background]).' '.stripslashes($preparedObject[button_color]).' '.stripslashes($preparedObject[animation]),
			'blackoutOption'=>array('disable'=>0, 'style'=>stripslashes($preparedObject[overlay])),
			'inputEmailTitle'=>stripslashes($preparedObject[inputEmailTitle]),
			'buttonTitle'=>stripslashes($preparedObject[buttonTitle]),
			'formAction'=>stripslashes($profitquery[subscribeProviderUrl])
		);		
	}
	if((int)$profitquery[feedback_widgets_loaded] == 0){
		$profitquerySmartWidgetsStructure['phoneCollectOptions'] = array(
			'disabled'=>1		
		);
		$profitquerySmartWidgetsStructure['contactUsOptions'] = array(
			'disabled'=>1		
		);
	}else{
		$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[callMe]);
		if($preparedObject[animation] && $preparedObject[animation] != 'fade') $preparedObject[animation] = 'pq_animated '.$preparedObject[animation];
		$profitquerySmartWidgetsStructure['phoneCollectOptions'] = array(
			'disabled'=>(int)$preparedObject[disabled],
			'title'=>stripslashes($preparedObject[title]),
			'sub_title'=>stripslashes($preparedObject[sub_title]),
			'img'=>stripslashes($preparedObject[img]),
			'buttonTitle'=>stripslashes($preparedObject[buttonTitle]),
			'typeBookmark'=>stripslashes($preparedObject[position]).' '.stripslashes($preparedObject[loader_background]).' pq_call',			
			'typeWindow'=>stripslashes($preparedObject[typeWindow]).' '.stripslashes($preparedObject[background]).' '.stripslashes($preparedObject[button_color]).' '.stripslashes($preparedObject[animation]),
			'blackoutOption'=>array('disable'=>0, 'style'=>stripslashes($preparedObject[overlay])),
			'afterProfitLoader'=>$preparedObject[afterProceed],
			'emailOption'=>array(
				'to_email'=>stripslashes($profitquery[adminEmail])			
			)
		);
		
		$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[contactUs]);
		if($preparedObject[animation] && $preparedObject[animation] != 'fade') $preparedObject[animation] = 'pq_animated '.$preparedObject[animation];
		$profitquerySmartWidgetsStructure['contactUsOptions'] = array(
			'disabled'=>(int)$preparedObject[disabled],
			'title'=>stripslashes($preparedObject[title]),
			'sub_title'=>stripslashes($preparedObject[sub_title]),
			'img'=>stripslashes($preparedObject[img]),
			'buttonTitle'=>stripslashes($preparedObject[buttonTitle]),
			'typeBookmark'=>stripslashes($preparedObject[position]).' '.stripslashes($preparedObject[loader_background]).' pq_contact',			
			'typeWindow'=>stripslashes($preparedObject[typeWindow]).' '.stripslashes($preparedObject[background]).' '.stripslashes($preparedObject[button_color]).' '.stripslashes($preparedObject[animation]),
			'blackoutOption'=>array('disable'=>0, 'style'=>stripslashes($preparedObject[overlay])),
			'afterProfitLoader'=>$preparedObject[afterProceed],
			'emailOption'=>array(
				'to_email'=>stripslashes($profitquery[adminEmail])			
			)
		);		
	}
	$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[sharingSideBar]);
	$preparedObject[socnet][typeBlock] = 'pq-social-block '.$preparedObject[design];
	$profitquerySmartWidgetsStructure['sharingSideBarOptions'] = array(
		'typeWindow'=>'pq_icons '.$preparedObject[position],
		'socnetIconsBlock'=>$preparedObject[socnet],
		'disabled'=>(int)$preparedObject[disabled],
		'afterProfitLoader'=>$preparedObject[afterProceed]
	);
	
	$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[imageSharer]);	
	$profitquerySmartWidgetsStructure['imageSharer'] = array(
		'typeDesign'=>$preparedObject[design].' '.$preparedObject[position],
		'minWidth'=>(int)$preparedObject[minWidth],
		'disabled'=>(int)$preparedObject[disabled],
		'activeSocnet'=>$preparedObject[socnet],
		'afterProfitLoader'=>stripslashes($preparedObject[afterProceed])
	);	
	
	$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[thankPopup]);
	if($preparedObject[animation] && $preparedObject[animation] != 'fade') $preparedObject[animation] = 'pq_animated '.$preparedObject[animation];
	$profitquerySmartWidgetsStructure['thankPopupOptions'] = array(
		'title'=>stripslashes($preparedObject[title]),
		'sub_title'=>stripslashes($preparedObject[sub_title]),
		'typeWindow'=>stripslashes($preparedObject[background]).' '.stripslashes($preparedObject[animation]),
		'blackoutOption'=>array('disable'=>0, 'style'=>stripslashes($preparedObject[overlay])),
		'img'=>stripslashes($preparedObject[img]),
		'buttonTitle'=>stripslashes($preparedObject[buttonTitle])
	);
	
	$preparedObject = $ProfitQueryShareWidgetsClass->prepare_sctructure_product($profitquery[follow]);
	if($preparedObject[animation] && $preparedObject[animation] != 'fade') $preparedObject[animation] = 'pq_animated '.$preparedObject[animation];
	$profitquerySmartWidgetsStructure['followUsOptions'] = array(
		'title'=>stripslashes($preparedObject[title]),
		'sub_title'=>stripslashes($preparedObject[sub_title]),
		'typeWindow'=>stripslashes($preparedObject[background]).' '.stripslashes($preparedObject[animation]),
		'blackoutOption'=>array('disable'=>0, 'style'=>stripslashes($preparedObject[overlay])),
		'socnetIconsBlock'=>$preparedObject[follow_socnet]
	);		

	
	print "
	<script>
	profitquery.loadFunc.callAfterPQInit(function(){
		var smartWidgetsBoxObject = ".json_encode($profitquerySmartWidgetsStructure).";	
		profitquery.widgets.smartWidgetsBox(smartWidgetsBoxObject);	
	});
	</script>
	";
}

add_filter('plugin_action_links', 'profitquery_share_wordpress_admin_link', 10, 2);