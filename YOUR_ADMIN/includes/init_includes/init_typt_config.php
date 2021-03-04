<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.  Performs the plugin's database
// initialization and/or update.
//
// Last updated 20210301-lat9 for v4.0.0
//
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    die('Illegal Access');
}

// -----
// Only update configuration when an admin is logged in.
//
if (!isset($_SESSION['admin_id'])) {
    return;
}

define('TY_TRACKER_CURRENT_VERSION', '4.0.0');

// -----
// Locate the existing or create a new configuration group for the TyPT settings.
//
$typt_menu_title = 'Ty Package Tracker';
$original_config = $db->Execute(
    "SELECT * 
       FROM " . TABLE_CONFIGURATION_GROUP . "
      WHERE configuration_group_title = '" . $typt_menu_title . "'
      LIMIT 1"
);
if (!$original_config->EOF) {
    $cgi = $original_config->fields['configuration_group_id'];
} else {
    $db->Execute(
        "INSERT INTO " . TABLE_CONFIGURATION_GROUP . " 
            (configuration_group_title, configuration_group_description, sort_order, visible) 
        VALUES 
            ('$typt_menu_title', 'Settings for Ty Package Tracker Features', 1, 1)"
    );
    $cgi = $db->Insert_ID(); 
    $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = $cgi WHERE configuration_group_id = $cgi LIMIT 1");
}

