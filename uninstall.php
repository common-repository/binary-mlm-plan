<?php

//this check makes sure that this file is called manually.
if (!defined("WP_UNINSTALL_PLUGIN")) 
    exit();

//put plugin uninstall code here

include_once dirname(__FILE__) . '/includes/classs-bmp-uninstaller.php';

$uninstall= new bmp_Uninstall;
$uninstall->uninstall();
