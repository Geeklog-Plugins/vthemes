<?php
// +---------------------------------------------------------------------------+
// | Visual Theme Switcher v1 Geeklog Plugin                                   |
// +---------------------------------------------------------------------------+
// | $Id:: index.php 1386 2007-12-04 03:35:03Z mevans0263                     $|
// | index.php                                                                 |
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
require_once('../lib-common.php');

$T = COM_newTemplate(CTL_plugin_templatePath('vthemes'));
$T->set_file('page','tchooser.thtml');

if (isset($_GET['page'])) {
    $page = COM_applyFilter($_GET['page'], true);
	if ($page < 1) {
		COM_handle404($_CONF['site_url'] . '/vthemes/index.php');
	}
} else {
	// Only time page can be 0 if it is not set
    $page = 0;
}
if ( $page != 0 ) {
    $page = $page - 1;
}

if ( isset($_VT_CONF['override_columns']) ) {
	$columns_per_page   = $_VT_CONF['override_columns'];
} else {
	$columns_per_page   = $_VT_CONF['columns'];
}

if ($columns_per_page == 1) {
    $tdwidth = 'width:100%;';
} else if ($columns_per_page == 2) {
    $tdwidth = 'width:50%;';
} else if ($columns_per_page == 3) {
    $tdwidth = 'width:33%;';
} else if ($columns_per_page == 4) {
    $tdwidth = 'width:25%;';
} else {
    $tdwidth = '';
}

$rows_per_page = $_VT_CONF['rows'];
$items_per_page = $columns_per_page * $rows_per_page;


if ( SEC_inGroup('Root')) {
	$admin = 1;
} else {
	$admin = 0;
}

// Get Current Theme name for page
// *************************************************************
// Once Geeklog 2.2.2 is released replace with COM_getThemeInfo
$theme = vThemes_COM_getThemeInfo($_CONF['theme']);
$tmsg = sprintf($LANG_VT01['active'], $theme['theme_name']);
unset($theme);
$T->set_var('tmsg', $tmsg);

// Retrieve all valid themes
// *************************************************************
// Once Geeklog 2.2.2 is released replace with COM_getThemes
$themes = vThemes_COM_getThemes(false, true, true);

$total_themes = count($themes);
$total_pages = ceil($total_themes/($items_per_page));

if ( $page >= $total_pages ) {
	COM_handle404($_CONF['site_url'] . '/vthemes/index.php');
}

$start = $page * $items_per_page;

$current_print_page = ( floor( $start / $items_per_page ) + 1 );
$total_print_pages  = ceil($total_themes/($items_per_page));

if ( $current_print_page == 0 ) {
    $current_print_page = 1;
}

if ( $total_print_pages == 0 ) {
    $total_print_pages = 1;
}

