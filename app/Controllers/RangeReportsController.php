<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class RangeReportsController extends BaseController
{
    public function __construct()
    {
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
    }

    public function index() 
    {
        $meaction = $this->request->getPostGet('meaction');

        switch ($meaction) {
            case 'MAIN': 
                return view('rangereports/rangereports-main');
                break;

            case 'PRINT-DAILY-SUMMARY': 
                return view('rangereports/rangereports-daily-summary-pdf');
                break;

            case 'PRINT-SHOOTER-LOG': 
                return view('rangereports/rangereports-shooter-log-pdf');
                break;

            case 'PRINT-REVENUE-SUMMARY': 
                return view('rangereports/rangereports-revenue-summary-pdf');
                break;

            case 'PRINT-STAGE-USAGE': 
                return view('rangereports/rangereports-stage-usage-pdf');
                break;

            case 'PRINT-SHOOTER-HISTORY': 
                return view('rangereports/rangereports-shooter-history-pdf');
                break;

            default:
                return view('rangereports/rangereports-main');
                break;
        }
    }
}