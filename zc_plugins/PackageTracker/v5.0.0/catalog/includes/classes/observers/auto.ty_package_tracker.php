<?php
// -----
// Part of the Ty Package Tracker plugin, v4.0.0 and later.  Provides integration to
// display an order's tracking information in the status-history table of a template's
// account_history_info page rendering.
//
// Last updated: v5.0.0 (new)
//
use Zencart\Traits\ObserverManager;

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

class zcObserverTyPackageTracker
{
    use ObserverManager;

    public function __construct()
    {
        $this->attach(
            $this,
            [
                /* From /templates/tpl_account_history_info_default.php */
                'NOTIFY_ACCOUNT_HISTORY_INFO_OSH_HEADINGS',
                'NOTIFY_ACCOUNT_HISTORY_INFO_OSH_DATA',
            ]
        );
    }

    public function notify_account_history_info_osh_headings(&$class, string $e, array $x, array &$extra_headings): void
    {
        $extra_headings[] = TABLE_HEADING_TRACKING_ID;
    }

    public function notify_account_history_info_osh_data(&$class, string $e, array $statuses, array &$extra_data)
    {
        $display_track_id = '';
        for ($i = 1; $i <= 5; $i++) {
            if (empty($statuses["track_id$i"])) {
                continue;
            }

            $track_id = nl2br(zen_output_string_protected($statuses["track_id$i"]));
            $display_track_id .=
                '<span class="ty-pt d-block text-center">' .
                    '<b>' . constant("CARRIER_NAME_$i") . '</b>:&nbsp;' .
                    '<a href="' . constant("CARRIER_LINK_$i") . $track_id . '" target="_blank" rel="noopener noreferrer">' .
                        $track_id .
                    '</a>' .
                '</span>';
        }

        $extra_data[] = $display_track_id;
    }
}
