<?php
// -----
// Admin-level installation script for the "encapsulated" Ty Package Tracker plugin for Zen Cart, by lat9.
// Copyright (C) 2018-2025, Vinos de Frutas Tropicales.
//
// Last updated: v5.0.2
//
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    private string $configGroupTitle = 'Ty Package Tracker';

    protected function executeInstall()
    {
        if ($this->nonEncapsulatedVersionPresent() === true) {
            $this->errorContainer->addError('error', ZC_PLUGIN_TYPT_INSTALL_REMOVE_PREVIOUS, true);
            return false;
        }

        // -----
        // First, determine the configuration-group-id and install the settings.
        //
        $cgi = $this->getOrCreateConfigGroupId(
            $this->configGroupTitle,
            $this->configGroupTitle . ' Settings'
        );

        $sql =
            "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
             VALUES
                ('Package Tracking - Carrier 1 Status', 'CARRIER_STATUS_1', 'False', 'Enable Tracking for Carrier 1.<br><br>Set to False if you do NOT want Carrier 1 to be displayed on the Admin Order Details or Customer Order Status pages.', $cgi, 90, now(), NULL, 'zen_cfg_select_option([\'True\', \'False\'],'),

                ('Package Tracking - Carrier 1 Name', 'CARRIER_NAME_1', 'FedEx', 'Enter name of Carrier 1.<br><br><strong>Example:</strong> FedEx, UPS, USPS, Canada Post, Royal Mail, etc...<br>(default: FedEx)', $cgi, 95,  now(), NULL, NULL),

                ('Package Tracking - Carrier 1 Tracking Link', 'CARRIER_LINK_1', 'https://www.fedex.com/Tracking?action=track&tracknumbers=', 'Enter the tracking link of Carrier 1<br><br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 100, now(), NULL, NULL),

                ('Package Tracking - Carrier 2 Status', 'CARRIER_STATUS_2', 'False', 'Enable Tracking for Carrier 2.<br><br>Set to False if you do NOT want Carrier 2 to be displayed on the Admin Order Details or Customer Order Status pages.', $cgi, 105, now(), NULL, 'zen_cfg_select_option([\'True\', \'False\'],'),

                ('Package Tracking - Carrier 2 Name', 'CARRIER_NAME_2', 'UPS', 'Enter name of Carrier 2.<br><br><strong>Example:</strong> FedEx, UPS, USPS, Canada Post, Royal Mail, etc...<br>(default: UPS)', $cgi, 110,  now(), NULL, NULL),

                ('Package Tracking - Carrier 2 Tracking Link', 'CARRIER_LINK_2', 'https://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=', 'Enter the tracking link of Carrier 2<br><br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 115, now(), NULL, NULL),

                ('Package Tracking - Carrier 3 Status', 'CARRIER_STATUS_3', 'False', 'Enable Tracking for Carrier 3.<br><br>Set to False if you do NOT want Carrier 3 to be displayed oon the Admin Order Details or Customer Order Status pages.', $cgi, 120, now(), NULL, 'zen_cfg_select_option([\'True\', \'False\'],'),

                ('Package Tracking - Carrier 3 Name', 'CARRIER_NAME_3', 'USPS', 'Enter name of Carrier 3.<br><br><strong>Example:</strong> FedEx, UPS, USPS, Canada Post, Royal Mail, etc...<br>(default: USPS)', $cgi, 125,  now(), NULL, NULL),

                ('Package Tracking - Carrier 3 Tracking Link', 'CARRIER_LINK_3', 'https://tools.usps.com/go/TrackConfirmAction!input.action?tLabels=', 'Enter the tracking link of Carrier 3<br><br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 130, now(), NULL, NULL),

                ('Package Tracking - Carrier 4 Status', 'CARRIER_STATUS_4', 'False', 'Enable Tracking for Carrier 4.<br><br>Set to False if you do NOT want Carrier 4 to be displayed on the Admin Order Details or Customer Order Status pages.', $cgi, 135, now(), NULL, 'zen_cfg_select_option([\'True\', \'False\'],'),

                ('Package Tracking - Carrier 4 Name', 'CARRIER_NAME_4', 'Canada Post', 'Enter name of Carrier 4.<br><br><strong>Example:</strong> FedEx, UPS, USPS, Canada Post, Royal Mail, etc...<br>(default: Canada Post)', $cgi, 140,  now(), NULL, NULL),

                ('Package Tracking - Carrier 4 Tracking Link', 'CARRIER_LINK_4', 'https://www.canadapost-postescanada.ca/track-reperage/en#/search?searchFor=', 'Enter the tracking link of Carrier 4.<br><br><strong>Example Canada Post:</strong> https://www.canadapost-postescanada.ca/track-reperage/en#/search?searchFor=', $cgi, 145, now(), NULL, NULL),

                ('Package Tracking - Carrier 5 Status', 'CARRIER_STATUS_5', 'False', 'Enable Tracking for Carrier 5.<br><br>Set to False if you do NOT want Carrier 5 to be displayed on the Admin Order Details or Customer Order Status pages.', $cgi, 150, now(), NULL, 'zen_cfg_select_option([\'True\', \'False\'],'),

                ('Package Tracking - Carrier 5 Name', 'CARRIER_NAME_5', 'Royal Mail', 'Enter name of Carrier 5.<br><br><strong>Example:</strong> FedEx, UPS, USPS, Canada Post, Royal Mail, etc...<br>(default: Royal Mail)', $cgi, 155,  now(), NULL, NULL),

                ('Package Tracking - Carrier 5 Tracking Link', 'CARRIER_LINK_5', 'https://www.royalmail.com/portal/rm/track?trackNumber=', 'Enter the tracking link of Carrier 5.<br><br><strong>Example Royal Mail:</strong> https://www.royalmail.com/portal/rm/track?trackNumber=', $cgi, 160, now(), NULL, NULL),

                ('Max display for Track Order sidebox', 'MAX_DISPLAY_PRODUCTS_IN_TRACK_ORDERS_BOX', '3', 'The maximum number of orders to display on the Track Order sidebox.', $cgi, 170, now(), NULL, NULL)";
        $this->executeInstallerSql($sql);

        // -----
        // Add columns to the orders_status_history table, one for each 'track_id'.
        //
        global $sniffer;
        if (!$sniffer->field_exists(TABLE_ORDERS_STATUS_HISTORY, 'track_id1')) {
            $this->executeInstallerSql(
                "ALTER TABLE " . TABLE_ORDERS_STATUS_HISTORY . "
                    ADD track_id1 varchar(191) default NULL,
                    ADD track_id2 varchar(191) default NULL,
                    ADD track_id3 varchar(191) default NULL,
                    ADD track_id4 varchar(191) default NULL,
                    ADD track_id5 varchar(191) default NULL"
            );
        }

        // -----
        // Add the plugin's configuration menu to the admin's menus.
        //
        if (!zen_page_key_exists('configTyPackageTracker')) {
            zen_register_admin_page('configTyPackageTracker', 'BOX_CONFIGURATION_TY_PACKAGE_TRACKER', 'FILENAME_CONFIGURATION', "gID=$cgi", 'configuration', 'Y');
        }

        // -----
        // If a previous (non-encapsulated) version of the plugin is currently installed,
        // perform any version-specific updates needed.
        //
        if (defined('TY_TRACKER_VERSION')) {
            $this->updateFromNonEncapsulatedVersion();
        }

        return true;
    }

    // -----
    // Not used, initially, but included for the possibility of future upgrades!
    //
    // Note: This (https://github.com/zencart/zencart/pull/6498) Zen Cart PR must
    // be present in the base code or a PHP Fatal error is generated due to the
    // function signature difference.
    //
    protected function executeUpgrade($oldVersion)
    {
    }

    protected function executeUninstall()
    {
        zen_deregister_admin_pages([
            'configTyPackageTracker',
        ]);

        $this->deleteConfigurationGroup($this->configGroupTitle, true);
    }

    protected function nonEncapsulatedVersionPresent(): bool
    {
        $log_messages = [];

        $file_found_message = 'Non-encapsulated admin file (%s) must be removed before this plugin can be installed.';
        $files_to_check = [
            'includes/auto_loaders/' => [
                'config.typt.php',
            ],
            'includes/classes/' => [
                'observers/TyPackageTrackerAdminObserver.php',
            ],
            'includes/extra_datafiles/' => [
                'tracker.php',
                'typackage_defines.php',
                'typackage_filenames.php',
            ],
            'includes/functions/' => [
                'extra_functions/common_orders_functions.php',
                'ty_package_tracker_functions.php',
            ],
            'includes/init_includes/' => [
                'init_typt_config.php',
            ],
            'includes/languages/english/' => [
                'tracker.php',
                'extra_definitions/ty_package_tracker_admin_definitions.php',
            ],
            'includes/modules/ty_package_tracker/' => [
                'tpl_package_tracker_eo_form.php',
                'tpl_package_tracker_form.php',
            ],
        ];
        foreach ($files_to_check as $dir => $files) {
            $current_dir = DIR_FS_ADMIN . $dir;
            foreach ($files as $next_file) {
                if (file_exists($current_dir . $next_file)) {
                    $log_messages[] = sprintf($file_found_message, $dir . $next_file);
                }
            }
        }

        $file_found_message = 'Non-encapsulated storefront file (%s) must be removed before this plugin can be installed.';
        $files_to_check = [
            'includes/extra_datafiles/' => [
                'track_orders.php',
            ],
            'includes/languages/english/' => [
                'tracker.php',
                'extra_definitions/track_order.php',
                'extra_definitions/ty_package_tracker_definitions.php',
            ],
            'includes/modules/' => [
                'pages/tracker/header_php.php',
                'pages/account_history_info/header_php_typt.php',
                'sideboxes/track_orders.php',
            ],
            'includes/templates/template_default/' => [
                'sideboxes/tpl_track_orders.php',
            ],
        ];
        foreach ($files_to_check as $dir => $files) {
            $current_dir = DIR_FS_CATALOG . $dir;
            foreach ($files as $next_file) {
                if (file_exists($current_dir . $next_file)) {
                    $log_messages[] = sprintf($file_found_message, $dir . $next_file);
                }
            }
        }

        if (count($log_messages) !== 0) {
            error_log(implode("\n", $log_messages), 3, DIR_FS_LOGS . '/myDEBUG-adm-typt-installation-error-' . date('Ymd-His') . '.log');
            return true;
        }
        return false;
    }

    // -----
    // Remove any no-longer-used settings.
    //
    protected function updateFromNonEncapsulatedVersion(): void
    {
        $this->executeInstallerSql(
            "DELETE FROM " . TABLE_CONFIGURATION . "
              WHERE configuration_key IN (
                'TY_TRACKER',
                'TY_TRACKER_VERSION'
              )"
        );
    }
}
