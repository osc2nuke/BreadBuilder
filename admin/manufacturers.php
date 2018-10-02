<?php

    require('includes/application_top.php');
    $OSCOM_Hooks->register('bread');
    require('includes/template_top.php');
    
    echo $oscTemplate->getContent('bread');

    require('includes/template_bottom.php');
    require('includes/application_bottom.php');