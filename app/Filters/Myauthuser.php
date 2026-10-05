<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Myauthuser implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('__xsys_myuserzicas_is_logged__'))
        {
            return redirect()
                ->to('/');
        }

        helper('permission');
        $module_key = trim((string) $request->getUri()->getSegment(1));

        if ($module_key !== '' && !user_can($module_key, 'view')) {
            session()->setFlashdata('mesyszicas_memsg_access', 'You do not have access to that module.');
            return redirect()->to('/myadmindashboard');
        }
    }
    
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}  //end main class