if ( $total_themes > 0 ) {
    $k = 0;

	$T->set_block('page', 'themeColumn', 'tColumn');
	$T->set_block('page', 'themeRow', 'tRow');

    for ( $i = $start; $i < ($start + $items_per_page ); $i += $columns_per_page ) {
        for ($j = $i; $j < ($i + $columns_per_page); $j++) {
            if ($j >= $total_themes) {
                $k = ($i+$columns_per_page) - $j;
                $m = $k % $columns_per_page;
                $T->set_var(array(
                    'themeDisplay'  =>  '',
					'tdwidth'       => $tdwidth,
                ));

                $T->parse('tColumn','themeColumn',true);
                break;
            }
            
			$themeFolder = key($themes);
			$theme = current($themes);
			
			$V = COM_newTemplate(CTL_plugin_templatePath('vthemes'));
			$V->set_file('theme','theme.thtml');

			if (isset($theme['theme_preview_image']) AND !empty($theme['theme_preview_image'])) {
				$previewImage = "/layout/$themeFolder/" . $theme['theme_preview_image'];
			} else {
				$previewImage = '';
			}
			
			// Retrieve theme config options
			$themeDescription = '';
			$themeDescNoHTML  = '';
			$themeHomepage	  = '';
			$themeAuthor	  = '';
			$themeAuthorUrl	  = '';
			$themeDownloadUrl = '';
			$themeVersion	  = '';
			$themeGLVersion	  = '';
			$themeCopyright	  = '';
			$themeLicense	  = '';			
			
				
			$themeName	      = (isset($theme['theme_name']) ? $theme['theme_name'] : $themeFolder);
			
			$themeDescription = (isset($theme['theme_description']) ? $theme['theme_description'] : '');
			if (!empty(trim($themeDescription))) {
				$themeDescNoHTML = preg_match('/<.*>/', $themeDescription); // Will = 0 if no HTML tags
				// Update variable to now indicate if html found or not
				if ($themeDescNoHTML > 0) {
					$themeDescNoHTML = '';
				} else {
					$themeDescNoHTML = 1; 
				}
				if ($themeDescNoHTML) {
					$themeDescription = COM_nl2br($themeDescription);
				}
			}
			
			$themeHomepage	  = (isset($theme['theme_homepage']) ? $theme['theme_homepage'] : '');
			
			$themeAuthor	  = (isset($theme['theme_author']) ? $theme['theme_author'] : '');
			$themeAuthorUrl	  = (isset($theme['theme_author_url']) ? $theme['theme_author_url'] : '');
			
			if ($_VT_CONF['disable_download'] != 1) {
				$themeDownloadUrl 	  = (isset($theme['theme_download_url']) ? $theme['theme_download_url'] : '');
			}
			
			$themeVersion   = (isset($theme['theme_version']) ? $theme['theme_version'] : '');
			$themeGLVersion   = (isset($theme['theme_gl_version']) ? $theme['theme_gl_version'] : '');
			$themeCopyright   = (isset($theme['theme_copyright']) ? $theme['theme_copyright'] : '');
			$themeLicense     = (isset($theme['theme_license']) ? $theme['theme_license'] : '');

			$V->set_var(array(
				'lang_author'           => $LANG_VT00['author'],
				'lang_use_theme'    => $LANG_VT00['use_theme'],
				'lang_theme'		=> $LANG_VT00['theme'],
				'lang_version'		=> $LANG_VT00['version'],
				'lang_requires'		=> $LANG_VT00['requires'],
				'lang_copyright'	=> $LANG_VT00['copyright'],
				'lang_license'		=> $LANG_VT00['license'],
				'lang_description'	=> $LANG_VT00['description'],
				
				'php_self'	    	=> $_SERVER['PHP_SELF'],
				'themeId'	    	=> 'theme' . $i,
				'themeFolder'    	=> $themeFolder,
				'themeTitle'	    => $themeName,
				'themeDescription'  => $themeDescription,
				'themeDescNoHTML'   => $themeDescNoHTML,
				'themeHomepage'		=> $themeHomepage,
				'themeAuthor'		=> $themeAuthor,
				'themeAuthorUrl'	=> $themeAuthorUrl,
				'themeDownloadUrl'	=> $themeDownloadUrl,
				'lang_download' 	=> $LANG_VT00['download_theme'],
				'themeVersion'		=> $themeVersion,
				'themeGLVersion'	=> $themeGLVersion,
				'themeCopyright'	=> $themeCopyright,
				'themeLicense'		=> $themeLicense,
				
				'themeUse'         => ( $_VT_CONF['disable_use_theme'] != 1 ? '1' : '') ,
                'themePreviewImage'   => $previewImage,
				'previewImageWidth' => $_VT_CONF['max_width_preview'], 
			));
			
			$V->parse('output','theme');
			$themeDisplay = $V->finish($V->get_var('output'));

            $T->set_var(array(
                'themeDisplay'  =>  $themeDisplay,
				'tdwidth'       => $tdwidth,
            ));
			$T->parse('tColumn','themeColumn',true);
			
			$theme = next($themes);
		}
        $T->parse('tRow','themeRow',true);
        $T->set_var('tColumn','');
		
		if ($j >= $total_themes) {
			break;
		}
	}
}

$T->set_var(array(
    'bottom_pagination'     => COM_printPageNavigation($_CONF['site_url'] . '/vthemes/index.php', $page+1,ceil($total_themes  / $items_per_page)),
    'title_row'				=> $LANG_VT00['vtheme_header'],
));

$display = $T->finish($T->parse('output','page'));

($_VT_CONF['leftblocks'] == 1) ? $html_infos['what'] = 'menu' : $html_infos['what'] = 'none';

//Output
COM_output(COM_createHTMLDocument($display, $html_infos));
?>
