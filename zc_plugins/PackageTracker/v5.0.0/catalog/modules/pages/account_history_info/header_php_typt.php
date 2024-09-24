<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.  Gathers the various tracking-ids for display
// on the account_history_info page.
//
// Last updated 20210301-lat9 for v4.0.0
//

// -----
// If not configured in the admin, nothing more to do here.
//
if (!defined('TY_TRACKER_VERSION')) {
    return;
}

// -----
// Gather the tracking ids for the current order, using the same sort-order as the base
// header_php.php, so that the tracking ids 'line up'.
//
$typt_status_query =
    "SELECT track_id1, track_id2, track_id3, track_id4, track_id5
       FROM   " . TABLE_ORDERS_STATUS_HISTORY . "
      WHERE orders_id = :ordersID
        AND customer_notified >= 0
      ORDER BY date_added";
$typt_status_query = $db->bindVars($typt_status_query, ':ordersID', $_GET['order_id'], 'integer');
$typt_statuses = $db->Execute($typt_status_query);

// -----
// Loop through the status records, merging in the associated record's tracking ids.
//
$typt_index = 0;
foreach ($typt_statuses as $typt_ids) {
    $statusArray[$typt_index] = array_merge($statusArray[$typt_index], $typt_ids);
    $typt_index++;
}
