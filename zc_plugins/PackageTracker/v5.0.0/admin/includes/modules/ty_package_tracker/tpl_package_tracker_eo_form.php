<?php
// -----
// Ty Package Tracker, status-update form insert for Edit Orders.  Derived from the processing
// previously embedded in /admin/edit_orders.php for the display.
//
// Loaded by /includes/classes/observers/TyPackageTrackerAdminObserver.php, to add the form to
// the edit_orders data-entry page.
//
?>
<table>
    <tr>
        <td class="main"><strong><?php echo zen_image(DIR_WS_IMAGES . 'icon_track_add.png', ENTRY_ADD_TRACK) . '&nbsp;' . ENTRY_ADD_TRACK; ?></strong></td>
    </tr>
    <tr class="v-top">
        <td>
            <table class="w100">
                <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent smallText"><strong><?php echo TABLE_HEADING_CARRIER_NAME; ?></strong></td>
                    <td class="dataTableHeadingContent smallText"><strong><?php echo TABLE_HEADING_TRACKING_ID; ?></strong></td>
                </tr>
<?php 
for ($i = 1; $i <= 5; $i++) {
    if (constant('CARRIER_STATUS_' . $i) == 'True') { 
?>
                <tr>
                    <td><?php echo constant('CARRIER_NAME_' . $i); ?></td>
                    <td valign="top"><?php echo zen_draw_input_field("track_id$i", '', 'size="50"'); ?></td>
                </tr>
<?php 
    } 
} 
?>
            </table>
        </td>
    </tr>
</table>
