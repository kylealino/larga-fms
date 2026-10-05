<?php namespace App\Controllers;
  
use CodeIgniter\Controller;
  
class MyLogIn extends BaseController
{
	
	public function __construct()
	{
        $this->request = \Config\Services::request();
        $this->db = \Config\Database::connect();
        $this->session = session();
	}
		
    public function index()
    {
        echo view('mylogin');
    } 

    public function auth()
    {
        $meusername = $this->request->getPostGet('MyUsername');
        $password = $this->request->getPostGet('MyPassword');
        $data = $this->Verify_User($meusername)->getRowArray();

        if($data) {

            if ((int) $data['is_active'] === 0) {
                $this->session->setFlashdata('mesyszicas_memsg_login', 'This account has been deactivated.');
                return redirect()->to('/');
            }

            $passdb = $data['hash_password'];
            $verify_pass = $this->Verify_Password($passdb, $password);
            if($verify_pass) {
                // Only role_id/role_name are cached in session — actual permission flags
                // are looked up live per request (see permission_helper::user_can()) so
                // that a role's permissions take effect immediately, without re-login.
                $ses_data = array(
                '__xsys_myuserzicas_is_logged__' => TRUE,
                '__xsys_myuserzicas__' => $meusername,
                '__xsys_myuserrole__' => $data['role_id'],
                '__xsys_myuserrolename__' => $data['role_name'],
                );

                $this->session->set($ses_data);
                return redirect()->to('/myadmindashboard');

            } else {
                $this->session->setFlashdata('mesyszicas_memsg_login', 'Wrong Password');
                return redirect()->to('/');
            }
        } else {
            $this->session->setFlashdata('mesyszicas_memsg_login', 'User Name not Found');
            return redirect()->to('/');
        }
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }

    public function Verify_User($cuser='') {
		$q = $this->db->query("
			SELECT u.username, u.hash_password, u.is_active, u.role_id, r.role_name
			FROM myua_user u
			LEFT JOIN tbl_roles r ON u.role_id = r.role_id
			WHERE u.username = ?
			LIMIT 1
		", [$cuser]);
		return $q;
	}

    public function Verify_Password($cuserpassdb='',$cuserpass='') {
		$query = $this->db->query("SELECT IF(? = SHA2(?,512),1,0) metruefalse LIMIT 1", [$cuserpassdb, $cuserpass]);
		$row = $query->getRowArray();
		$query->freeResult();
		return $row['metruefalse'];
	}
}
