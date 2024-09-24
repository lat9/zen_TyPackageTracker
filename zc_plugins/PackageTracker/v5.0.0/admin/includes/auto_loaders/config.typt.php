<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.
//
// Last updated: v5.0.0
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
