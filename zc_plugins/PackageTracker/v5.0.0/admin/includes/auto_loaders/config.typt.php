<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.
//
// Last updated 20210301-lat9 for v4.0.0
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// -----
// Load, and instantiate, the plugin's admin observer.
//
$autoLoadConfig[71][] = [
    'autoType' => 'class',
    'loadFile' => 'observers/TyPackageTrackerAdminObserver.php',
    'classPath' => DIR_WS_CLASSES
];
$autoLoadConfig[71][] = [
    'autoType' => 'classInstantiate',
    'className' => 'TyPackageTrackerAdminObserver',
    'objectName' => 'typt'
];

$autoLoadConfig[999][] = [
    'autoType' => 'init_script',
    'loadFile' => 'init_typt_config.php'
];
