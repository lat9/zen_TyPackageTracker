



# Upgrading to v4.0.0 (or later) From an Earlier Version

Version 4.0.0 and later of ***Ty Package Tracker*** ***(TyPT)*** no longer includes core-file overwrites.  The first step in an upgrade from an earlier version of the plugin requires the removal of those previous versions' changes.

## Files to Remove

The files listed below should be removed (or deleted) from the store's file-system:

1. /includes/extra_datafiles/track_orders.php
2. /includes/languages/english/tracker.php
3. /includes/languages/english/extra_definitions/track_order.php
4. /includes/languages/english/YOUR_TEMPLATE/tracker.php
5. /includes/modules/pages/tracker/header_php.php
6. /includes/templates/YOUR_TEMPLATE/templates/tpl_tracker_default.php
7. /YOUR_ADMIN/tracker.php
8. /YOUR_ADMIN/images/icon_track.gif
9. /YOUR_ADMIN/includes/typt_stylesheet.css
10. /YOUR_ADMIN/includes/boxes/extra_boxes/tracker_customers_dhtml.php
11. /YOUR_ADMIN/includes/extra_datafiles/tracker.php
12. /YOUR_ADMIN/includes/extra_datafiles/typackage_filenames.php
13. /YOUR_ADMIN/includes/functions/extra_functions/common_orders_functions.php
14. /YOUR_ADMIN/includes/languages/english/tracker.php
15. /YOUR_ADMIN/includes/languages/english/images/button_track.gif

## Core-File Changes to Remove

This section identifies, file-by-file, the in-core edits made by earlier versions of ***TyPT*** prior to the v4.0.0+ upgrade.

### Storefront Changes to Remove

#### /includes/languages/YOUR_TEMPLATE/english.php

Close to the end of the file, remove the following lines:

```php
//text for Ty Package Tracker sidebox heading
  define('BOX_HEADING_TRACK_ORDERS', 'Previous Orders');
```

 If those were the only changes to the file, when compared to `/includes/languages/english.php`, you can safely remove this template-override file.

#### /includes/languages/english/YOUR_TEMPLATE/account_history_info.php

Close to the end of the file, remove the following lines:

```php
// Begin Ty Package Tracker
define('TABLE_HEADING_TRACKING_ID', 'Tracking ID');
// End Ty Package Tracker
```

If those were the only changes to the file, when compared to `/includes/languages/english/account_history_info.php`, you can safely remove this template-override file.  That definition now resides in `/includes/languages/english/extra_definitions/ty_package_tracker_definitions.php`.

#### /includes/modules/pages/account_history_info.php

Remove the ***TyPT***-specific changes to this file, noting that the changes might be slightly different, based on your Zen Cart version.

```php
// Begin Ty Package Tracker
$statuses_query = "SELECT os.orders_status_name, osh.date_added, osh.comments, osh.track_id1, osh.track_id2, osh.track_id3, osh.track_id4, osh.track_id5
                   FROM   " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh
                   WHERE      osh.orders_id = :ordersID
                   AND        osh.orders_status_id = os.orders_status_id
                   AND        os.language_id = :languagesID
                   AND        osh.customer_notified >= 0
                   ORDER BY   osh.date_added";
// End Ty Package Tracker

$statuses_query = $db->bindVars($statuses_query, ':ordersID', $_GET['order_id'], 'integer');
$statuses_query = $db->bindVars($statuses_query, ':languagesID', $_SESSION['languages_id'], 'integer');
$statuses = $db->Execute($statuses_query);
$statusArray = array();

while (!$statuses->EOF) {

// Begin Ty Package Tracker
  $statusArray[] = array('date_added'=>$statuses->fields['date_added'],
  'orders_status_name'=>$statuses->fields['orders_status_name'],
  'comments'=>$statuses->fields['comments'],
  'track_id1'=>$statuses->fields['track_id1'],
  'track_id2'=>$statuses->fields['track_id2'],
  'track_id3'=>$statuses->fields['track_id3'],
  'track_id4'=>$statuses->fields['track_id4'],
  'track_id5'=>$statuses->fields['track_id5']
  );
  $statuses->MoveNext();
}
// End Ty Package Tracker
```

You'll remove the `, osh.track_id1, osh.track_id2, osh.track_id3, osh.track_id4, osh.track_id5` from the query's **SELECT** clause as well as the inclusion of the `track_id`***n*** elements from the `$statusArray`.

Those changes are now provided by `/includes/modules/pages/account_history_info/header_php_typt.php`.

### Admin Changes to Remove

