<?php
// -----
// Ty Package Tracker, status-update form insert.  Derived from the processing
// previously embedded in the admin's orders.php for TyPT v3.1.6 by @jeking928.
//
?>
<div class="form-group">
    <div class="col-sm-3 control-label"><?php echo zen_image(DIR_WS_IMAGES . 'icon_track_add.png', ENTRY_ADD_TRACK) . '&nbsp;' . ENTRY_ADD_TRACK; ?></strong></div>
    <div class="col-sm-9"><table class="table">
        <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><strong><?php echo TABLE_HEADING_CARRIER_NAME; ?></strong></td>
            <td class="dataTableHeadingContent"><strong><?php echo TABLE_HEADING_TRACKING_ID; ?></strong></td>
        </tr>
<?php 
for ($i = 1; $i <= 5; $i++) {
    $carrier_status = "CARRIER_STATUS_$i";
    $carrier_name = "CARRIER_NAME_$i";
    if (defined($carrier_status) && defined($carrier_name)) {
        if (constant($carrier_status) == 'True') { 
?>
        <tr>
            <td><?php echo constant($carrier_name); ?></td>
            <td valign="top"><?php echo zen_draw_input_field("track_id$i", '', 'size="50"'); ?></td>
        </tr>
<?php
        }
    } 
} 
?>
    </table></div>
</div>
