-- =============================================
-- PHASE 8: DATA FIXES (user decisions 2026-10-05)
-- Backup before this file: __db_backup/largafms (10052026 pre-data-fixes).sql
-- =============================================

-- =============================================
-- TRIP 27 ORPHAN CHAIN: dispatch 23 -> DR 25 -> billing 12 -> invoice 13 (no payments)
-- =============================================
DELETE FROM `tbl_invoices` WHERE `invoice_id` = 13;
DELETE FROM `tbl_billing_charges` WHERE `billing_id` = 12;
DELETE FROM `tbl_billing` WHERE `billing_id` = 12;
DELETE FROM `tbl_delivery_receipt_items` WHERE `dr_id` = 25;
DELETE FROM `tbl_delivery_receipt_pod` WHERE `dr_id` = 25;
DELETE FROM `tbl_delivery_receipts` WHERE `dr_id` = 25;
DELETE FROM `tbl_dispatch_checklist` WHERE `dispatch_id` = 23;
DELETE FROM `tbl_dispatch_expenses` WHERE `dispatch_id` = 23;
DELETE FROM `tbl_dispatch` WHERE `dispatch_id` = 23 AND `trip_id` = 27;

-- =============================================
-- TOOLS: issuances are the source of truth
-- =============================================
-- Orphan issuances of tool 11 (tool no longer exists)
DELETE FROM `tbl_tool_issuances` WHERE `tool_id` NOT IN (SELECT `tool_id` FROM `tbl_tools`);
-- Returned issuances: everything came back
UPDATE `tbl_tool_issuances` SET `quantity_returned` = `quantity_issued`, `quantity_pending` = 0 WHERE `issuance_status` = 'RETURNED';
-- On hand = owned - still out
UPDATE `tbl_tools` t
    LEFT JOIN (SELECT `tool_id`, SUM(`quantity_pending`) AS `out_qty` FROM `tbl_tool_issuances` WHERE `quantity_pending` > 0 GROUP BY `tool_id`) x ON x.`tool_id` = t.`tool_id`
    SET t.`quantity_on_hand` = GREATEST(t.`quantity` - IFNULL(x.`out_qty`, 0), 0);
-- Same rule as FMS_Tool_Model: manual states stay, otherwise ASSIGNED while anything is out
UPDATE `tbl_tools` SET `tool_status` = IF(`quantity_on_hand` < `quantity`, 'ASSIGNED', 'AVAILABLE')
    WHERE `tool_status` NOT IN ('UNDER_REPAIR','DAMAGED','LOST','RETIRED');

-- =============================================
-- SUPPLIES: ledger is the source of truth (replay = FMS_Supply_Model::recalcSupplyStock)
-- =============================================
-- BASAHAN: opening stock 100 was set on the supply record, never recorded
UPDATE `tbl_supply_transactions` SET `transaction_id` = 39 WHERE `transaction_id` = 34;
INSERT INTO `tbl_supply_transactions` (`transaction_id`, `transaction_code`, `transaction_type`, `transaction_date`, `supply_id`, `supply_code`, `supply_name`, `quantity`, `unit`, `unit_cost`, `total_cost`, `previous_stock`, `new_stock`, `purpose`, `created_at`, `created_by`) VALUES (38, 'STX-2026-000035', 'ADJUSTMENT', '2026-09-17', 11, 'SUP-2026-000011', 'BASAHAN', 100.00, 'Pcs', 20.00, 2000.00, 0.00, 100.00, 'Opening stock', '2026-09-17 16:37:20', 'SYSTEM');
ALTER TABLE `tbl_supply_transactions` AUTO_INCREMENT = 40;
UPDATE `tbl_supply_transactions` SET `previous_stock` = 95.00, `new_stock` = 94.00 WHERE `transaction_id` = 33; -- was 120.00->119.00
UPDATE `tbl_supplies` SET `current_stock` = 94.00, `supply_status` = 'IN_STOCK' WHERE `supply_id` = 1; -- Engine Oil 15W-40: was 119.00 IN_STOCK
UPDATE `tbl_supplies` SET `current_stock` = 7.00, `supply_status` = 'LOW_STOCK' WHERE `supply_id` = 4; -- Oil Filter (Light Duty): was 8.00 LOW_STOCK
UPDATE `tbl_supplies` SET `current_stock` = 12.00, `supply_status` = 'IN_STOCK' WHERE `supply_id` = 5; -- Air Filter: was 15.00 IN_STOCK
UPDATE `tbl_supplies` SET `current_stock` = 10.00, `supply_status` = 'IN_STOCK' WHERE `supply_id` = 6; -- Fuel Filter: was 12.00 IN_STOCK
UPDATE `tbl_supplies` SET `current_stock` = 50.00, `supply_status` = 'IN_STOCK' WHERE `supply_id` = 7; -- Grease (Multi-Purpose): was 35.00 IN_STOCK
UPDATE `tbl_supplies` SET `current_stock` = 25.00, `supply_status` = 'IN_STOCK' WHERE `supply_id` = 9; -- Coolant (Long-Life): was 0.00 OUT_OF_STOCK
