<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class RESTBaseController extends ResourceController
{

    protected $helpers = [];
    protected $format       = 'json';

	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

        $this->session = \Config\Database::connect();
        $this->session = \Config\Services::session();
	}

}
