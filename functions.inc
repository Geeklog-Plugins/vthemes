<?php
// +---------------------------------------------------------------------------+
// | Visual Theme Switcher v1.5.1 Geeklog Plugin                                   |
// +---------------------------------------------------------------------------+
// | $Id::                                                                    $|
// | Geeklog integration functions.                                            |
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

// this file can't be used on its own
if (strpos ($_SERVER['PHP_SELF'], 'functions.inc') !== false)
{
    die ('This file can not be used on its own.');
}

$plugin_path = $_CONF['path'] . 'plugins/vthemes/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) {
    include_once ($langfile);
} else {
    require_once $plugin_path . 'language/english.php';
}

/*
* Check and see if we need to load the plugin configuration
*/
if (!isset($_VT_CONF['themesloginrequired'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $vt_config = config::get_instance();
    $_VT_CONF = $vt_config->get_config('vthemes');
}


// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* @return   mixed   menu entry, or boolean false if disabled / hidden
*
*/
function plugin_getmenuitems_vthemes()
{
    global $_CONF, $_USER, $_VT_CONF, $LANG_VT00;

    $anon = (empty ($_USER['uid']) || ($_USER['uid'] <= 1)) ? true : false;
    if (($_VT_CONF['hidevthemesmenu'] == 1) || ($anon &&
                ($_CONF['loginrequired'] || $_VT_CONF['vthemesloginrequired']))) {
        return false;
    }

    $menuitems[$LANG_VT00['menulabel']] = $_CONF['site_url'] . '/vthemes/index.php';

    return $menuitems;
}

function plugin_getheadercode_vthemes()
{
    global $_CONF, $_VT_CONF, $_SCRIPTS;

	$_SCRIPTS->setCSSFile('vthemes_css', '/vthemes/css/style.css');
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_vthemes ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array(),
        /* give the full name of the group, as in the db */
        'groups' => array('vthemes Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('vthemes.admin'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array('phpblock_vthemeswitcher'),
        /* give all vars with their name */
        'vars'=> array()
    );
    return $out;
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different than the version installed and registered currently.
* If newer then you may want to run the update
*
* @return   string  version number
*
*/

function plugin_chkVersion_vthemes()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/vthemes/autoinstall.php';

    $inst_parms = plugin_autoinstall_vthemes('vthemes');

    return $inst_parms['info']['pi_version'];
}

function plugin_upgrade_vthemes()
{
    global $_TABLES, $_CONF;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version', "pi_name = 'vthemes'");
    $code_version = plugin_chkVersion_vthemes();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/vthemes/autoinstall.php';

    if (!plugin_compatible_with_this_version_vthemes('vthemes')) {
        return 3002;
    }

    // Retrieve other plugin info like min geeklog version and homepage
    $inst_parms = plugin_autoinstall_vthemes('vthemes');

    require_once $_CONF['path'] . 'plugins/vthemes/install_updates.php';

    $current_version = $installed_version;
    $done = false;
    $current_config = false;
    while (!$done) {
        switch ($current_version) {
            case '1.0.0' :
                $current_version = '1.0.1';
                break;

            case '1.0.1' :
                $current_version = '1.0.2';
                break;

            case '1.0.2' :
                DB_query("UPDATE {$_TABLES['plugins']} SET pi_version='1.5.1',pi_homepage='http://www.geeklog.fr',pi_gl_version='1.5.0' WHERE pi_name='vthemes' LIMIT 1");

                $current_version = '1.5.1.1';
                break;

            case '1.5.1.1':
                $current_version = '1.5.2';
                break;

            case '1.5.2':
                $current_version = '1.5.2.1';
                break;

            case '1.5.2.1':
                $current_version = '1.5.3';
                break;

            case '1.5.3':
                vthemes_update_ConfValues_1_5_3();

                $current_version = '1.6.0';
                break;
        }
    }

    // update plugin version number and other info
    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '{$inst_parms['info']['pi_gl_version']}', pi_homepage = '{$inst_parms['info']['pi_homepage']}' WHERE pi_name = 'vthemes'");

    COM_errorLog( "Updated vthemes plugin from v$installed_version to v$code_version", 1 );

	return true;
}

/**
* Gets Geeklog blocks from plugins
*
* Returns data for blocks on a given side and, potentially, for
* a given topic.
*
* @param    string  $side   Side to get blocks for (right or left for now)
* @param    string  $topic  Only get blocks for this topic
* @return   array           array of block data
* @link     http://wiki.geeklog.net/index.php/Dynamic_Blocks
*
*/
function plugin_getBlocks_vthemes($side, $topic='')
{
    global $_TABLES, $_CONF, $_VT_CONF, $LANG_VT00;

    $retval = array();

    $owner_id = SEC_getDefaultRootUser();

    // Check permissions first
    if ($_VT_CONF['block_enable'] && SEC_hasAccess($owner_id, $_VT_CONF['block_group_id'], $_VT_CONF['block_permissions'][0], $_VT_CONF['block_permissions'][1], $_VT_CONF['block_permissions'][2], $_VT_CONF['block_permissions'][3])) {
        // Check if right topic
        if (($_VT_CONF['block_topic_option'] == TOPIC_ALL_OPTION) || ($_VT_CONF['block_topic_option'] == TOPIC_HOMEONLY_OPTION && COM_onFrontpage()) || ($_VT_CONF['block_topic_option'] == TOPIC_SELECTED_OPTION && in_array($topic, $_VT_CONF['block_topic']))) {
            if (($side=='left' && $_VT_CONF['block_isleft'] == 1) || ($side=='right' && $_VT_CONF['block_isleft'] == 0)) {
                // Create a block
                $display = phpblock_vthemeswitcher();

                $retval[] = array('name'           => 'vthemes',
                                  'type'           => 'dynamic',
                                  'onleft'         => $_VT_CONF['block_isleft'],
                                  'title'          => $LANG_VT00['vt_block_header'],
                                  'blockorder'     => $_VT_CONF['block_order'],
                                  'content'        => $display,
                                  'allow_autotags' => false,
                                  'convert_newlines' => false,
                                  'help'           => '');
            }
        }
    }

    return $retval;
}


/**
* Gets config information for dynamic blocks from plugins
*
* Returns data for blocks on a given side and, potentially, for
* a given topic.
*
* @param    string  $side   Side to get blocks for (right or left for now)
* @param    string  $topic  Only get blocks for this topic
* @return   array           array of block data
* @link     http://wiki.geeklog.net/index.php/Dynamic_Blocks
*
*/
function plugin_getBlocksConfig_vthemes($side, $topic='')
{
    global $_TABLES, $_CONF, $_VT_CONF, $LANG_VT00;

    $retval = array();

    $owner_id = SEC_getDefaultRootUser();

    // Check permissions first
    if (SEC_hasAccess($owner_id, $_VT_CONF['block_group_id'], $_VT_CONF['block_permissions'][0], $_VT_CONF['block_permissions'][1], $_VT_CONF['block_permissions'][2], $_VT_CONF['block_permissions'][3])) {
        if (($side=='left' && $_VT_CONF['block_isleft'] == 1) || ($side=='right' && $_VT_CONF['block_isleft'] == 0)) {
            $retval[] = array('plugin'         => $LANG_VT00['plugin'],
                              'name'           => 'vthemes',
                              'title'          => $LANG_VT00['vt_block_header'],
                              'type'           => 'dynamic',
                              'onleft'         => $_VT_CONF['block_isleft'],
                              'blockorder'     => $_VT_CONF['block_order'],
                              'allow_autotags' => false,
                              'help'           => '',
                              'enable'         => $_VT_CONF['block_enable'],
                              'topic_option'   => $_VT_CONF['block_topic_option'],
                              'topic'          => $_VT_CONF['block_topic'],
                              'inherit'        => array()
                              );
        }
    }

    return $retval;
}

function phpblock_vthemeswitcher()
{
    global $_TABLES, $_USER, $_CONF, $_VT_CONF, $LANG_VT00, $LANG_VT01;

    // Grab them variable that Geeklog has already checked if theme change requested
    $usetheme = $_CONF['theme'];

    if ( isset($_USER['uid']) && $_USER['uid'] > 1 && !empty($usetheme) ) {
        DB_query("UPDATE {$_TABLES['users']} SET theme='$usetheme' WHERE uid = {$_USER['uid']}");
    }

	if (SEC_inGroup('Root') || SEC_hasRights('vthemes.admin')) {
		$admin = 1;
	} else {
		$admin = 0;
	}

    $T = COM_newTemplate(CTL_plugin_templatePath('vthemes'));
    $T->set_file('block', 'block.thtml');

	// *************************************************************
	// Once Geeklog 2.2.2 is released replace with COM_getThemes
	$themes = vThemes_COM_getThemes(false, true, true);
	
	$T->set_var('message', sprintf($LANG_VT01['vt_block'], count($themes), $_CONF['site_url'] . '/vthemes/index.php'));

	$items = '';
	foreach ($themes as $key => $theme) {
		if (isset($_VT_CONF['exclude']) && is_array($_VT_CONF['exclude'])) {
			if (!in_array($key, $_VT_CONF['exclude']) || $admin ) {
				$excluded = false;
			} else {
				$excluded = true;
			}
		} else {
			$excluded = false;
		}
		
		if (!$excluded) {
			$items .= '<option value="' . $key . '"';
			if ($usetheme == $key) {
				$items .= ' selected="selected"';
			}

			$items .= '>' . $theme['theme_name'] . '</option>' . LB;
		}
	}
	
	$T->set_var('select_options', $items);

    $retval = $T->finish($T->parse('output', 'block'));

    return $retval;
}

// ********************************************************************************************************************************
// NOTE: Once Geeklog 2.2.2 released remove these functions and replace calls with COM_getThemeInfo and COM_getThemes
// This is done to support Geeklog 2.2.1 which these functions have not been updated or exist

/**
 * Gets all installed themes
 * Returns a list of all the directory names in $_CONF['path_themes'], i.e.
 * a list of all the theme names.
 *
 * @param    boolean $all if true, return all themes even if users aren't allowed to change their default themes
 * @param    boolean $valid if true, return all valid themes only
 * @param    boolean $info if true, return all theme config info as well
 * @return   array        All installed themes. If $info true then theme folder name is used as an index for the array followed by the configuration (as an array)
 */
function vThemes_COM_getThemes($all = false, $valid = true, $info = false)
{
    global $_CONF;

    $index = 1;
    $themes = array();

    // If users aren't allowed to change their theme then only return the default theme

    if (($_CONF['allow_user_themes'] == 0) && !$all) {
        if ($info) {
            $themes[$dir] = vThemes_COM_getThemeInfo($_CONF['theme']);
        } else {
            $themes[$index] = $_CONF['theme'];
        }
    } else {
        $fd = opendir($_CONF['path_themes']);

        while (($dir = @readdir($fd)) !== false) {
            if (is_dir($_CONF['path_themes'] . $dir) && ($dir !== '.') && ($dir !== '..') &&
                ($dir !== 'CVS') && (substr($dir, 0, 1) !== '.')
            ) {
                clearstatcache();
                if ($valid) {
                    if (COM_validateTheme($dir)) {
                        if ($info) {
                            $themes[$dir] = vThemes_COM_getThemeInfo($dir);
                        } else {
                            $themes[$index] = $dir;
                        }
                    }
                } else {
                    if ($info) {
                        $themes[$dir] = vThemes_COM_getThemeInfo($dir);
                    } else {
                        $themes[$index] = $dir;
                    }
                }
                $index++;
            }
        }
    }

    return $themes;
}

/**
 * Gets theme info if found
 *
 * @param    string theme   id of theme (which is also the directory name that should be located in the layout folder)
 * @return   array        All installed themes
 */
function vThemes_COM_getThemeInfo($theme)
{
    global $_CONF;

    $theme_config = array();

    if (!empty($theme)) {
        $temp_path_layout = $_CONF['path_themes'] . $theme . '/';
        if (file_exists($temp_path_layout . 'functions.php')) {
            require_once $temp_path_layout . 'functions.php';
            $func = 'theme_config_' . $theme;
            if (is_callable($func)) {
                $theme_config = $func();

                // Make sure theme name exists else could really cause problems
                if (isset($theme_config['theme_name'])) {
                    if (empty(trim($theme_config['theme_name']))) {
                        $theme_config['theme_name'] = $theme;
                    }
                } else {
                    $theme_config['theme_name'] = $theme;
                }

                $currentThemePath = $_CONF['path_themes'] . $theme . '/';
                $previewImage = $theme . '.png';
                if (!file_exists($currentThemePath . $previewImage)) {
                    $previewImage = $theme . '.jpg';
                    if (!file_exists($currentThemePath . $previewImage)) {
                        $previewImage = '';
                    }
                }
                if (!empty($previewImage)) {
                    $theme_config['theme_preview_image'] = $previewImage;
                }
            }
        }
    }

    return $theme_config;
}


?>
