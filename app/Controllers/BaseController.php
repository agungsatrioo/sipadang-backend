<?php

namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\Controller;

class BaseController extends Controller
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */

	protected $helpers = [];

	public function __construct()
	{
		helper(['url', 'form', 'html']);
	}

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();
		$this->db = \Config\Database::connect();
		$this->session = \Config\Services::session();
	}

	public function renderPage($page, $data)
	{

		$data['css'] = "";
		$data['js']  = "";

		$css = [
			"public/media/vendor/fontawesome-free/css/all.min.css",
			"public/media/sbadmin2/css/sb-admin-2.min.css",
			"public/media/jquery_chosen/chosen.min.css",
			"public/media/vendor/datatables/dataTables.bootstrap4.min.css",
			"public/media/custom.css"
		];

		$js = [
			"public/media/vendor/jquery/jquery.min.js",
			"public/media/vendor/bootstrap/js/bootstrap.bundle.min.js",
			"public/media/vendor/jquery-easing/jquery.easing.min.js",
			"public/media/jquery_chosen/chosen.jquery.min.js",
			"public/media/sbadmin2/js/sb-admin-2.min.js",
			"public/media/vendor/chart.js/Chart.min.js",
			"public/media/vendor/datatables/jquery.dataTables.min.js",
			"public/media/vendor/datatables/dataTables.bootstrap4.min.js",
		];

		foreach ($css as $cssItem) {
			$data['css'] .= link_tag($cssItem);
		}

		foreach ($js as $jsItem) {
			$data['js'] .= script_tag($jsItem);
		}

		$data['page'] = view($page, $data);

		return view('ViewBase', $data);
	}

	public function renderPaper($data) {
		$landscape 	= !empty($data['landscape']) ? $data['landscape'] : true;
		$pageSize	= 'letter';

		$data['paperSize'] = $landscape ? "$pageSize landscape" : $pageSize;

		$data['css'] = "";

		$css = [
			"public/media/paper-css/paper.css",
			"public/media/paper-css/flexboxgrid.css",
		];

		foreach ($css as $cssItem) {
			$data['css'] .= link_tag($cssItem);
		}

		return view('PaperView', $data);
	}
}


/*

  <!-- Page level plugins -->
  <script src="vendor/chart.js/Chart.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-pie-demo.js"></script>
*/