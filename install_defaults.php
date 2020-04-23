<?php

// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | vthemes Plugin 1.5.1                                                          |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2007 by the following authors:                              |
// |                                                                           |
// | Author: Mark R. Evans - mevans@ecsnet.com                                 |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * vthemes default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

/**
* the vthemes plugin's config array
*/
global $_VT_DEFAULT;
$_VT_DEFAULT = array();

/**
 * this lets you select which functions are available for registered users only
 */
$_VT_DEFAULT['vthemesloginrequired'] = 0;

/**
 * Set to 1 to hide the "vthemes" entry from the top menu:
 */
$_VT_DEFAULT['hidevthemesmenu']    = 0;


/*
 * Configuration Options:
 *
 * max_width_preview 	- Sets the maximum width of the thumbnail image
 * colums				- The number of columns to display
 * rows					- The number of rows to display per page
 * disable_menu			- Disable the 'vThemes' link the menu items
 * disable_use_theme	- Disable the 'Use Theme' button
 * disable_download		- Disable the Download Theme link
 *
 */

$_VT_DEFAULT['max_width_preview'] 	= '480';
$_VT_DEFAULT['columns'] 			= 1;
$_VT_DEFAULT['rows']    			= 4;
$_VT_DEFAULT['disable_use_theme'] 	= 0;
$_VT_DEFAULT['disable_download'] 	= 0;

/*
 * Visual Tweaks
 */
 $_VT_DEFAULT['leftblocks']		= '1';

// vthemes Block
$_VT_DEFAULT['block_isleft'] = 0;
$_VT_DEFAULT['block_order'] = 100;
$_VT_DEFAULT['block_topic_option'] = TOPIC_ALL_OPTION;
$_VT_DEFAULT['block_topic'] = array();
$_VT_DEFAULT['block_enable'] = true;
$_VT_DEFAULT['block_permissions'] = array (2, 2, 2, 2);

/**
* Initialize vthemes plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_VT_CONF if available (e.g. from
* an old config.php), uses $_VT_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_vthemes()
{
    global $_VT_CONF, $_VT_DEFAULT, $_GROUPS, $_TABLES;

    if (is_array($_VT_CONF) && (count($_VT_CONF) > 1)) {
        $_VT_DEFAULT = array_merge($_VT_DEFAULT, $_VT_CONF);
    }

    $c = config::get_instance();
    if (!$c->group_exists('vthemes')) {
        $c->add('sg_main', NULL, 'subgroup', 0, 0, NULL, 0, true, 'vthemes', 0);
        $c->add('tab_main', NULL, 'tab', 0, 0, NULL, 0, true, 'vthemes', 0);
        $c->add('fs_access', NULL, 'fieldset', 0, 0, NULL, 0, true, 'vthemes', 0);
        $c->add('vthemesloginrequired', $_VT_DEFAULT['vthemesloginrequired'],
                'select', 0, 0, 0, 10, true, 'vthemes', 0);
        $c->add('hidevthemesmenu', $_VT_DEFAULT['hidevthemesmenu'], 'select',
                0, 1, 0, 20, true, 'vthemes', 0);
		$c->add('fs_visual', NULL, 'fieldset', 0, 1, NULL, 0, true, 'vthemes', 0);
        $c->add('max_width_preview', $_VT_DEFAULT['max_width_preview'], 'text',
                0, 0, 0, 30, true, 'vthemes', 0);
		$c->add('columns', $_VT_DEFAULT['columns'], 'text',
                0, 0, 0, 40, true, 'vthemes', 0);
		$c->add('rows', $_VT_DEFAULT['rows'], 'text',
                0, 0, 0, 50, true, 'vthemes', 0);
		$c->add('disable_use_theme', $_VT_DEFAULT['disable_use_theme'], 'select',
                0, 1, 0, 60, true, 'vthemes', 0);
		$c->add('disable_download', $_VT_DEFAULT['disable_download'], 'select',
                0, 1, 0, 70, true, 'vthemes', 0);
		$c->add('leftblocks', $_VT_DEFAULT['leftblocks'], 'select',
                0, 1, 0, 100, true, 'vthemes', 0);
		
		//Block tab
		$c->add('tab_vthemes_block', NULL, 'tab', 0, 20, NULL, 0, true, 'vthemes', 10);
        $c->add('fs_block_settings', NULL, 'fieldset', 0, 10, NULL, 0, true, 'vthemes', 10);
        $c->add('block_enable', $_VT_DEFAULT['block_enable'], 'select', 
                0, 10, 0, 10, true, 'vthemes', 10);
        $c->add('block_isleft', $_VT_DEFAULT['block_isleft'], 'select', 
                0, 10, 0, 20, true, 'vthemes', 10);
        $c->add('block_order', $_VT_DEFAULT['block_order'], 'text',
                0, 10, 0, 30, true, 'vthemes', 10);
        $c->add('block_topic_option', $_VT_DEFAULT['block_topic_option'],'select',
                0, 10, 15, 40, true, 'vthemes', 10);  
        $c->add('block_topic', $_VT_DEFAULT['block_topic'], '%select',
                0, 10, NULL, 50, true, 'vthemes', 10);
    
        $c->add('fs_block_permissions', NULL, 'fieldset', 0, 20, NULL, 0, true, 'vthemes', 10);
        $new_group_id = 0;
        if (isset($_GROUPS['vthemes Admin'])) {
            $new_group_id = $_GROUPS['vthemes Admin'];
        } else {
            $new_group_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'vthemes Admin'");
            if ($new_group_id == 0) {
                if (isset($_GROUPS['Root'])) {
                    $new_group_id = $_GROUPS['Root'];
                } else {
                    $new_group_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'Root'");
                }
            }
        }         
        $c->add('block_group_id', $new_group_id,'select',
                0, 20, NULL, 10, TRUE, 'vthemes', 10);        
        $c->add('block_permissions', $_VT_DEFAULT['block_permissions'], '@select', 
                0, 20, 14, 20, true, 'vthemes', 10);
    }

    return true;
}

?>