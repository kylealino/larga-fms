-- =============================================
-- PHASE 15: RE-INVOICE A BILLING AFTER ITS INVOICE IS CANCELLED
-- A cancelled / voided invoice is kept (official record) and its billing goes back
-- to FOR_INVOICE so a replacement invoice can be issued. tbl_invoices.billing_id
-- therefore can't be UNIQUE any more — "one ACTIVE invoice per billing" is enforced
-- in FMS_Invoice_Model::createInvoiceFromBilling() (ignores CANCELLED / VOID).
-- Found by the 2026-10-10 end-to-end test: before, a cancelled invoice left its
-- billing stuck INVOICED forever (no new invoice, no edits, no delete).
-- =============================================

ALTER TABLE `tbl_invoices`
    DROP INDEX IF EXISTS `billing_id`,
    ADD KEY IF NOT EXISTS `idx_billing_id` (`billing_id`);
