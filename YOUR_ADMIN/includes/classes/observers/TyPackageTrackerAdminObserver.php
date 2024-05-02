<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.  Provides integration with the
// admin's Customers :: Orders and Edit Orders display and update of an order's tracking information.
//
// Last updated 20240502-lat9 for v4.1.0
//
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    die('Illegal Access');
}

class TyPackageTrackerAdminObserver extends base
{
    protected string $eo_field_display;

    public function __construct()
    {
        // -----
        // If the plugin's configuration is set, register for notifications from
        // various admin elements associated with an order's status-history updates.
        //
        if (defined('TY_TRACKER_VERSION')) {
            // -----
            // Always watch for notifications from the core Customers::Orders.
            //
            $this->attach(
                $this,
                [
                    /* From /admin/orders.php (zc157+) */
                    'NOTIFY_ADMIN_ORDERS_STATUS_HISTORY_EXTRA_COLUMN_HEADING',
                    'NOTIFY_ADMIN_ORDERS_STATUS_HISTORY_EXTRA_COLUMN_DATA',
                    'NOTIFY_ADMIN_ORDERS_ADDL_HISTORY_INPUTS',
                ]
            );

            // -----
            // The 'Edit Orders' integration is a tad complicated.  EO versions
            // prior to v4.6.0-beta1 include TyPT integration when the 'TY_TRACKER' constant
            // is defined and set to 'True' and handle the base order-status update directly
            // rather than using the zen_update_orders_history function.  v4.4.0 and later of EO
            // use that function to add the EO-specific, hidden comment to identify what changes
            // were performed.
            //
            // Thus, we'll register for notifications from the zen_update_orders_history function
            // UNLESS the request comes during EO operations and the EO version is less than 4.6.0-beta1.
            //
            $is_eo_access = (defined('FILENAME_EDIT_ORDERS') && $GLOBALS['current_page'] == (FILENAME_EDIT_ORDERS . '.php'));
            $eo_supports_typt_notifications = (defined('EO_VERSION') && version_compare(EO_VERSION, '4.6.0-beta1', '>='));
            if (!$is_eo_access || $eo_supports_typt_notifications) {
                $this->attach(
                    $this,
                    [
                        /* From /includes/functions/functions_osh_update.php */
                        'ZEN_UPDATE_ORDERS_HISTORY_PRE_EMAIL',
                        'ZEN_UPDATE_ORDERS_HISTORY_BEFORE_INSERT',
                    ]
                );
            }

            // -----
            // If the plugin is configured to also inject its form-fields and updates
            // for the 'Edit Orders' display and the EO version supports the required
            // notifications for TyPT, monitor for the EO-related notifications as well.
            //
            if (TY_TRACKER === 'False' && $is_eo_access && $eo_supports_typt_notifications) {
                $this->attach(
                    $this,
                    [
                        /* From /admin/edit_orders.php */
                        'EDIT_ORDERS_STATUS_DISPLAY_ARRAY_INIT',
                        'EDIT_ORDERS_ADDITIONAL_OSH_CONTENT',
                    ]
                );
            }
        }
    }