#### /YOUR_ADMIN/orders.php

Removing various edits, which will be replaced by observer-class handling, five (5) sections total.

1. Find the following section

```php
// BEGIN TY TRACKER 
      $updated_by = zen_updated_by_admin();
      $status_updated = zen_update_orders_history($oID, $comments, $updated_by, $status, $customer_notified, $email_include_message);
// END TY TRACKER
```

and replace with

```php
      $status_updated = zen_update_orders_history($oID, $comments, null, $status, $customer_notified, $email_include_message);
```

2. Find the following section

```php
// TY TRACKER 5 BEGIN - Add Super Orders Order Navigation Functionality

    $prev_button = '';
    $result = $db->Execute("SELECT orders_id
    					FROM " . TABLE_ORDERS . " 
					WHERE orders_id < " . (int)$oID . "
					ORDER BY orders_id DESC 
					LIMIT 1");
    if ($result->RecordCount()) {
          $prev_button = '<a role="button" class="btn btn-default" href="' . zen_href_link(FILENAME_ORDERS, 'oID=' . $result->fields['orders_id'] . '&action=edit') . '">&laquo; ' . $result->fields['orders_id'] . '</a>';
    }

    $next_button = '';
    $result = $db->Execute("SELECT orders_id 
					FROM " . TABLE_ORDERS . " 
					WHERE orders_id > " . (int)$oID . "
					ORDER BY orders_id ASC 
					LIMIT 1");
    if ($result->RecordCount()) {
          $next_button = '<a role="button" class="btn btn-default" href="' . zen_href_link(FILENAME_ORDERS, 'oID=' . $result->fields['orders_id'] . '&action=edit') . '">' . $result->fields['orders_id'] . ' &raquo;</a>';
    }
    else {
      $next_button = '<button type="button" class="btn btn-default" TYPE="BUTTON" VALUE="' . BUTTON_TO_LIST . '" ONCLICK="window.location.href=\'' . zen_href_link(FILENAME_ORDERS) . '\'">';
    }
// TY TRACKER 5 END - Add Super Orders Order Navigation Functionality
```

... and replace with

```php
    $prev_button = '';
    $result = $db->Execute("SELECT orders_id
    					FROM " . TABLE_ORDERS . " 
					WHERE orders_id < " . (int)$oID . "
					ORDER BY orders_id DESC 
					LIMIT 1");
    if ($result->RecordCount()) {
          $prev_button = '<a role="button" class="btn btn-default" href="' . zen_href_link(FILENAME_ORDERS, 'oID=' . $result->fields['orders_id'] . '&action=edit') . '">&laquo; ' . $result->fields['orders_id'] . '</a>';
    }

    $next_button = '';
    $result = $db->Execute("SELECT orders_id 
					FROM " . TABLE_ORDERS . " 
					WHERE orders_id > " . (int)$oID . "
					ORDER BY orders_id ASC 
					LIMIT 1");
    if ($result->RecordCount()) {
          $next_button = '<a role="button" class="btn btn-default" href="' . zen_href_link(FILENAME_ORDERS, 'oID=' . $result->fields['orders_id'] . '&action=edit') . '">' . $result->fields['orders_id'] . ' &raquo;</a>';
    }
```

3. Find this section

```php
    <table class="table-condensed table-striped table-bordered">
          <thead>
        <tr>
              <th class="text-center"><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
              <!-- BEGIN TY TRACKER 3 - DISPLAY TRACKING ID IN COMMENTS TABLE ------------------------------->
              <th class="text-center"><?php echo TABLE_HEADING_TRACKING_ID; ?>
            </td>
            
            <!-- END TY TRACKER 3 - DISPLAY TRACKING ID IN COMMENTS TABLE ------------------------------------------------------------> 
          <th class="text-center"><?php echo TABLE_HEADING_COMMENTS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_UPDATED_BY; ?></th>
            </tr>
      </thead>
          <tbody>
        <?php

// BEGIN TY TRACKER 4 - INCLUDE DATABASE FIELDS IN STATUS TABLE ------------------------------
    $orders_history = $db->Execute("select orders_status_id, date_added, customer_notified, track_id1, track_id2, track_id3, track_id4, track_id5, comments, updated_by
                                    FROM " . TABLE_ORDERS_STATUS_HISTORY . "
                                    WHERE orders_id = '" . zen_db_input($oID) . "'
                                    ORDER BY date_added");
// END TY TRACKER 4 - INCLUDE DATABASE FIELDS IN STATUS TABLE -----------------------------------------------------------

```

... and replace with

