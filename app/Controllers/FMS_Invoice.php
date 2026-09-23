<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FMS_Invoice extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->invoiceModel = model('App\Models\FMS_Invoice_Model');
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
                return view('fms/invoice/invoice-main');
                break;

            // ==============================
            // INVOICE CRUD
            // ==============================
            case 'GET_INVOICEABLE_BILLINGS':
                try { $result = $this->invoiceModel->getInvoiceableBillings(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'CREATE_INVOICE_FROM_BILLING':
                try { $result = $this->invoiceModel->createInvoiceFromBilling(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'GET_ALL_INVOICES':
                try { $result = $this->invoiceModel->getAllInvoices(); }
                catch (\Throwable $e) { $result = []; }
                echo json_encode($result);
                break;

            case 'GET_INVOICE':
                $invoice_id = $this->request->getPost('invoice_id');
                try { $result = $this->invoiceModel->getInvoice($invoice_id); }
                catch (\Throwable $e) { $result = null; }
                echo json_encode($result);
                break;

            case 'UPDATE_INVOICE':
                try { $result = $this->invoiceModel->updateInvoice(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            case 'DELETE_INVOICE':
                try { $result = $this->invoiceModel->deleteInvoice(); }
                catch (\Throwable $e) { $result = ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]; }
                echo json_encode($result);
                break;

            // ==============================
            // PDF
            // ==============================
            case 'PRINT-INVOICE':
                return view('fms/invoice/invoice-pdf');
                break;

            default:
                return view('fms/invoice/invoice-main');
                break;
        }
    }
}
