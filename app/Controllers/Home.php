<?php

namespace App\Controllers;
use CodeIgniter\HTTP\Response;
class Home extends BaseController
{
    public function __construct(){
		$this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->session = session();
        $this->cuser = $this->session->get('__xsys_myuserzicas__');
	}
    public function index(): string
    {
        return view('MyLogin-admin'); 
    }
    
}
