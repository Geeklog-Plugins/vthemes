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

// No need to worry about if theme changed as lib-common figures this out already
$tmsg = $LANG_VT01['active']; 

$T->set_var('tmsg',$tmsg);

if (isset($_GET['page'])) {
    $page = COM_applyFilter($_GET['page'],true);
} else {
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

$glThemes = COM_getThemes(1);
natcasesort($glThemes);

$x = 0;
for ($i=0; $i < count($glThemes); $i++ ) {
	$themes[$x] = current($glThemes);
	$x++;
	next($glThemes);
}

$total_themes = count($themes);
$total_pages = ceil($total_themes/($items_per_page));

if ( $page >= $total_pages ) {
    $page = $total_pages - 1;
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
            if ($j >= $total_themes)
            {
                $k = ($i+$columns_per_page) - $j;
                $m = $k % $columns_per_page;
                $T->set_var(array(
                    'themeDisplay'  =>  '',
					'tdwidth'       => $tdwidth,
                ));

                $T->parse('tColumn','themeColumn',true);
                break;
            }
            $themeOffset = $j;
			$V = COM_newTemplate(CTL_plugin_templatePath('vthemes'));
			$V->set_file('theme','theme.thtml');

			$previewImageFull = '/layout/' . $themes[$themeOffset] . '/' . $themes[$themeOffset] . '.png';
			if ( !file_exists($_CONF['path_html'] . $previewImageFull) ) {
				$previewImageFull = '/layout/' . $themes[$themeOffset] . '/' . $themes[$themeOffset] . '.jpg';
				if ( !file_exists($_CONF['path_html'] . $previewImageFull) ) {
					$previewImageFull = '';
				}
			}

			$previewImage = '/layout/' . $themes[$themeOffset] . '/' . $themes[$themeOffset] . '.png';

			if ( !file_exists($_CONF['path_html'] . $previewImage) ) {
				$previewImage = '/layout/' . $themes[$themeOffset] . '/' . $themes[$themeOffset] . '.jpg';
				if ( !file_exists($_CONF['path_html'] . $previewImage) ) {
					if ( $previewImageFull != '' ) {
						$previewImage = $previewImageFull;
					} else {
						$previewImage = '/vthemes/images/nopreview.png';
					}
				}
			}

			$previewImageWidth = $_VT_CONF['max_width_preview'];
            $previewImageHeight = 0;

			//
			// process theme ini file, if it exists...
			//
			unset($themeData);
			$themeData = array();
			$themeData = @parse_ini_file ( $_CONF['path_html'] . '/layout/' . $themes[$themeOffset] . '/' . 'theme.ini' );
			$themeAuthor	  = '';
			$themeURL		  = '';
			$themeDownload    = '';
			$themeDownloadEnd = '';
			$lang_download	  = '';
			$themeDescription = '';
			$themeName        = $themeData['name'];

			if ( isset ($themeData['name']) && $themeData['name'] == $themes[$themeOffset] ) {
			    $themeName	      = (isset($themeData['display_name']) ? $themeData['display_name'] : $themeData['name']);
				$themeAuthor	  = (isset($themeData['author']) ? $themeData['author'] : '');
				$themeURL		  = (isset($themeData['url']) ? $themeData['url'] : '');
				$themeDownload    = (isset($themeData['download']) ? '<a href="' . $themeData['download'] . '">' : '');
				$themeDownloadEnd = (isset($themeData['download']) ? '</a>' : '');
				$lang_download	  = (isset($themeData['download']) ? $LANG_VT00['download_theme'] : '');
				if ( $themeData['download'] == "#" ) {
				$lang_download = '';
				$themeDownload = '';
				$themeDownloadEnd = ''; }
				$themeDescription = (isset($themeData['description']) ? $themeData['description'] : '');
			} else {
			    $themeName = $themes[$themeOffset];
			}

			if ( $_VT_CONF['disable_download'] == 1 ) {
				$lang_download = '';
				$themeDownload = '';
				$themeDownloadEnd = '';
			}

			$V->set_var(array(
				'by'                => $LANG_VT00['by'],
				'themeAuthor'		=> $themeAuthor,
				'themeURL'			=> $themeURL,
				'themeDownload'		=> $themeDownload,
				'themeDownloadEnd' 	=> $themeDownloadEnd,
				'lang_download' 	=> $lang_download,
			));

			if ($themeAuthor == '') $V->set_var('by','');

			$borderWidth = $previewImageWidth+15;

			$V->set_var(array(
				'themeTitle'	   => $themeName,
				'themeUse'         => ( $_VT_CONF['disable_use_theme'] != 1 ? '<form name="theme' . $i . '" method="post" action="' . $_SERVER['PHP_SELF'] . '"><input type="hidden" name="gl-usetheme" value="' . $themes[$themeOffset] . '"><input type="submit" name="use" value="' . $LANG_VT00['use_theme'] . '"></form>' : '') ,
				//'themeThumbnail'   => $_CONF['site_url'] . '/vthemes/timthumb.php?src=' . $_CONF['site_url'] . $previewImage . '&amp;w=' . $_VT_CONF['max_width_preview'] . '&amp;zc=1&amp;q=90',
                'themeThumbnail'   => $_CONF['site_url'] . $previewImage,
				'themeLigtBox'     => '<a class="lightbox" href="' . $_CONF['site_url'] . '/vthemes/timthumb.php?src=' .  $_CONF['site_url'] . $previewImageFull . '&amp;w=' . (4*$_VT_CONF['max_width_preview']) . '&amp;zc=1&amp;q=90' . '" title="' . $themes[$themeOffset] . '">',
				'borderWidth'      => $previewImageWidth + 15,
				'row_height'	   => $previewImageHeight + 40,
      			'themeDescription' => $themeDescription,
			));
			if ( $previewImageFull != '' ) {
				$previewImageFullSize = @getimagesize($_CONF['path_html'] . $previewImageFull);
			} else {
				$previewImageFullSize = false;
			}

			if ( $previewImageFullSize != false ) {
				$V->set_var('themeLightBox', '<a class="lightbox" href="' . $_CONF['site_url'] . '/vthemes/timthumb.php?src=' .  $_CONF['site_url'] . $previewImageFull . '&amp;w=' . (800) . '&amp;zc=1&amp;q=90' . '" title="' . $themes[$themeOffset] . '">');
				$V->set_var('themeLightBoxEnd','</a>');
			} else {
				$V->set_var('themeLightBox','');
				$V->set_var('themeLightBoxEnd','');
			}

			$V->parse('output','theme');
			$themeDisplay = $V->finish($V->get_var('output'));

            $T->set_var(array(
                'themeDisplay'  =>  $themeDisplay,
				'tdwidth'       => $tdwidth,
            ));
			$T->parse('tColumn','themeColumn',true);
		}
        $T->parse('tRow','themeRow',true);
        $T->set_var('tColumn','');
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
