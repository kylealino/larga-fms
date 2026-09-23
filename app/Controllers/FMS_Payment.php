<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Payment extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->paymentModel = model('App\Models\FMS_Payment_Model');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index()
    {
        $meaction = $this->request->getPostGet('meaction');

        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        switch ($meaction) {
            case 'MAIN':
                return view('fms/payment/payment-main');
                break;

            // ==============================
            // PAYMENT CRUD
            // ==============================
            case 'GET_PAYABLE_INVOICES':
                try { $result = $this->paymentModel->getPayableInvoices(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_ALL_PAYMENTS':
                try { $result = $this->paymentModel->getAllPayments(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_PAYMENT':
                $payment_id = $this->request->getPost('payment_id');
                try { $result = $this->paymentModel->getPayment($payment_id); }
                catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'SAVE_PAYMENT':
                try { $result = $this->paymentModel->savePayment(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPDATE_PAYMENT':
                try { $result = $this->paymentModel->updatePayment(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_PAYMENT':
                try { $result = $this->paymentModel->deletePayment(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'UPLOAD_RECEIPT':
                try { $result = $this->paymentModel->uploadReceipt(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            default:
                return view('fms/payment/payment-main');
                break;
        }
    }
}