// ----
// If not already set, record the plugin's required elements into the database.
//
if (!defined('TY_TRACKER_VERSION')) {
    // -----
    // Add configuration elements.
    //
    $db->Execute(
        "INSERT INTO " . TABLE_CONFIGURATION . "
            (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
         VALUES
            ('Current Ty Package Tracker Version', 'TY_TRACKER_VERSION', '0.0.0', 'Version number.', $cgi, 0, now(), NULL, 'zen_cfg_select_option(array(\'0.0.0\'),'),

            ('Package Tracking - Carrier 1 Status', 'CARRIER_STATUS_1', 'True', 'Enable Tracking for Carrier 1<br><br>Set to false if you do NOT want Carrier 1 to be displayed on Admin and Customer page.', $cgi, 90, now(), NULL, 'zen_cfg_select_option(array(\'True\', \'False\'),'),

            ('Package Tracking - Carrier 1 Name', 'CARRIER_NAME_1', 'FedEx', 'Enter name of Carrier 1<br><br><strong>Example:</strong> FedEx, UPS, Canada Post, etc...<br>(default: FedEx)', $cgi, 95,  now(), NULL, NULL),

            ('Package Tracking - Carrier 1 Tracking Link', 'CARRIER_LINK_1', 'https://www.fedex.com/Tracking?action=track&tracknumbers=', 'Enter the tracking link of Carrier 1<br> <br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 100, now(), NULL, NULL),

            ('Package Tracking - Carrier 2 Status', 'CARRIER_STATUS_2', 'True', 'Enable Tracking for Carrier 2<br><br>Set to false if you do NOT want Carrier 2 to be displayed on Admin and Customer page.', $cgi, 105, now(), NULL, 'zen_cfg_select_option(array(\'True\', \'False\'),'),

            ('Package Tracking - Carrier 2 Name', 'CARRIER_NAME_2', 'UPS', 'Enter name of Carrier 1<br><br><strong>Example:</strong> FedEx, UPS, Canada Post, etc...<br>(default: UPS)', $cgi, 110,  now(), NULL, NULL),

            ('Package Tracking - Carrier 2 Tracking Link', 'CARRIER_LINK_2', 'https://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=', 'Enter the tracking link of Carrier 2<br> <br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 115, now(), NULL, NULL),

            ('Package Tracking - Carrier 3 Status', 'CARRIER_STATUS_3', 'True', 'Enable Tracking for Carrier 3<br><br>Set to false if you do NOT want Carrier 3 to be displayed on Admin and Customer page.', $cgi, 120, now(), NULL, 'zen_cfg_select_option(array(\'True\', \'False\'),'),

            ('Package Tracking - Carrier 3 Name', 'CARRIER_NAME_3', 'USPS', 'Enter name of Carrier 3<br><br><strong>Example:</strong> FedEx, UPS, Canada Post, etc...<br>(default: USPS)', $cgi, 125,  now(), NULL, NULL),

            ('Package Tracking - Carrier 3 Tracking Link', 'CARRIER_LINK_3', 'https://tools.usps.com/go/TrackConfirmAction!input.action?tLabels=', 'Enter the tracking link of Carrier 3<br> <br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 130, now(), NULL, NULL),

            ('Package Tracking - Carrier 4 Status', 'CARRIER_STATUS_4', 'False', 'Enable Tracking for Carrier 4<br><br>Set to false if you do NOT want Carrier 4 to be displayed on Admin and Customer page.', $cgi, 140, now(), NULL, 'zen_cfg_select_option(array(\'True\', \'False\'),'),

            ('Package Tracking - Carrier 4 Name', 'CARRIER_NAME_4', '', 'Enter name of Carrier 4<br><br><strong>Example:</strong> FedEx, UPS, Canada Post, etc...<br>(default: blank)', $cgi, 145,  now(), NULL, NULL),

            ('Package Tracking - Carrier 4 Tracking Link', 'CARRIER_LINK_4', '', 'Enter the tracking link of Carrier 4<br><br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 150, now(), NULL, NULL),

            ('Package Tracking - Carrier 5 Status', 'CARRIER_STATUS_5', 'False', 'Enable Tracking for Carrier 5<br><br>Set to false if you do NOT want Carrier 5 to be displayed on Admin and Customer page.', $cgi, 166, now(), NULL, 'zen_cfg_select_option(array(\'True\', \'False\'),'),

            ('Package Tracking - Carrier 5 Name', 'CARRIER_NAME_5', '', 'Enter name of Carrier 5<br><br><strong>Example:</strong> FedEx, UPS, Canada Post, etc...<br>(default: blank)', $cgi, 160,  now(), NULL, NULL),

            ('Package Tracking - Carrier 5 Tracking Link', 'CARRIER_LINK_5', '', 'Enter the tracking link of Carrier 5<br><br><strong>Example:</strong> https://www.fedex.com/Tracking?action=track&tracknumbers=', $cgi, 165, now(), NULL, NULL),

            ('Max display for Track Order sidebox', 'MAX_DISPLAY_PRODUCTS_IN_TRACK_ORDERS_BOX', '3', 'The maximum number of orders to display on the Track Order sidebox.', $cgi, 170, now(), NULL, NULL),

            ('<em>Edit Orders</em> Mode', 'TY_TRACKER', 'False', 'Versions prior to v4.6.0 include the package-tracker handling, but follow-on versions do not.  Set this value to <em>True</em> for EO versions &lt; 4.6.0 and <em>False</em> (the default) for EO versions &gt;= 4.6.0.', $cgi, 175, now(), NULL, 'zen_cfg_select_option(array(\'True\', \'False\'),')"
    );

    // -----
    // Add columns to the orders_status_history table, one for each 'track_id'.
    //
    $db->Execute(
        "ALTER TABLE " . TABLE_ORDERS_STATUS_HISTORY . "
            ADD track_id1 varchar(255) default NULL,
            ADD track_id2 varchar(255) default NULL,
            ADD track_id3 varchar(255) default NULL,
            ADD track_id4 varchar(255) default NULL,
            ADD track_id5 varchar(255) default NULL"
    );

    // -----
    // Add the plugin's configuration menu to the admin's menus.
    //
    zen_register_admin_page('configTyPackageTracker', 'BOX_CONFIGURATION_TY_PACKAGE_TRACKER', 'FILENAME_CONFIGURATION', "gID=$cgi", 'configuration', 'Y');
    
    // -----
    // Initialize the installed version to indicate an initial install.
    //
    define('TY_TRACKER_VERSION', '0.0.0');
}

// -----
// If the installed version is different from the plugin's current version, perform version-specific
// updates.  This section will also run on an initial installation.
//
if (TY_TRACKER_VERSION != TY_TRACKER_CURRENT_VERSION) {
    switch (true) {
        // -----
        // Versions prior to v4.0.0 set each of the track_id{n} values as TEXT fields.  We'll save
        // some space in the database by converting those to varchar(255).
        //
        case version_compare(TY_TRACKER_VERSION, '4.0.0', '<'):
            if (TY_TRACKER_VERSION === '0.0.0') {
                break;
            }
            $db->Execute(
                "ALTER TABLE " . TABLE_ORDERS_STATUS_HISTORY . "
                    MODIFY COLUMN track_id1 varchar(255),
                    MODIFY COLUMN track_id2 varchar(255),
                    MODIFY COLUMN track_id3 varchar(255),
                    MODIFY COLUMN track_id4 varchar(255),
                    MODIFY COLUMN track_id5 varchar(255)"
            );
            $db->Execute(
                "UPDATE " . TABLE_CONFIGURATION . "
                    SET configuration_title = '<em>Edit Orders</em> Mode',
                        configuration_description = 'Versions prior to v4.6.0 include the package-tracker handling, but follow-on versions do not.  Set this value to <em>True</em> for EO versions &lt; 4.6.0 and <em>False</em> (the default) for EO versions &gt;= 4.6.0.'
                  WHERE configuration_key = 'TY_TRACKER'
                  LIMIT 1"
            );

        default:                                        //-Fall through from above
            break;
    }
    
    // -----
    // Let the currently-logged in admin know that a change has been made.
    //
    if (TY_TRACKER_VERSION === '0.0.0') {
        $messageStack->add_session(sprintf(SUCCESS_TYPT_INSTALLED, TY_TRACKER_CURRENT_VERSION), 'success');
    } else {
        $messageStack->add_session(sprintf(SUCCESS_TYPT_UPDATED, TY_TRACKER_VERSION, TY_TRACKER_CURRENT_VERSION), 'success');
    }
    
    // -----
    // Update the plugin's version number in the database.
    //
    $db->Execute(
        "UPDATE " . TABLE_CONFIGURATION . "
            SET configuration_value = '" . TY_TRACKER_CURRENT_VERSION . "',
                set_function = 'zen_cfg_select_option(array(\'" . TY_TRACKER_CURRENT_VERSION . "\'),'
          WHERE configuration_key = 'TY_TRACKER_VERSION'
          LIMIT 1"
    );
}
