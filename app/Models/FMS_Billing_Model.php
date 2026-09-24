<?php
namespace App\Models;
use CodeIgniter\Model;

class FMS_Billing_Model extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    // ==============================
    // GENERATE BILLING CODE
    // ==============================
    private function generateBillingCode()
    {
        $year = date('Y');
        $query = $this->db->query("SELECT COUNT(*) as total FROM tbl_billing WHERE YEAR(created_at) = ?", [$year]);
        $count = $query->getRow()->total + 1;
        return 'BILL-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    // ==============================
    // GET DELIVERED DRs WITHOUT A BILLING YET
    // ==============================
    public function getBillableDRs()
    {
        return $this->db->query("
            SELECT dr.dr_id, dr.dr_code, dr.dr_date, dr.trip_id, dr.customer_id,
                   t.trip_code, t.service_type,
                   c.customer_name
            FROM tbl_delivery_receipts dr
            LEFT JOIN tbl_trips t ON dr.trip_id = t.trip_id
            LEFT JOIN tbl_customers c ON dr.customer_id = c.customer_id
            WHERE dr.dr_status IN ('DELIVERED', 'PARTIALLY_DELIVERED')
              AND NOT EXISTS (
                  SELECT 1 FROM tbl_billing b WHERE b.dr_id = dr.dr_id
              )
            ORDER BY dr.dr_date DESC
        ")->getResultArray();
    }

    // ==============================
    // GET ALL BILLINGS
    // ==============================
    public function getAllBillings()
    {
        return $this->db->query("
            SELECT b.*,
                   c.customer_name,
                   t.trip_code,
                   dr.dr_code
            FROM tbl_billing b
            LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
            LEFT JOIN tbl_trips t ON b.trip_id = t.trip_id
            LEFT JOIN tbl_delivery_receipts dr ON b.dr_id = dr.dr_id
            ORDER BY b.billing_date DESC, b.billing_id DESC
        ")->getResultArray();
    }

    // ==============================
    // GET SINGLE BILLING
    // ==============================
    public function getBilling($billing_id)
    {
        $query = $this->db->query("
            SELECT b.*,
                   c.customer_name, c.payment_terms as customer_payment_terms,
                   t.trip_code, t.origin, t.destination,
                   dr.dr_code
            FROM tbl_billing b
            LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
            LEFT JOIN tbl_trips t ON b.trip_id = t.trip_id
            LEFT JOIN tbl_delivery_receipts dr ON b.dr_id = dr.dr_id
            WHERE b.billing_id = ?
        ", [$billing_id]);
        return $query->getRowArray();
    }

    // ==============================
    // CREATE BILLING FROM DR
    // ==============================
    public function createBillingFromDR()
    {
        $dr_id = $this->request->getPost('dr_id');
        $billing_date = $this->request->getPost('billing_date') ?: date('Y-m-d');
        $billing_basis = $this->request->getPost('billing_basis') ?: 'PER_TRIP';
        $rate = $this->request->getPost('rate') ?: 0;
        $quantity = $this->request->getPost('quantity') ?: 1;
        $discount = $this->request->getPost('discount') ?: 0;
        $remarks = $this->request->getPost('remarks');

        if (!$dr_id) {
            return ['status' => 'error', 'message' => 'Missing delivery receipt reference.'];
        }

        $existing = $this->db->query("SELECT billing_id FROM tbl_billing WHERE dr_id = ? LIMIT 1", [$dr_id])->getRow();
        if ($existing) {
            return ['status' => 'error', 'message' => 'A billing already exists for this delivery receipt.'];
        }

        $dr = $this->db->query("
            SELECT dr.dr_id, dr.trip_id, dr.customer_id, t.service_type
            FROM tbl_delivery_receipts dr
            LEFT JOIN tbl_trips t ON dr.trip_id = t.trip_id
            WHERE dr.dr_id = ?
        ", [$dr_id])->getRow();

        if (!$dr) {
            return ['status' => 'error', 'message' => 'Delivery receipt not found.'];
        }

        $billing_code = $this->generateBillingCode();

        $subtotal = $rate * $quantity;
        $taxable = $subtotal - $discount;
        $vat = round($taxable * 0.12, 2);
        $other_charges = 0;
        $total = $taxable + $vat + $other_charges;

        $query = $this->db->query("
            INSERT INTO `tbl_billing`(
                `billing_code`, `customer_id`, `trip_id`, `dr_id`, `billing_date`,
                `service_type`, `billing_basis`, `rate`, `quantity`,
                `subtotal`, `discount`, `vat`, `other_charges`, `total`,
                `billing_status`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'DRAFT', ?, ?)
        ", [
            $billing_code, $dr->customer_id, $dr->trip_id, $dr->dr_id, $billing_date,
            $dr->service_type, $billing_basis, $rate, $quantity,
            $subtotal, $discount, $vat, $other_charges, $total,
            $remarks, $this->cuser
        ]);

        if ($query) {
            $billing_id = $this->db->insertID();
            return ['status' => 'success', 'message' => 'Billing Created Successfully!', 'billing_id' => $billing_id];
        } else {
            $error = $this->db->error();
            log_message('error', 'Billing Create Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while creating billing.'];
        }
    }

    // ==============================
    // UPDATE BILLING
    // ==============================
    public function updateBilling()
    {
        $billing_id = $this->request->getPost('billing_id');
        $billing_date = $this->request->getPost('billing_date');
        $billing_basis = $this->request->getPost('billing_basis') ?: 'PER_TRIP';
        $rate = $this->request->getPost('rate') ?: 0;
        $quantity = $this->request->getPost('quantity') ?: 1;
        $discount = $this->request->getPost('discount') ?: 0;
        $billing_status = $this->request->getPost('billing_status') ?: 'DRAFT';
        $remarks = $this->request->getPost('remarks');

        $existing = $this->db->query("SELECT other_charges FROM tbl_billing WHERE billing_id = ?", [$billing_id])->getRow();
        $other_charges = $existing ? $existing->other_charges : 0;

        $subtotal = $rate * $quantity;
        $taxable = $subtotal - $discount;
        $vat = round($taxable * 0.12, 2);
        $total = $taxable + $vat + $other_charges;

        $query = $this->db->query("
            UPDATE `tbl_billing`
            SET
                `billing_date` = ?, `billing_basis` = ?, `rate` = ?, `quantity` = ?,
                `subtotal` = ?, `discount` = ?, `vat` = ?, `total` = ?,
                `billing_status` = ?, `remarks` = ?, `updated_at` = NOW()
            WHERE `billing_id` = ?
        ", [
            $billing_date, $billing_basis, $rate, $quantity,
            $subtotal, $discount, $vat, $total,
            $billing_status, $remarks, $billing_id
        ]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Billing Updated Successfully!'];
        } else {
            $error = $this->db->error();
            log_message('error', 'Billing Update Error: ' . print_r($error, true));
            return ['status' => 'error', 'message' => 'An error occurred while updating billing.'];
        }
    }

    // ==============================
    // DELETE BILLING
    // ==============================
    public function deleteBilling()
    {
        $billing_id = $this->request->getPost('billing_id');

        $billing = $this->db->query("SELECT billing_status FROM tbl_billing WHERE billing_id = ?", [$billing_id])->getRow();
        if ($billing && $billing->billing_status === 'INVOICED') {
            return ['status' => 'error', 'message' => 'Cannot delete a billing that already has an invoice.'];
        }

        $this->db->query("DELETE FROM `tbl_billing_charges` WHERE `billing_id` = ?", [$billing_id]);
        $query = $this->db->query("DELETE FROM `tbl_billing` WHERE `billing_id` = ?", [$billing_id]);

        if ($query) {
            return ['status' => 'success', 'message' => 'Billing Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting billing.'];
        }
    }

    // ==============================
    // ADDITIONAL CHARGES
    // ==============================
    public function getCharges($billing_id)
    {
        return $this->db->query("
            SELECT * FROM tbl_billing_charges WHERE billing_id = ? ORDER BY charge_id ASC
        ", [$billing_id])->getResultArray();
    }

    public function getCharge($charge_id)
    {
        $query = $this->db->query("SELECT * FROM tbl_billing_charges WHERE charge_id = ?", [$charge_id]);
        return $query->getRowArray();
    }

    public function saveCharge()
    {
        $billing_id = $this->request->getPost('billing_id');
        $charge_type = $this->request->getPost('charge_type') ?: 'OTHER';
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount') ?: 0;
        $remarks = $this->request->getPost('remarks');

        $query = $this->db->query("
            INSERT INTO `tbl_billing_charges`(
                `billing_id`, `charge_type`, `description`, `amount`, `remarks`, `created_by`
            ) VALUES (?, ?, ?, ?, ?, ?)
        ", [$billing_id, $charge_type, $description, $amount, $remarks, $this->cuser]);

        if ($query) {
            $this->recalculateBillingTotals($billing_id);
            return ['status' => 'success', 'message' => 'Additional Charge Added Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while adding charge.'];
        }
    }

    public function updateCharge()
    {
        $charge_id = $this->request->getPost('charge_id');
        $charge_type = $this->request->getPost('charge_type') ?: 'OTHER';
        $description = $this->request->getPost('description');
        $amount = $this->request->getPost('amount') ?: 0;
        $remarks = $this->request->getPost('remarks');

        $row = $this->db->query("SELECT billing_id FROM tbl_billing_charges WHERE charge_id = ?", [$charge_id])->getRow();

        $query = $this->db->query("
            UPDATE `tbl_billing_charges`
            SET `charge_type` = ?, `description` = ?, `amount` = ?, `remarks` = ?
            WHERE `charge_id` = ?
        ", [$charge_type, $description, $amount, $remarks, $charge_id]);

        if ($query) {
            if ($row) $this->recalculateBillingTotals($row->billing_id);
            return ['status' => 'success', 'message' => 'Additional Charge Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating charge.'];
        }
    }

    public function deleteCharge()
    {
        $charge_id = $this->request->getPost('charge_id');

        $row = $this->db->query("SELECT billing_id FROM tbl_billing_charges WHERE charge_id = ?", [$charge_id])->getRow();
        $query = $this->db->query("DELETE FROM `tbl_billing_charges` WHERE `charge_id` = ?", [$charge_id]);

        if ($query) {
            if ($row) $this->recalculateBillingTotals($row->billing_id);
            return ['status' => 'success', 'message' => 'Additional Charge Deleted Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting charge.'];
        }
    }

    // ==============================
    // RECALCULATE BILLING TOTALS (from rate/qty/discount + sum of charges)
    // ==============================
    private function recalculateBillingTotals($billing_id)
    {
        $billing = $this->db->query("SELECT rate, quantity, discount FROM tbl_billing WHERE billing_id = ?", [$billing_id])->getRow();
        if (!$billing) return;

        $chargesSum = $this->db->query("SELECT COALESCE(SUM(amount),0) as total FROM tbl_billing_charges WHERE billing_id = ?", [$billing_id])->getRow()->total;

        $subtotal = $billing->rate * $billing->quantity;
        $taxable = $subtotal - $billing->discount;
        $vat = round($taxable * 0.12, 2);
        $total = $taxable + $vat + $chargesSum;

        $this->db->query("
            UPDATE tbl_billing
            SET subtotal = ?, vat = ?, other_charges = ?, total = ?, updated_at = NOW()
            WHERE billing_id = ?
        ", [$subtotal, $vat, $chargesSum, $total, $billing_id]);
    }
}
