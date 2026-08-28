<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TransactionsController extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->transactionModel = model('App\Models\TransactionsModel');
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('transactions/transactions-main');
                break;

            case 'SAVE': 
                $result = $this->transactionModel->saveTransaction();
                echo json_encode($result);
                break;

            case 'EDIT': 
                $result = $this->transactionModel->updateTransaction();
                echo json_encode($result);
                break;

            case 'DELETE': 
                $result = $this->transactionModel->deleteTransaction();
                echo json_encode($result);
                break;

            case 'CHECKOUT':
                $result = $this->transactionModel->checkoutTransaction();
                echo json_encode($result);
                break;

            case 'GET_AVAILABLE_BAYS':
                $result = $this->transactionModel->getAvailableBays();
                echo json_encode($result);
                break;
                
            case 'EDIT_PAYMENT':
                $result = $this->transactionModel->updatePayment();
                echo json_encode($result);
                break;


            case 'UPLOAD_DOCUMENT':
                try {
                    $result = $this->transactionModel->uploadDocument();
                } catch (\Exception $e) {
                    log_message('error', 'Document upload error: ' . $e->getMessage());
                    $result = ['status' => 'error', 'message' => 'Upload failed: ' . $e->getMessage()];
                }
                echo json_encode($result);
                break;

            case 'DELETE_DOCUMENT':
                $result = $this->transactionModel->deleteDocument();
                echo json_encode($result);
                break;

            case 'GET_DOCUMENTS':
                $transaction_id = $this->request->getPost('transaction_id');
                $result = $this->transactionModel->getDocuments($transaction_id);
                echo json_encode($result);
                break;

            case 'GET_TRANSACTION':
                $transaction_id = $this->request->getPost('transaction_id');
                $result = $this->transactionModel->getTransaction($transaction_id);
                echo json_encode($result);
                break;

            default:
                return view('transactions/transactions-main');
                break;
        }
    }
}