DELETE FROM configuration WHERE configuration_key LIKE 'CARRIER_STATUS_%';
DELETE FROM configuration WHERE configuration_key LIKE 'CARRIER_NAME_%';
DELETE FROM configuration WHERE configuration_key LIKE 'CARRIER_LINK_%';
DELETE FROM configuration WHERE configuration_key IN ('TY_TRACKER_VERSION', 'MAX_DISPLAY_PRODUCTS_IN_TRACK_ORDERS_BOX', 'TY_TRACKER');
DELETE FROM configuration_group WHERE configuration_group_title = 'Ty Package Tracker' LIMIT 1;
DELETE FROM admin_pages WHERE page_key = 'configTyPackageTracker' LIMIT 1;

ALTER TABLE `orders_status_history` 
    DROP `track_id1`,
    DROP `track_id2`,
    DROP `track_id3`,
    DROP `track_id4`,
    DROP `track_id5`;