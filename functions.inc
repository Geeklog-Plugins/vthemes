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

	if ( SEC_inGroup('Root')) {
		$admin = 1;
	} else {
		$admin = 0;
	}

	$glThemes = COM_getThemes();
	natcasesort($glThemes);
	$x = 0;
    $themes = array();
    if (!isset($_VT_CONF['exclude'])) {
        $_VT_CONF['exclude'] = '';
    }
	for ($i=0; $i < count($glThemes); $i++ )
	{
        if ($_VT_CONF['exclude'] == "") {
			$exclude= array ("", "");
			 if ( !in_array(current($glThemes),$exclude) || $admin ) {
                $themes[$x] = current($glThemes);
                $x++;
            }
        } else {
			if (!in_array(current($glThemes), $_VT_CONF['exclude']) || $admin ) {
                $themes[$x] = current($glThemes);
                $x++;
            }
        }
		next($glThemes);
	}

    if ($x == 1) {
        return 'Sorry, there is only one theme installed on this system';
    }

    $T = COM_newTemplate(CTL_plugin_templatePath('vthemes'));
    $T->set_file('block', 'block.thtml');

    $T->set_var('message', sprintf($LANG_VT01['vt_block'],$x, $_CONF['site_url'] . '/vthemes/index.php'));

    $select_options = '';
    for ($i = 1; $i <= count($themes); $i++) {
        $select_options .= '<option value="' . current($themes) . '"';
        if ($usetheme == current($themes)) {
            $select_options .= ' selected="SELECTED"';
        }

		// Get actual Theme name
		$themeName = current($themes);
		$func = 'theme_config_' . $themeName;
		require_once $_CONF['path_layout'] . 'functions.php';
		if (function_exists($func)) { // Function is required for a theme but lets check just in case
			$theme_config = $func();		
			if (!empty(trim($theme_config['theme_name']))) {
				$themeName = $theme_config['theme_name'];
			}
		}		
		
        $select_options .= '>' . $themeName . '</option>';
        next($themes);
    }
    $T->set_var('select_options', $select_options);

    $retval = $T->finish($T->parse('output', 'block'));

    return $retval;
}
?>
