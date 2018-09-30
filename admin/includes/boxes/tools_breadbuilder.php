<?php

    foreach ( $cl_box_groups as &$group ) {
        if ( $group['heading'] == BOX_HEADING_TOOLS ) {
            $group['apps'][] = [
                'code' => 'breadbuilder.php',
                'title' => MODULES_ADMIN_MENU_TOOLS_BREABBUILDER,
                'link' => tep_href_link('breadbuilder.php'),
            ];

            break;
        }
    }