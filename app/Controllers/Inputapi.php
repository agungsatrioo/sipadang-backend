<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\RESTful\ResourceController;
use Exception;
use Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: * ");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Inputapi extends ResourceController
{
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		parent::initController($request, $response, $logger);

		$this->db = \Config\Database::connect();
		$this->session = \Config\Services::session();
	}

	public function explain_error($code)
	{
		switch ($code) {
			case 1062:
				return "Data yang Anda masukkan sudah ada sebelumnya.";
			case 1053:
				return "Server sedang dimatikan.";
			default:
				return "Kesalahan yang tidak diketahui. Silakan hubungi administrator.";
		}
	}

	private function verifyApp()
	{
		$authProvider = new AuthModel($this->db);

		$myPrivateKey = $authProvider->getPrivateKey();

		$authHeader = $this->request->getServer('HTTP_AUTHORIZATION');

		try {
			if (empty($authHeader)) return false;

			$arr = explode(" ", $authHeader);

			$token = $arr[1];

			$decoded = JWT::decode($token, $myPrivateKey, array('HS256'));

			if ($decoded) {
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	private function my_response($result)
	{
		switch ($result) {
			case "ok":
				return $this->respond(["info" => "ok"], 200);
			default:
				return $this->respond([
					"code" => $result,
					"error" => $this->explain_error($result)
				], 400);
		}
	}

	private function error_response($array, $code)
	{
		return array_merge([
			"code" => $code
		], $array);
	}

	public function mahasiswa($nim = null)
	{
		$mhsModel = new \App\Models\MahasiswaModel($this->db);

		$query = $mhsModel->findMahasiswa($nim);

		$results = $query->getResultArray();

		return $this->respond($results, 200);
	}

	public function dosen($id = null)
	{
		$dosenModel = new \App\Models\DosenModel($this->db);

		$query = $dosenModel->findDosen($id);

		$results = $query->getResultArray();

		return $this->respond($results, 200);
	}

	public function up()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$nim = $data->getGet('mahasiswa');
		$id_dosen = $data->getGet('dosen');

		$query = $sidangModel->getUP($id_dosen, $nim);

		foreach ($query as $key => $item) {
			$ada_nilai = true;

			if (isset($nim)) {
				$penguji        = $sidangModel->getStatusDosenDiSidang($item->nim, "Penguji Sidang Proposal %");
				$item->penguji  = $penguji;

				foreach ($item->penguji as $k1 => $v1) {
					if (!is_numeric($v1->nilai)) {
						$item->nilai = ["nilai" => $v1->nilai, "mutu" => $v1->mutu, "color" => $v1->color];
						$ada_nilai   = false;
						break;
					}
				}

				if ($ada_nilai) {
					$nilai = (.5 * $item->penguji[0]->nilai) + (.5 * $item->penguji[1]->nilai);
					$item->nilai = ["nilai" => floor($nilai), "mutu" => $sidangModel->_mutu($nilai), "color" => $sidangModel->warna($nilai)];
				}

				break;
			} else {
				$item->nilai = $sidangModel->cekNilai($item->id_status)[0];
			}
		}

		return $this->respond(isset($nim) ? $query[0] : $query, 200);
	}

	public function munaqosah()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$nim = $data->getGet('mahasiswa');
		$id_dosen = $data->getGet('dosen');

		$query = $sidangModel->getMunaqosah($id_dosen, $nim);

		foreach ($query as $key => $item) {
			$ada_nilai = true;

			if (isset($nim)) {
				$penguji        = $sidangModel->getStatusDosenDiSidang($item->nim, "Penguji Sidang Munaqosah %");
				$pembimbing        = $sidangModel->getStatusDosenDiSidang($item->nim, "Pembimbing Munaqosah %");
				$dosenku        = $sidangModel->getStatusDosenDiSidang($item->nim, "Munaqosah %");

				$item->penguji  = $penguji;
				$item->pembimbing  = $pembimbing;

				foreach ($dosenku as $k1 => $v1) {
					if (!is_numeric($v1->nilai)) {
						$item->nilai = ["nilai" => $v1->nilai, "mutu" => $v1->mutu, "color" => $v1->color];
						$ada_nilai   = false;
						break;
					}
				}

				if ($ada_nilai) {
					$nilai = (.3 * $item->penguji[0]->nilai) + (.3 * $item->penguji[1]->nilai) +  (.2 * $item->pembimbing[0]->nilai) + (.2 * $item->pembimbing[1]->nilai);

					$item->nilai = ["nilai" => floor($nilai), "mutu" => $sidangModel->_mutu($nilai), "color" => $sidangModel->warna($nilai)];
				}

				break;
			} else {
				$item->nilai = $sidangModel->cekNilai($item->id_status)[0];
			}
		}

		return $this->respond(isset($nim) ? $query[0] : $query, 200);
	}

	public function kompre()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$nim = $data->getGet('mahasiswa');
		$id_dosen = $data->getGet('dosen');

		$presentase_kompre = .333333333; //must be precise!

		$query = $sidangModel->getKompre($id_dosen, $nim);

		foreach ($query as $key => $item) {
			$ada_nilai = true;

			if (isset($nim)) {
				$penguji        = $sidangModel->getStatusDosenDiSidang($item->nim, "Penguji Sidang Komprehensif %");
				$item->penguji  = $penguji;

				foreach ($item->penguji as $k1 => $v1) {
					if (!is_numeric($v1->nilai)) {
						$item->nilai = ["nilai" => $v1->nilai, "mutu" => $v1->mutu, "color" => $v1->color];
						$ada_nilai   = false;
						break;
					}
				}

				if ($ada_nilai) {
					$nilai = ($presentase_kompre * $item->penguji[0]->nilai) + ($presentase_kompre * $item->penguji[1]->nilai) + ($presentase_kompre * $item->penguji[2]->nilai);

					if ($nilai > 100) $nilai = 100;

					$item->nilai = ["nilai" => floor($nilai), "mutu" => $sidangModel->_mutu($nilai), "color" => $sidangModel->warna($nilai)];
				}

				break;
			} else {
				$item->nilai = $sidangModel->cekNilai($item->id_status)[0];
			}
		}

		return $this->respond(isset($nim) ? $query[0] : $query, 200);
	}

	public function cek_nilai()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$id_status = $data->getGet('id_status');

		$query = $sidangModel->cekNilai($id_status);

		foreach ($query as $key => $item) {
			$item->color = $sidangModel->warna($item->nilai);
		}

		if (empty($query)) $query = [["nilai" => "Belum ada", "mutu" => "Belum ada", "color" => "#000000"]];

		return $this->respond($query, 200);
	}

	public function get_revisi()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$status = $data->getGet('id_status');
		$mhs    = $data->getGet('mahasiswa');
		$revid  = $data->getGet('id_revisi');

		$result = [];

		if (isset($status)) {
			$result =  $sidangModel->getRevisi(["t_status.id_status" => $status]);
		} elseif (isset($mhs)) {
			$result = $sidangModel->getRevisi(["t_mahasiswa.nim" => $mhs]);
		} elseif (isset($revid)) {
			$result = $sidangModel->getRevisi(["id_revisi" => $revid]);
		}

		return $this->respond($result, 200);
	}

	public function input_revisi($edit = false)
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$id         = $edit ? $data->getRawInput()['id_status'] : $data->getPost("id_status");
		$id_revisi    = $edit ? $data->getRawInput()['id_revisi'] : null;
		$details    = $edit ? $data->getRawInput()['detail_revisi'] : $data->getPost("detail_revisi");
		$deadline   = $edit ? $data->getRawInput()['tgl_revisi_deadline'] : $data->getPost("tgl_revisi_deadline");
		$status     = $edit ? $data->getRawInput()['status_revisi'] : $data->getPost("status_revisi");

		try {
			$result = $edit ? $sidangModel->editRevisi($id_revisi, $id, $details, $deadline) : $sidangModel->addRevisi($id, $details, $deadline, $status);

			return $this->my_response($result);
		} catch (Exception $e) {
			print_r($e);
			return $this->my_response("1069");
		}
	}

	public function delete_revisi()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$id_status =  $data->getRawInput()['id_status'];
		$id_revisi =  $data->getRawInput()['id_revisi'];

		if (isset($id_status)) {
			try {
				$result = $sidangModel->deleteRevisi($id_revisi, $id_status);

				return $this->my_response($result);
			} catch (Exception $e) {
				return $this->my_response("1066");
			}
		} else {
			return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);
		}
	}

	public function mark_revisi()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$id_status         = $data->getRawInput()['id_status'];
		$id_revisi         = $data->getRawInput()['id_revisi'];
		$status         = $data->getRawInput()['status'];

		try {
			$result = $sidangModel->markRevisi($id_revisi, $id_status, $status);

			return $this->my_response($result);
		} catch (Exception $e) {
			return $this->my_response("1066");
		}
	}

	public function input_nilai($edit = false)
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$id = $edit ? $data->getRawInput()['id_status'] : $data->getPost("id_status");
		$nilai = $edit ? $data->getRawInput()['nilai'] : $data->getPost("nilai");

		if (!is_numeric($nilai)) {
			return $this->respond($this->error_response(["error" => "The value you entered ($nilai) is not a number."], 400), 400);
		} else {
			if ($nilai > 0 && $nilai <= 100) {
				try {
					$result = $edit ? $sidangModel->editNilai($id, $nilai) : $sidangModel->inputNilai($id, $nilai);

					return $this->my_response($result);
				} catch (Exception $e) {
					return $this->my_response("1066");
				}
			} else {
				return $this->respond($this->error_response(["error" => "Please enter 0-100. Value you entered is: $nilai"], 400), 400);
			}
		}
	}

	public function auth($type = "")
	{
		$authModel = new \App\Models\AuthModel($this->db);

		$data = $this->request;

		$identity = $data->getPost("identity");
		$password = $data->getPost("password");

		$result = $authModel->auth($identity, $password, $type);

		return $this->respond($result, 200);
	}
}
