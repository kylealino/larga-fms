-- =============================================
-- PHASE 7: YES/NO FLAGS -> tinyint(1) 0/1, plain int widths
-- =============================================
ALTER TABLE `tbl_drivers`
    MODIFY `long_distance_experience` varchar(3) NOT NULL DEFAULT 'NO',
    MODIFY `city_urban_experience` varchar(3) NOT NULL DEFAULT 'NO',
    MODIFY `highway_experience` varchar(3) NOT NULL DEFAULT 'NO',
    MODIFY `defensive_driving_training` varchar(3) NOT NULL DEFAULT 'NO',
    MODIFY `safety_training` varchar(3) NOT NULL DEFAULT 'NO';
UPDATE `tbl_drivers` SET
    `long_distance_experience` = IF(`long_distance_experience` = 'YES', '1', '0'),
    `city_urban_experience` = IF(`city_urban_experience` = 'YES', '1', '0'),
    `highway_experience` = IF(`highway_experience` = 'YES', '1', '0'),
    `defensive_driving_training` = IF(`defensive_driving_training` = 'YES', '1', '0'),
    `safety_training` = IF(`safety_training` = 'YES', '1', '0');
ALTER TABLE `tbl_drivers`
    MODIFY `long_distance_experience` tinyint(1) NOT NULL DEFAULT 0,
    MODIFY `city_urban_experience` tinyint(1) NOT NULL DEFAULT 0,
    MODIFY `highway_experience` tinyint(1) NOT NULL DEFAULT 0,
    MODIFY `defensive_driving_training` tinyint(1) NOT NULL DEFAULT 0,
    MODIFY `safety_training` tinyint(1) NOT NULL DEFAULT 0;

-- Counts / ratings: plain int (display widths int(3)/int(1) meant nothing)
ALTER TABLE `tbl_drivers`
    MODIFY `years_experience` int(11) DEFAULT 0,
    MODIFY `heavy_vehicle_experience` int(11) DEFAULT 0,
    MODIFY `tractor_head_experience` int(11) DEFAULT 0,
    MODIFY `ten_wheeler_experience` int(11) DEFAULT 0;
ALTER TABLE `tbl_driver_skillsets` MODIFY `rating` int(11) NOT NULL DEFAULT 0;
