-- =============================================
-- PHASE 1: DROP ORPHAN TABLES
-- Shooting-range leftovers + unused container return table.
-- Data preserved in __db_backup/largafms (10052026 pre-streamline).sql
-- =============================================
DROP TABLE IF EXISTS `tbl_transaction_documents`;
DROP TABLE IF EXISTS `tbl_transactions`;
DROP TABLE IF EXISTS `tbl_bay_status`;
DROP TABLE IF EXISTS `tbl_range_assistants`;
DROP TABLE IF EXISTS `tbl_delivery_receipt_container_return`;
