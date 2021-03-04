<?php
// -----
// Part of the Ty Package Tracker plugin, modified to interoperate with EO v4.4.4 and later.
//
// -----
// This function, called by Edit Orders to display the Ty Package Tracker fields within
// the orders-status-history table.
//
// Note: As strange as this looks, the class-method is used so that the buildEoTrackDisplay function
// can 'remember' the previous value for the output.
//
function typt_eo_display_field($field_value, $field_name)
{
    return $GLOBALS['typt']->buildEoTrackDisplay($field_value, $field_name);
}
