<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.  Provides integration with the
// admin's Customers :: Orders and Edit Orders display and update of an order's tracking information.
//
// Last updated 20210301-lat9 for v4.0.0
//

// -----
// Various language definitions, used for both the Customers::Orders and EditOrders notifications.
//
define('TABLE_HEADING_TRACKING_ID', 'Tracking ID');
define('TABLE_HEADING_CARRIER_NAME', 'Carrier');
define('ENTRY_ADD_TRACK', 'Add Tracking ID');
define('EMAIL_TEXT_COMMENTS_TRACKING_UPDATE', '<em>Items from your order will be shipping soon!</em>'); 

// -----
// Used to sprintf the carrier-name (%1$s), tracking-id (%2$s) and the carrier-link (%3$s) into the to-be-sent tracking-update email.
//
define('EMAIL_TEXT_TRACKID_UPDATE', "\n\n" . 'Your %1$s Tracking ID is %2$s' . "\n" . '<br><a href=\"%3$s\">Click here</a> to track your package.' . "\n" . '<br>If the above link does not work, copy the following URL address and paste it into your Web browser.' . "\n" . '<br>%3%s' . "\n\n" . '<br><br>It may take up to 24 hours for the tracking information to appear on the website.' . "\n<br>"');
