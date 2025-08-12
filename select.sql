SELECT
    devices.id as device_id,
    devices.mac_address,
    url_filters.filters
FROM devices, url_filters
WHERE devices.id IN (
    SELECT url_filter_devices.device_id FROM url_filter_devices WHERE url_filter_devices.url_filter_id = url_filters.id
) OR devices.id IN (
    SELECT
        group_devices.device_id
    FROM
        group_devices
    JOIN url_filter_groups ON url_filter_groups.group_id = group_devices.group_id
    WHERE url_filter_groups.url_filter_id = url_filters.id
)
