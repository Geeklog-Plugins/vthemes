<?php

function vthemes_update_ConfValues_1_5_3()
{
    global $_VT_CONF, $_VT_DEFAULT, $_GROUPS, $_TABLES;

    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $c = config::get_instance();

    require_once $_CONF['path'] . 'plugins/vthemes/install_defaults.php';

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

    return true;
}

?>