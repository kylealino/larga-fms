<?php
namespace App\Models;
use CodeIgniter\Model;

class TransactionsModel extends Model
{
    protected $db;

    public function __construct(){
        parent::__construct();
        $this->session = session();
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function saveTransaction() 
    { 
        $transaction_date = $this->request->getPost('transaction_date');
        $checkin_time = $this->request->getPost('checkin_time');
        $checkout_time = $this->request->getPost('checkout_time');
        $stage_id = $this->request->getPost('stage_id');
        $shooter_type = $this->request->getPost('shooter_type');
        $shooter_name = $this->request->getPost('shooter_name');
        $range_assistant_id = $this->request->getPost('range_assistant_id');
        $rangefee_amount = $this->request->getPost('rangefee_amount') ?: 0;
        $targetboard_amount = $this->request->getPost('targetboard_amount') ?: 0;
        $ammunition_amount = $this->request->getPost('ammunition_amount') ?: 0;
        $total_amount = $rangefee_amount + $targetboard_amount + $ammunition_amount;
        $notes = $this->request->getPost('notes');

        // Check if bay/stage is available
        $bay_check = $this->db->query("
            SELECT status FROM tbl_bay_status WHERE stage_id = ? AND status = 'AVAILABLE'
        ", [$stage_id])->getRow();

        if(!$bay_check) {
            return ['status' => 'error', 'message' => 'Stage ' . $stage_id . ' is currently not available. Please select another stage.'];
        }

        // Begin transaction
        $this->db->transStart();

        // Insert transaction
        $query = $this->db->query("
            INSERT INTO `tbl_transactions`(
                `transaction_date`,
                `checkin_time`,
                `checkout_time`,
                `stage_id`,
                `shooter_type`,
                `shooter_name`,
                `range_assistant_id`,
                `rangefee_amount`,
                `targetboard_amount`,
                `ammunition_amount`,
                `total_amount`,
                `notes`,
                `created_by`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
            [
                $transaction_date,
                $checkin_time,
                $checkout_time,
                $stage_id,
                $shooter_type,
                $shooter_name,
                $range_assistant_id ?: NULL,
                $rangefee_amount,
                $targetboard_amount,
                $ammunition_amount,
                $total_amount,
                $notes,
                $this->cuser
            ]
        );

        if ($query) {
            $transaction_id = $this->db->insertID();
            
            // Update bay status to OCCUPIED
            $this->db->query("
                UPDATE tbl_bay_status 
                SET status = 'OCCUPIED', 
                    current_transaction_id = ?,
                    updated_at = NOW()
                WHERE stage_id = ?
            ", [$transaction_id, $stage_id]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return ['status' => 'error', 'message' => 'An error occurred while saving.'];
        } else {
            return ['status' => 'success', 'message' => 'Transaction Saved Successfully!', 'transaction_id' => $transaction_id];
        }
    }

    public function checkoutTransaction() 
    { 
        $transaction_id = $this->request->getPost('transaction_id');
        $checkout_time = $this->request->getPost('checkout_time') ?: date('H:i:s');

        // Get stage_id from transaction
        $stage = $this->db->query("
            SELECT stage_id FROM tbl_transactions WHERE transaction_id = ?
        ", [$transaction_id])->getRow();

        if(!$stage) {
            return ['status' => 'error', 'message' => 'Transaction not found.'];
        }

        $this->db->transStart();

        // Update transaction with checkout time and status
        $query = $this->db->query("
            UPDATE `tbl_transactions`
            SET 
                `checkout_time` = ?,
                `status` = 'COMPLETED',
                `updated_at` = NOW()
            WHERE `transaction_id` = ?
            ", 
            [$checkout_time, $transaction_id]
        );

        if ($query) {
            // Update bay status back to AVAILABLE
            $this->db->query("
                UPDATE tbl_bay_status 
                SET status = 'AVAILABLE', 
                    current_transaction_id = NULL,
                    updated_at = NOW()
                WHERE stage_id = ?
            ", [$stage->stage_id]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return ['status' => 'error', 'message' => 'An error occurred during checkout.'];
        } else {
            return ['status' => 'success', 'message' => 'Checkout completed successfully! Stage ' . $stage->stage_id . ' is now available.'];
        }
    }

    public function getAvailableBays()
    {
        $query = $this->db->query("
            SELECT stage_id, status 
            FROM tbl_bay_status 
            WHERE status = 'AVAILABLE' 
            ORDER BY stage_id
        ");
        return $query->getResultArray();
    }

    public function updateTransaction() 
    { 
        $transaction_id = $this->request->getPost('transaction_id');
        $transaction_date = $this->request->getPost('transaction_date');
        $checkin_time = $this->request->getPost('checkin_time');
        $checkout_time = $this->request->getPost('checkout_time');
        $stage_id = $this->request->getPost('stage_id');
        $shooter_type = $this->request->getPost('shooter_type');
        $shooter_name = $this->request->getPost('shooter_name');
        $range_assistant_id = $this->request->getPost('range_assistant_id');
        $rangefee_amount = $this->request->getPost('rangefee_amount') ?: 0;
        $targetboard_amount = $this->request->getPost('targetboard_amount') ?: 0;
        $ammunition_amount = $this->request->getPost('ammunition_amount') ?: 0;
        $total_amount = $rangefee_amount + $targetboard_amount + $ammunition_amount;
        $notes = $this->request->getPost('notes');

        $query = $this->db->query("
            UPDATE `tbl_transactions`
            SET 
                `transaction_date` = ?,
                `checkin_time` = ?,
                `checkout_time` = ?,
                `stage_id` = ?,
                `shooter_type` = ?,
                `shooter_name` = ?,
                `range_assistant_id` = ?,
                `rangefee_amount` = ?,
                `targetboard_amount` = ?,
                `ammunition_amount` = ?,
                `total_amount` = ?,
                `notes` = ?,
                `updated_at` = NOW()
            WHERE `transaction_id` = ?
            ", 
            [
                $transaction_date,
                $checkin_time,
                $checkout_time,
                $stage_id,
                $shooter_type,
                $shooter_name,
                $range_assistant_id ?: NULL,
                $rangefee_amount,
                $targetboard_amount,
                $ammunition_amount,
                $total_amount,
                $notes,
                $transaction_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Transaction Updated Successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating.'];
        }
    }

    public function deleteTransaction() 
    { 
        $transaction_id = $this->request->getPost('transaction_id');

        // Get stage_id first
        $stage = $this->db->query("
            SELECT stage_id FROM tbl_transactions WHERE transaction_id = ?
        ", [$transaction_id])->getRow();

        $this->db->transStart();

        // Delete transaction
        $query = $this->db->query("DELETE FROM `tbl_transactions` WHERE `transaction_id` = ?", [$transaction_id]);

        if ($query && $stage) {
            // Update bay status back to AVAILABLE
            $this->db->query("
                UPDATE tbl_bay_status 
                SET status = 'AVAILABLE', 
                    current_transaction_id = NULL,
                    updated_at = NOW()
                WHERE stage_id = ?
            ", [$stage->stage_id]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        } else {
            return ['status' => 'success', 'message' => 'Transaction Deleted Successfully!'];
        }
    }

    public function uploadDocument()
    {
        $transaction_id = $this->request->getPost('transaction_id');
        $document_type = $this->request->getPost('document_type');
        
        // Get the uploaded file
        $file = $this->request->getFile('document_file');
        
        // Check if file was uploaded
        if (!$file || !$file->isValid()) {
            return ['status' => 'error', 'message' => 'No valid file uploaded. Error: ' . ($file ? $file->getErrorString() : 'No file')];
        }
        
        // Check if file exists in temp location
        if (!file_exists($file->getTempName())) {
            return ['status' => 'error', 'message' => 'File upload failed. The temporary file does not exist.'];
        }

        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($file->getSize() > $maxSize) {
            return ['status' => 'error', 'message' => 'File size exceeds 5MB limit. Current size: ' . round($file->getSize() / 1024, 2) . ' KB'];
        }

        // Get file extension
        $extension = $file->getExtension();
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return ['status' => 'error', 'message' => 'Only JPG, PNG, GIF, and PDF files are allowed. Uploaded: ' . $extension];
        }

        // Generate unique filename
        $newName = $transaction_id . '_' . $document_type . '_' . time() . '.' . $extension;
        
        // Set upload path
        $uploadPath = FCPATH . 'uploads/documents/';
        
        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0777, true)) {
                return ['status' => 'error', 'message' => 'Failed to create upload directory. Please check permissions.'];
            }
        }

        // Check if directory is writable
        if (!is_writable($uploadPath)) {
            return ['status' => 'error', 'message' => 'Upload directory is not writable. Please check permissions for: ' . $uploadPath];
        }

        // Move file
        try {
            if ($file->move($uploadPath, $newName)) {
                $file_path = 'uploads/documents/' . $newName;
                $file_size = round($file->getSize() / 1024, 2) . ' KB';
                
                // Get mime type from the moved file
                $fullPath = $uploadPath . $newName;
                $mime_type = mime_content_type($fullPath);
                
                if (!$mime_type) {
                    $mime_type = $file->getClientMimeType() ?: 'application/octet-stream';
                }
                
                // Save to database
                $query = $this->db->query("
                    INSERT INTO `tbl_transaction_documents`(
                        `transaction_id`,
                        `document_type`,
                        `document_name`,
                        `document_path`,
                        `file_size`,
                        `file_type`,
                        `uploaded_by`
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?)", 
                    [
                        $transaction_id,
                        $document_type,
                        $file->getName(),
                        $file_path,
                        $file_size,
                        $mime_type,
                        $this->cuser
                    ]
                );

                if ($query) {
                    return ['status' => 'success', 'message' => 'Document uploaded successfully!'];
                } else {
                    // Delete file if database insert failed
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                    return ['status' => 'error', 'message' => 'Failed to save document record to database.'];
                }
            } else {
                return ['status' => 'error', 'message' => 'Failed to move uploaded file. Please check folder permissions.'];
            }
        } catch (\Exception $e) {
            log_message('error', 'File upload exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Upload error: ' . $e->getMessage()];
        }
    }

    public function deleteDocument()
    {
        $document_id = $this->request->getPost('document_id');

        // Get document path first
        $doc = $this->db->query("SELECT document_path FROM `tbl_transaction_documents` WHERE document_id = ?", [$document_id])->getRow();
        
        $query = $this->db->query("DELETE FROM `tbl_transaction_documents` WHERE `document_id` = ?", [$document_id]);

        if ($query) {
            // Delete physical file after successful database deletion
            if ($doc) {
                $file_path = FCPATH . $doc->document_path;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            return ['status' => 'success', 'message' => 'Document deleted successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while deleting.'];
        }
    }

    public function getDocuments($transaction_id)
    {
        $query = $this->db->query("
            SELECT * FROM `tbl_transaction_documents` 
            WHERE transaction_id = ? 
            ORDER BY uploaded_at DESC
        ", [$transaction_id]);
        
        return $query->getResultArray();
    }

    public function getTransaction($transaction_id)
    {
        $query = $this->db->query("
            SELECT t.*, 
                   ra.full_name as assistant_name,
                   ra.badge_number as assistant_badge,
                   bs.status as bay_status
            FROM `tbl_transactions` t
            LEFT JOIN `tbl_range_assistants` ra ON t.range_assistant_id = ra.assistant_id
            LEFT JOIN `tbl_bay_status` bs ON t.stage_id = bs.stage_id
            WHERE t.transaction_id = ?
        ", [$transaction_id]);
        
        return $query->getRowArray();
    }

    public function updatePayment()
    {
        $transaction_id = $this->request->getPost('transaction_id');
        $rangefee_amount = $this->request->getPost('rangefee_amount') ?: 0;
        $targetboard_amount = $this->request->getPost('targetboard_amount') ?: 0;
        $ammunition_amount = $this->request->getPost('ammunition_amount') ?: 0;
        $total_amount = $rangefee_amount + $targetboard_amount + $ammunition_amount;
        $notes = $this->request->getPost('notes');

        $query = $this->db->query("
            UPDATE `tbl_transactions`
            SET 
                `rangefee_amount` = ?,
                `targetboard_amount` = ?,
                `ammunition_amount` = ?,
                `total_amount` = ?,
                `notes` = ?,
                `updated_at` = NOW()
            WHERE `transaction_id` = ?
            ", 
            [
                $rangefee_amount,
                $targetboard_amount,
                $ammunition_amount,
                $total_amount,
                $notes,
                $transaction_id
            ]
        );

        if ($query) {
            return ['status' => 'success', 'message' => 'Payment updated successfully!'];
        } else {
            return ['status' => 'error', 'message' => 'An error occurred while updating payment.'];
        }
    }
}