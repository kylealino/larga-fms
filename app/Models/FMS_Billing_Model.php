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
        $query = $this->db->query("SELECT IFNULL(MAX(CAST(SUBSTRING_INDEX(billing_code, '-', -1) AS UNSIGNED)), 0) as total FROM tbl_billing WHERE billing_code LIKE ?", ['%-' . $year . '-%']);
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
                   dr.dr_code,
                   (SELECT COALESCE(SUM(e.amount), 0) FROM tbl_dispatch_expenses e WHERE e.trip_id = b.trip_id) as expense_cost,
                   (SELECT COALESCE(SUM(a.rental_rate), 0) FROM tbl_trip_assignments a WHERE a.trip_id = b.trip_id) as rental_cost,
                   (SELECT COALESCE(SUM(e.amount), 0) FROM tbl_dispatch_expenses e WHERE e.trip_id = b.trip_id)
                   + (SELECT COALESCE(SUM(a.rental_rate), 0) FROM tbl_trip_assignments a WHERE a.trip_id = b.trip_id) as trip_cost
            FROM tbl_billing b
            LEFT JOIN tbl_customers c ON b.customer_id = c.customer_id
            LEFT JOIN tbl_trips t ON b.trip_id = t.trip_id
            LEFT JOIN tbl_delivery_receipts dr ON b.dr_id = dr.dr_id
            WHERE b.billing_id = ?
        ", [$billing_id]);
        return $query->getRowArray();
    }

    // ==============================
    // BILLABLE (REIMBURSABLE) DISPATCH EXPENSES → CHARGES
    // One way only: billable expenses become billing charges, linked by expense_id so
    // an expense is never billed twice. Billing-only charges never go back to dispatch.
    // ==============================
    const EXPENSE_CHARGE_TYPE = [
        'TOLL' => 'TOLL_FEES',
        'LOADING_UNLOADING' => 'HANDLING',
    ];
    const EXPENSE_LABEL = [
        'TOLL' => 'Toll', 'PARKING' => 'Parking', 'FUEL' => 'Fuel', 'MEALS' => 'Meals',
        'LOADING_UNLOADING' => 'Loading/Unloading', 'CONTAINER_RENTAL' => 'Container Rental',
        'EMPTY_RETURN_FEE' => 'Empty Return Fee', 'CONTAINER_RETURN_FEE' => 'Container Return Fee', 'OTHER' => 'Other',
    ];

    // Charges can only change while the billing is still open (not yet invoiced or cancelled)
    private function chargesLocked($billing_id)
    {
        $b = $this->db->query("SELECT billing_code, billing_status FROM tbl_billing WHERE billing_id = ?", [$billing_id])->getRow();
        if (!$b) return 'Billing not found.';
        if (in_array($b->billing_status, ['INVOICED', 'CANCELLED'])) {
            return "Billing {$b->billing_code} is already " . strtolower($b->billing_status) . ' — its charges can no longer change.';
        }
        return null;
    }

    // Billable expenses of the billing's trip that are not on any billing yet
    public function getUnbilledExpenses($billing_id)
    {
        return $this->db->query("
            SELECT e.expense_id, e.expense_code, e.expense_date, e.expense_type, e.description, e.amount, e.paid_by, e.reference_no
            FROM tbl_billing b
            JOIN tbl_dispatch_expenses e ON e.trip_id = b.trip_id
            WHERE b.billing_id = ? AND e.is_billable = 1
              AND NOT EXISTS (SELECT 1 FROM tbl_billing_charges c WHERE c.expense_id = e.expense_id)
            ORDER BY e.expense_date, e.expense_id
        ", [$billing_id])->getResultArray();
    }

    // Add billable expense(s) as charges — one expense_id, or all unbilled ones when empty
    private function addExpenseCharges($billing_id, $expense_id = null)
    {
        $added = 0;
        foreach ($this->getUnbilledExpenses($billing_id) as $e) {
            if ($expense_id && (int) $e['expense_id'] !== (int) $expense_id) continue;
            $label = self::EXPENSE_LABEL[$e['expense_type']] ?? 'Other';
            $description = $label . ($e['description'] ? ': ' . $e['description'] : '');
            $remarks = 'Reimbursable — dispatch expense ' . $e['expense_code']
                . ($e['paid_by'] ? ', paid by ' . $e['paid_by'] : '')
                . ($e['reference_no'] ? ', ref ' . $e['reference_no'] : '');
            $this->db->query("
                INSERT INTO `tbl_billing_charges`(
                    `billing_id`, `expense_id`, `charge_type`, `description`, `amount`, `remarks`, `created_by`
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ", [$billing_id, $e['expense_id'], self::EXPENSE_CHARGE_TYPE[$e['expense_type']] ?? 'OTHER', mb_substr($description, 0, 255), $e['amount'], $remarks, $this->cuser]);
            $added++;
        }
        if ($added) $this->recalculateBillingTotals($billing_id);
        return $added;
    }

    public function addExpenseCharge()
    {
        $billing_id = $this->request->getPost('billing_id');
        $expense_id = $this->request->getPost('expense_id') ?: null;

        $locked = $this->chargesLocked($billing_id);
        if ($locked) return ['status' => 'error', 'message' => $locked];

        $added = $this->addExpenseCharges($billing_id, $expense_id);
        if (!$added) {
            return ['status' => 'error', 'message' => 'That expense is already billed or is not billable.'];
        }
        return ['status' => 'success', 'message' => $added . ' reimbursable expense' . ($added > 1 ? 's' : '') . ' added as charge' . ($added > 1 ? 's' : '') . '.'];
    }

    // ==============================
    // CREATE BILLING FROM DR
    // ==============================
    // ==============================
    // AMOUNT RULES — rate/quantity/discount must not produce a negative bill
    // ==============================
    private function billingAmountsError($rate, $quantity, $discount)
    {
        if (!is_numeric($rate) || $rate < 0) {
            return 'The rate cannot be negative.';
        }
        if (!is_numeric($quantity) || $quantity <= 0) {
            return 'The quantity must be greater than zero.';
        }
        if (!is_numeric($discount) || $discount < 0) {
            return 'The discount cannot be negative.';
        }
        if ($discount > $rate * $quantity) {
            return 'The discount (₱' . number_format($discount, 2) . ') cannot be more than the subtotal (₱' . number_format($rate * $quantity, 2) . ').';
        }
        return null;
    }

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
            SELECT dr.dr_id, dr.trip_id, dr.customer_id, dr.dr_status, t.service_type
            FROM tbl_delivery_receipts dr
            LEFT JOIN tbl_trips t ON dr.trip_id = t.trip_id
            WHERE dr.dr_id = ?
        ", [$dr_id])->getRow();

        if (!$dr) {
            return ['status' => 'error', 'message' => 'Delivery receipt not found.'];
        }

        // Same rule as the billable DR list: only delivered (fully or partially) DRs can be billed
        if (!in_array($dr->dr_status, ['DELIVERED', 'PARTIALLY_DELIVERED'])) {
            return ['status' => 'error', 'message' => 'Only delivered or partially delivered receipts can be billed.'];
        }

        // Trip came from an ERP booking slip: prefill the rate from its trucking quote (still editable)
        if ((float) $rate <= 0) {
            $quote = $this->db->query("
                SELECT t.booking_reference, SUM(c.selling_php * c.quantity) as total,
                       GROUP_CONCAT(CONCAT(c.description, ' ', FORMAT(c.selling_php * c.quantity, 2)) ORDER BY c.charge_id SEPARATOR ' + ') as lines_desc
                FROM tbl_trip_booking_charges c JOIN tbl_trips t ON t.trip_id = c.trip_id
                WHERE c.trip_id = ?
                GROUP BY t.booking_reference
            ", [$dr->trip_id])->getRow();
            if ($quote && (float) $quote->total > 0) {
                $rate = $quote->total;
                $billing_basis = 'PER_TRIP';
                $quantity = 1;
                $remarks = trim(($remarks ? $remarks . ' | ' : '') . 'Rate from ERP booking ' . $quote->booking_reference . ' (VAT-exclusive, 12% VAT added once on this billing): ' . $quote->lines_desc);
            }
        }

        $amountsError = $this->billingAmountsError($rate, $quantity, $discount);
        if ($amountsError) {
            return ['status' => 'error', 'message' => $amountsError];
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
            // Billable dispatch expenses of the trip come in as reimbursable charges
            $added = $this->addExpenseCharges($billing_id);
            $message = 'Billing Created Successfully!' . ($added ? " {$added} reimbursable expense" . ($added > 1 ? 's' : '') . ' added as charge' . ($added > 1 ? 's' : '') . '.' : '');
            return ['status' => 'success', 'message' => $message, 'billing_id' => $billing_id];
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

        $amountsError = $this->billingAmountsError($rate, $quantity, $discount);
        if ($amountsError) {
            return ['status' => 'error', 'message' => $amountsError];
        }

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
            SELECT c.*, e.expense_code
            FROM tbl_billing_charges c
            LEFT JOIN tbl_dispatch_expenses e ON e.expense_id = c.expense_id
            WHERE c.billing_id = ? ORDER BY c.charge_id ASC
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

        $locked = $this->chargesLocked($billing_id);
        if ($locked) return ['status' => 'error', 'message' => $locked];
        if (!is_numeric($amount) || $amount <= 0) {
            return ['status' => 'error', 'message' => 'Enter a charge amount greater than zero.'];
        }

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
        $locked = $row ? $this->chargesLocked($row->billing_id) : 'Charge not found.';
        if ($locked) return ['status' => 'error', 'message' => $locked];
        if (!is_numeric($amount) || $amount <= 0) {
            return ['status' => 'error', 'message' => 'Enter a charge amount greater than zero.'];
        }

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

        $row = $this->db->query("SELECT billing_id, expense_id FROM tbl_billing_charges WHERE charge_id = ?", [$charge_id])->getRow();
        $locked = $row ? $this->chargesLocked($row->billing_id) : 'Charge not found.';
        if ($locked) return ['status' => 'error', 'message' => $locked];

        $query = $this->db->query("DELETE FROM `tbl_billing_charges` WHERE `charge_id` = ?", [$charge_id]);

        if ($query) {
            $this->recalculateBillingTotals($row->billing_id);
            // A removed reimbursable goes back to the "unbilled" list (untick Billable in dispatch to keep it internal)
            $message = $row->expense_id ? 'Charge removed — the expense is listed again under unbilled reimbursables.' : 'Additional Charge Deleted Successfully!';
            return ['status' => 'success', 'message' => $message];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting charge.'];
        }
    }

    // ==============================
    // RECALCULATE BILLING TOTALS (from rate/qty/discount + sum of charges)
    // ==============================
    public function recalculateBillingTotals($billing_id)
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
