-- =============================================
-- PHASE 12: BILLABLE (REIMBURSABLE) DISPATCH EXPENSES → BILLING CHARGES
-- Dispatch expenses are trip COSTS; billing charges are what the CUSTOMER pays.
-- Only expenses marked billable (pass-through costs paid for the client: tolls,
-- parking, port/container fees, loading) flow ONE WAY into billing as charges.
-- tbl_billing_charges.expense_id remembers the source expense (plain reference, no FK)
-- so an expense is never billed twice. Billing-only charges (waiting time,
-- detention…) stay in billing — they are revenue, not costs.
-- =============================================

ALTER TABLE `tbl_dispatch_expenses`
    ADD COLUMN IF NOT EXISTS `is_billable` tinyint(1) NOT NULL DEFAULT 0 AFTER `amount`,
    ADD KEY IF NOT EXISTS `idx_trip_id` (`trip_id`);

ALTER TABLE `tbl_billing_charges`
    ADD COLUMN IF NOT EXISTS `expense_id` int(11) DEFAULT NULL AFTER `billing_id`,
    ADD KEY IF NOT EXISTS `idx_expense_id` (`expense_id`);

-- Blank type (an invalid value saved silently before validation existed) → OTHER
UPDATE `tbl_dispatch_expenses` SET `expense_type` = 'OTHER' WHERE `expense_type` = '' OR `expense_type` IS NULL;

-- Default: pass-through costs are billable; fuel, meals and other are internal
UPDATE `tbl_dispatch_expenses` SET `is_billable` = 1
WHERE `expense_type` IN ('TOLL', 'PARKING', 'LOADING_UNLOADING', 'CONTAINER_RENTAL', 'EMPTY_RETURN_FEE', 'CONTAINER_RETURN_FEE');
