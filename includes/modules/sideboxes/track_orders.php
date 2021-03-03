<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.
//
// Last updated 20210301-lat9 for v4.0.0
//
/*
 * This file is derived from order_history.php
 *
 ******************************************************************************
 * order_history sidebox - if enabled, shows customers' most recent orders    *
 *                                                                            *
 * @package templateSystem                                                    *
 * @copyright Copyright 2003-2005 Zen Cart Development Team                   *
 * @copyright Portions Copyright 2003 osCommerce                              *
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0   *
 * @version $Id: order_history.php 2718 2005-12-28 06:42:39Z drbyte $         *
 ******************************************************************************
 * File ID: track_orders.php v2.2 by colosports
 */
if (zen_is_logged_in() && !zen_in_guest_checkout()) {
    $track_orders_limit = (ctype_digit(MAX_DISPLAY_PRODUCTS_IN_TRACK_ORDERS_BOX)) ? MAX_DISPLAY_PRODUCTS_IN_TRACK_ORDERS_BOX : '3';
    $orders_history = $db->Execute(
        "SELECT orders_id, date_purchased
           FROM " . TABLE_ORDERS . "
          WHERE customers_id = " . (int)$_SESSION['customer_id'] . "
          ORDER BY date_purchased DESC
          LIMIT $track_orders_limit"
    );

    if (!$orders_history->EOF) {
        $track_orders = [];
        foreach ($orders_history as $track_history) {
            $track_orders[] = $track_history;
        }
        
        require $template->get_template_dir('tpl_track_orders.php', DIR_WS_TEMPLATE, $current_page_base, 'sideboxes') . '/tpl_track_orders.php';
        $title =  BOX_HEADING_TRACK_ORDERS;
        $title_link = false;
        require $template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base, 'common') . '/' . $column_box_default;
    }
}