```php
    <table class="table-condensed table-striped table-bordered">
          <thead>
            <tr>
              <th class="text-center"><?php echo TABLE_HEADING_DATE_ADDED; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_STATUS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_COMMENTS; ?></th>
              <th class="text-center"><?php echo TABLE_HEADING_UPDATED_BY; ?></th>
            </tr>
          </thead>
          <tbody>
        <?php
    $orders_history = $db->Execute("SELECT *
                                    FROM " . TABLE_ORDERS_STATUS_HISTORY . "
                                    WHERE orders_id = " . zen_db_input($oID) . "
                                    ORDER BY date_added");

```

4. Find this section

```php
              <?php
// BEGIN TY TRACKER 5 - DEFINE TRACKING INFORMATION ----------------
        $display_track_id = '&nbsp;';
	$display_track_id .= (empty($orders_history->fields['track_id1']) ? '' : CARRIER_NAME_1 . ": <a href=" . CARRIER_LINK_1 . nl2br(zen_output_string_protected($orders_history->fields['track_id1'])) . ' target="_blank">' . nl2br(zen_output_string_protected($orders_history->fields['track_id1'])) . "</a>&nbsp;" );
	$display_track_id .= (empty($orders_history->fields['track_id2']) ? '' : CARRIER_NAME_2 . ": <a href=" . CARRIER_LINK_2 . nl2br(zen_output_string_protected($orders_history->fields['track_id2'])) . ' target="_blank">' . nl2br(zen_output_string_protected($orders_history->fields['track_id2'])) . "</a>&nbsp;" );
	$display_track_id .= (empty($orders_history->fields['track_id3']) ? '' : CARRIER_NAME_3 . ": <a href=" . CARRIER_LINK_3 . nl2br(zen_output_string_protected($orders_history->fields['track_id3'])) . ' target="_blank">' . nl2br(zen_output_string_protected($orders_history->fields['track_id3'])) . "</a>&nbsp;" );
	$display_track_id .= (empty($orders_history->fields['track_id4']) ? '' : CARRIER_NAME_4 . ": <a href=" . CARRIER_LINK_4 . nl2br(zen_output_string_protected($orders_history->fields['track_id4'])) . ' target="_blank">' . nl2br(zen_output_string_protected($orders_history->fields['track_id4'])) . "</a>&nbsp;" );
	$display_track_id .= (empty($orders_history->fields['track_id5']) ? '' : CARRIER_NAME_5 . ": <a href=" . CARRIER_LINK_5 . nl2br(zen_output_string_protected($orders_history->fields['track_id5'])) . ' target="_blank">' . nl2br(zen_output_string_protected($orders_history->fields['track_id5'])) . "</a>&nbsp;" );
        echo '            <td>' . $display_track_id . '</td>' . "\n";
// END TY TRACKER 5 - DEFINE TRACKING INFORMATION -------------------------------------------------------------------                    
                    ?>
```

... and remove it.

5. Find this section

```php
          <!-- BEGIN TY TRACKER 6 - ENTER TRACKING INFORMATION -->
          <?php for($i=1;$i<=5;$i++) {
                if(constant('CARRIER_STATUS_' . $i) == 'True')
			{ ?>
          <div class="form-group">
        <label class="col-sm-3 control-label"><?php echo constant('CARRIER_NAME_' . $i); ?></label>
        <div class="col-sm-9"> <?php echo zen_draw_input_field('track_id[' . $i . ']', '', 'class="form-control"'); ?> </div>
      </div>
          <?php } } ?>
          <!-- END TY TRACKER 6 - ENTER TRACKING INFORMATION -->
```

... and remove it.

#### /YOUR_ADMIN/includes/languages/english/orders.php

Close to the end of the file, remove the following block of ***TyPT*** additions:

```php
// BEGINTY TRACKER ----------------------------------------------
define('HEADING_TITLE_ORDER_DETAILS', 'Order # ');
define('TABLE_HEADING_TRACKING_ID', 'Tracking ID');
define('TABLE_HEADING_CARRIER_NAME', 'Carrier');
define('ENTRY_ADD_TRACK', 'Add Tracking ID');
define('IMAGE_TRACK', 'Add Tracking ID');
define('EMAIL_TEXT_COMMENTS_TRACKING_UPDATE', '<em>Items from your order will be shipping soon!</em>'); 
// END TY TRACKER -------------------------------------------------
```

All admin language constants used by ***TyPT*** are now present in `/YOUR_ADMIN/includes/languages/english/extra_definitions/ty_package_tracker_admin_definitions.php`.