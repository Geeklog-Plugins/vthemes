<?php
// +---------------------------------------------------------------------------+
// | Visual Theme Switcher v1 Geeklog Plugin                                   |
// +---------------------------------------------------------------------------+
// | $Id::                                                                    $|
// | This is the English language page for the vThemes Plugin                  |
// +---------------------------------------------------------------------------|
// | Copyright (C) 2002,2005,2006,2007 by the following authors:               |
// |                                                                           |
// | Author:                                                                   |
// | Mark R. Eavns               -    mevans@ecsnet.com                        |
// +---------------------------------------------------------------------------|
// |                                                                           |
// | If you translate this file, please consider uploading a copy at           |
// |    http://www.gllabs.org so others can benefit from your translation.     |
// |    Thanks You!                                                            |
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
// | along with this program; if not, write to the Free Software               |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA|
// |                                                                           |
// +---------------------------------------------------------------------------|

$LANG_VT00 = array (
    'menulabel'         => 'vThemes',
    'plugin'            => 'vthemes',
    'access_denied'     => 'Access Denied',
    'access_denied_msg' => 'You do not have the proper security privilege to access to this page.  Your user name and IP have been recorded.',
    'install_header'    => 'vThemes Plugin Install/Uninstall',
    'uninstalled'       => 'vThemes is Not Installed',
    'install_success'   => 'vThemes Installation Successful.  <br /><br />Please review the system documentation and also visit the  <a href="%s">administration section</a> to insure your settings correctly match the hosting environment.',
    'uninstall_msg'     => 'Plugin Successfully Uninstalled',
    'install'           => 'Install',
    'enabled'           => 'Disable plugin before uninstalling.',
    'warning'           => 'Warning! Plugin is still Enabled',
    'readme'            => 'vThemes Plugin Installation',
    'overview'          => 'vThemes is a Visual Theme Switcher for Geeklog.<br /><br />',
    'details'           => '',
    'vt_block_header'	=> 'Visual Theme Switcher',
    'preinstall_check'  => 'vThemes has the following requirements:',
    'geeklog_check'     => 'Geeklog v1.4.1 or greater, version reported is <b>%s</b>.',
    'php_check'         => 'PHP v4.3.0 or greater, version reported is <b>%s</b>.',
    'preinstall_confirm' => "For full details on installing vThemes, please refer to the <a href=\"http://www.geeklog.fr/cms/wiki/doku.php/plugins:vthemes\">Installation Manual</a>.",
    'use_theme'			=> 'Use Theme',
    'vtheme_header'		=> 'Visual Theme Switcher',
    'download_theme'	=> 'Download Theme',
    'author'			=> 'Author',
	'theme'				=> 'Theme',
	'version'			=> 'Version',
	'requires'			=> 'Requires',
	'copyright'			=> 'Copyright',
	'license'			=> 'License', 
	'description'		=> 'Description'
);

$LANG_VT01 = array(
	'active'			=> 'Current Active Theme: <em>%s</em>',
	'vt_block'			=> 'Test out available themes by selecting from one of the %d available themes or give the <a href="%s">Visual Switcher</a> a try:<br />',
	'use_permanently'	=> 'Use Permanently'
);

$PLG_vthemes_MESSAGE1 = 'vThemes Plugin successfully installed.';
$PLG_vthemes_MESSAGE2 = 'vThemes Plugin successfully upgraded.';
$PLG_vthemes_MESSAGE3 = 'vThemes Plugin upgrade failed - check error.log';

// Localization of the Admin Configuration UI
$LANG_configsections['vthemes'] = array(
    'label' => 'vthemes',
    'title' => 'vthemes Configuration'
);

$LANG_confignames['vthemes'] = array(
    'vthemesloginrequired' => 'vthemes Login Required?',
    'hidevthemesmenu' => 'Hide vthemes Menu Entry?',
    'max_width_preview' => 'Max width preview?',
    'columns' => 'How many columns?',
	'rows' => 'How many rows?',
	'disable_use_theme' => 'Disable use theme?',
	'disable_download' => 'Disable download?',
	'leftblocks' => 'Leftblocks?',
	'block_enable' => 'Enabled',
    'block_isleft' => 'Display Block on Left',
    'block_order' => 'Block Order',
    'block_topic_option' => 'Topic Options',
    'block_topic' => 'Topic',
    'block_group_id' => 'Group',
    'block_permissions' => 'Permissions'
);

$LANG_configsubgroups['vthemes'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_tab['vthemes'] = array(
    'tab_main' => 'General vthemes Settings',
    'tab_vthemes_block' => 'vthemes Block'
);

$LANG_fs['vthemes'] = array(
    'fs_access' => 'Access vthemes Settings',
    'fs_visual' => 'Visual Settings',
	'fs_block_settings' => 'Block Settings',
    'fs_block_permissions' => 'Block Permissions'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['vthemes'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE),
    14 => array('No access' => 0, 'Read-Only' => 2,'Read-Write' => 3),
	15 => array('All' => TOPIC_ALL_OPTION, 'Homepage Only' => TOPIC_HOMEONLY_OPTION, 'Select Topics' => TOPIC_SELECTED_OPTION)
);
?>
