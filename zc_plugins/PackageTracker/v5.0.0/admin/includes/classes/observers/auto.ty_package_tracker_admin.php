<?php
// -----
// Part of the Ty Package Tracker plugin, v5.0.0 and later.  Provides integration with the
// admin's Customers :: Orders and Edit Orders display and update of an order's tracking information.
//
// Last updated: v5.0.0
//
if (!defined('IS_ADMIN_FLAG') || IS_ADMIN_FLAG !== true) {
    die('Illegal Access');
}

class zcObserverTyPackageTrackerAdmin extends base
{
    public function __construct()
    {
        $this->attach(
            $this,
            [
                /* From /admin/orders.php (zc157+) and EO v5.0.0+ */
                'NOTIFY_ADMIN_ORDERS_STATUS_HISTORY_EXTRA_COLUMN_HEADING',
                'NOTIFY_ADMIN_ORDERS_STATUS_HISTORY_EXTRA_COLUMN_DATA',
                'NOTIFY_ADMIN_ORDERS_ADDL_HISTORY_INPUTS',

                /* From /includes/functions/functions_osh_update.php */
                'ZEN_UPDATE_ORDERS_HISTORY_PRE_EMAIL',
                'ZEN_UPDATE_ORDERS_HISTORY_BEFORE_INSERT',
            ]
        );
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
            // to display.  We'll directly output the tracking-additions' form.
            //
            case 'NOTIFY_ADMIN_ORDERS_ADDL_HISTORY_INPUTS':
                global $current_page;

                if ($current_page === 'edit_orders.php') {
                    $label_class = 'control-label';
                    $input_class = '';
                } else {
                    $label_class = 'col-sm-3 control-label';
                    $input_class = 'class="col-sm-9"';
                }
                for ($i = 1; $i <= 5; $i++) {
                    $carrier_status = "CARRIER_STATUS_$i";
                    $carrier_name = "CARRIER_NAME_$i";
                    if (defined($carrier_status) && defined($carrier_name)) {
                        if (constant($carrier_status) === 'True') { 
?>
<div class="form-group">
    <label for="track-id-<?= $i ?>" class="<?= $label_class ?>">
        <?= ENTRY_ADD_TRACK . ' (' . constant($carrier_name) . ')' ?>
    </label>
    <div <?= $input_class ?>>
        <?= zen_draw_input_field("track_id$i", '', 'id="track-id-' . $i . '" class="form-control"') ?>
    </div>
</div>
<?php
                        }
                    } 
                } 
                break;
 
            default:
                break;
        }
    }
}