    public function update(&$class, $eventID, $p1, &$p2, &$p3, &$p4)
    {
        switch ($eventID) {
            // -----
            // Issued by /includes/functions/functions_osh_update.php during a status-update action, giving
            // us the opportunity to add any tracking information to the order-update email.
            //
            // On entry:
            //
            // $p1 ... n/a
            // $p2 ... Contains a reference to the current to-be-notified comments.
            //
            case 'ZEN_UPDATE_ORDERS_HISTORY_PRE_EMAIL':
                for ($i = 1; $i <= 5; $i++) {
                    $track_id_var = "track_id$i";
                    if (empty($_POST[$track_id_var])) {
                        continue;
                    }
                    $tracking_id = str_replace(' ', '', zen_db_prepare_input($_POST[$track_id_var]));

                    $carrier_name = constant("CARRIER_NAME_$i");
                    $carrier_link = constant("CARRIER_LINK_$i") . $tracking_id;
                    
                    $p2 .= sprintf(EMAIL_TEXT_TRACKID_UPDATE, $carrier_name, $tracking_id, $carrier_link);
                }

                // -----
                // Now that we've set that message, there's no need to continue watching for the
                // event (and it could cause multiple insertions).
                //
                $this->detach($this, ['ZEN_UPDATE_ORDERS_HISTORY_PRE_EMAIL']);
                break;

            // -----
            // Issued by zen_update_orders_history during a status-update action, just
            // after writing the 'base' status-history record to the database.
            // Gives the opportunity to update that database record with any
            // tracking information supplied.
            //
            // On entry:
            //
            // $p1 ... n/a
            // $p2 ... A reference to the $sql_data_array containing the to-be-written
            //         status-history record.
            //
            case 'ZEN_UPDATE_ORDERS_HISTORY_BEFORE_INSERT':
                for ($i = 1; $i < 5; $i++) {
                    $track_id_var = "track_id$i";
                    if (!empty($_POST[$track_id_var])) {
                        $p2[$track_id_var] = zen_db_input(str_replace(' ', '', zen_db_prepare_input($_POST[$track_id_var])));
                    }
                }

                // -----
                // Now that we've set that message, there's no need to continue watching for the
                // event (and it could cause multiple insertions).
                //
                $this->detach($this, ['ZEN_UPDATE_ORDERS_HISTORY_BEFORE_INSERT']);
                break;

            // -----
            // Issued by /admin/orders.php at the beginning of an order's status-
            // history table display.  We'll add the column-heading associated
            // with the order's tracking numbers.
            //
            // $p1 ... n/a.
            // $p2 ... A reference to a variable to be updated with the heading text
            //         and associated alignment.
            //
            case 'NOTIFY_ADMIN_ORDERS_STATUS_HISTORY_EXTRA_COLUMN_HEADING':
                if (!is_array($p2)) {
                    $p2 = [];
                }
                $p2[] = [
                    'text' => TABLE_HEADING_TRACKING_ID
                ];
                break;

            // -----
            // Issued by /admin/orders.php when displaying an individual status-history
            // record.  We'll add the column-data associated with that record's
            // tracking number.
            //
            // $p1 ... Contains the fields associated with the current status-history record
            // $p2 ... A reference to a variable to be updated with the associated data to be displayed.
            //
            case 'NOTIFY_ADMIN_ORDERS_STATUS_HISTORY_EXTRA_COLUMN_DATA':
                if (!is_array($p2)) {
                    $p2 = [];
                }
                for ($i = 1, $display_track_id = '&nbsp;'; $i <= 5; $i++) {
                    $track_id_field = "track_id$i";
                    if (!empty($p1[$track_id_field])) {
                        $track_id = nl2br(zen_output_string_protected($p1[$track_id_field]));
                        $carrier_name = constant("CARRIER_NAME_$i");
                        $carrier_link = constant("CARRIER_LINK_$i");
                        $display_track_id .= "$carrier_name: <a href=\"$carrier_link$track_id\" target=\"_blank\" rel=\"noreferrer noopener\">$track_id</a>&nbsp;";
                    }
                }
                $p2[] = [
                    'align' => 'left',
                    'text' => $display_track_id
                ];
                break;

            // -----
            // Issued by /admin/orders.php to request any additional status-history form elements
            // to display.  We'll load the tracking-information input form.
            //
            case 'NOTIFY_ADMIN_ORDERS_ADDL_HISTORY_INPUTS':
                require DIR_WS_MODULES . 'ty_package_tracker/tpl_package_tracker_form.php';
                break;

            // -----
            // Issued by /admin/edit_orders.php, just prior to the display of the order's current
            // status-history table.  We'll inject the information, letting EO know how to display the
            // fields associated with Ty Package Tracker in the order's status-history table.
            //
            // On entry:
            //
            // $p1 ... (r/o) The orders_id being processed.
            // $p2 ... (r/w) An array describing the fields to be displayed in the table and how
            //               they're to be displayed.
            //
            case 'EDIT_ORDERS_STATUS_DISPLAY_ARRAY_INIT':
                require DIR_WS_FUNCTIONS . 'ty_package_tracker_functions.php';
                $field_data = [
                    'title' => '',
                    'show_function' => 'typt_eo_display_field',
                    'include_field_name' => true,
                ];
                $table_elements = [];
                foreach ($p2 as $key => $values) {
                    $table_elements[$key] = $values;
                    if ($key == 'orders_status_id') {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i == 5) {
                                $field_data['title'] = TABLE_HEADING_TRACKING_ID;
                            }
                            $table_elements["track_id$i"] = $field_data;
                        }
                    }
                }
                $p2 = $table_elements;
                break;

            // -----
            // Issued by /admin/edit_orders.php, within the status-update form just after the text-area
            // comments block.  We'll add the TyPT form fields to the display.
            //
            case 'EDIT_ORDERS_ADDITIONAL_OSH_CONTENT':
                ob_start();
                require DIR_WS_MODULES . 'ty_package_tracker/tpl_package_tracker_eo_form.php';
                $p2[] = ob_get_clean();
                break;
 
            default:
                break;
        }
    }

    // -----
    // A helper function, called by the typt_eo_display_field function, present in
    // /admin/includes/functions/ty_package_tracker_functions.php.
    //
    public function buildEoTrackDisplay($field_value, $field_name)
    {
        switch ($field_name) {
            case 'track_id1':
                $ty = '1';
                $this->eo_field_display = '';
                break;
            case 'track_id2':
                $ty = '2';
                break;
            case 'track_id3':
                $ty = '3';
                break;
            case 'track_id4':
                $ty = '4';
                break;
            case 'track_id5':
                $ty = '5';
                break;
            default:
                trigger_error("Unknown field name ($field_name) supplied.", E_USER_ERROR);
                exit();
                break;
        }
        if (!empty($field_value)) {
            $track_id = nl2br(zen_output_string_protected($field_value));
            $this->eo_field_display .= (constant("CARRIER_NAME_$ty") . ': <a href="' . constant("CARRIER_LINK_$ty") . $track_id . '" target="_blank" rel="noreferrer noopener">' . $track_id . '</a>&nbsp;');
        }
        return $this->eo_field_display;
    }
}
