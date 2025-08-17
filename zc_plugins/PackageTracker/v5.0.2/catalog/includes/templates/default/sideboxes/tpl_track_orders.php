<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.
//
// Last updated 20210301-lat9 for v4.0.0
//
/*
 * This file is derived from tpl_order_history.php
 *
 ******************************************************************************
 * Side Box Template                                                          *
 *                                                                            *
 * @package templateSystem                                                    *
 * @copyright Copyright 2003-2005 Zen Cart Development Team                   *
 * @copyright Portions Copyright 2003 osCommerce                              *
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0   *
 * @version $Id: tpl_order_history.php 3324 2006-03-31 06:02:07Z drbyte $     *
 ******************************************************************************
 * File ID: tpl_track_orders.php v3.1.4 by colosports
 */
$content = '<div id="' . str_replace('_', '-', $box_id . 'Content') . '" class="sideBoxContent">' . "\n";

$content .= '<ul class="orderHistList">' . "\n";
foreach ($track_orders as $next_track) {
    $track_order_id = $next_track['orders_id'];
    $track_purchase_date = TRACK_ORDERS_DATE . zen_date_short($next_track['date_purchased']);
    $content .= '<li>';
    $content .= '<a href="' . zen_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $track_order_id, 'SSL') . '">' . TRACK_ORDERS_ORDERNUM . $track_order_id . '</a>' . $track_purchase_date;
    $content .= '</li>' . "\n";
}
$content .= '</ul>' . "\n";

$content .= '</div>' . "\n";
