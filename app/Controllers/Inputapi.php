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
            case 92:
                return "Tidak dapat mengubah kata sandi.";
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
            case true:
				return $this->respond(["info" => "ok"], 200);
            case false:
                return $this->respond([
					"code" => 400,
					"error" => $this->explain_error(400)
				], 400);
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
        //DONE JANGAN DIOPREK
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

		return $this->respond($sidangModel->getUP($id_dosen, $nim), 200);
	}

	public function munaqosah()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$nim = $data->getGet('mahasiswa');
		$id_dosen = $data->getGet('dosen');

		return $this->respond($sidangModel->getMunaqosah($id_dosen, $nim), 200);
	}

	public function kompre()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$nim = $data->getGet('mahasiswa');
		$id_dosen = $data->getGet('dosen');

		return $this->respond($sidangModel->getKompre($id_dosen, $nim), 200);
	}

	public function cek_nilai()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$table = $data->getGet('table');
		$nim    = $data->getGet('mahasiswa');
		$dosen  = $data->getGet('id_dosen');

		$query = $sidangModel->newCekNilai($table, $nim, $dosen);

		if (empty($query)) $query = [["nilai" => "Belum ada", "mutu" => "Belum ada", "color" => "#000000"]];

		return $this->respond($query, 200);
	}

	public function get_revisi()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$table = $data->getGet('table');
		$nim    = $data->getGet('mahasiswa');
		$dosen  = $data->getGet('dosen');
		$id_revisi  = $data->getGet('id_revisi');

		$result = $sidangModel->newCekRevisi($table, $nim, $dosen, $id_revisi);

		return $this->respond($result, 200);
	}

	public function input_revisi($edit = false)
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$table      = $edit ? $data->getRawInput()['table'] : $data->getPost("table");
		$nim        = $edit ? $data->getRawInput()['mahasiswa'] : $data->getPost("mahasiswa");
		$id_dosen   = $edit ? $data->getRawInput()['dosen'] : $data->getPost("dosen");

		$id_revisi  = $edit ? $data->getRawInput()['id_revisi'] : null;

		$details    = $edit ? $data->getRawInput()['detail_revisi'] : $data->getPost("detail_revisi");
		$deadline   = $edit ? $data->getRawInput()['tgl_revisi_deadline'] : $data->getPost("tgl_revisi_deadline");

		try {
			$result = !$edit ? $sidangModel->newAddRevisi($table, $nim, $id_dosen, $details, $deadline) : $sidangModel->newEditRevisi($table, $nim, $id_revisi, $id_dosen, $details, $deadline);

			return $this->my_response($result);
		} catch (Exception $e) {
			return $this->my_response("1069");
		}
	}

	public function delete_revisi()
	{
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$table      = $data->getRawInput()['table'];
		$nim        = $data->getRawInput()['mahasiswa'];
		$dosen   = $data->getRawInput()['dosen'];
		$id_revisi  = $data->getRawInput()['id_revisi'];

		if (isset($id_revisi)) {
			try {
				$result = $sidangModel->newDeleteRevisi($table, $nim, $id_revisi, $dosen);

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

		$table      = $data->getRawInput()['table'];
		$nim        = $data->getRawInput()['mahasiswa'];
		$id_dosen   = $data->getRawInput()['dosen'];
		$id_revisi  = $data->getRawInput()['id_revisi'];
		$status  = $data->getRawInput()['status_revisi'];

		if (isset($id_revisi)) {
			try {
				$result = $sidangModel->newSetRevisiStatus($table, $nim, $id_revisi, $id_dosen, $status);

				return $this->my_response($result);
			} catch (Exception $e) {
				return $this->my_response("1066");
			}
		} else {
			return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);
		}
	}

	public function input_nilai($edit = false)
	{
        //DONE!
		if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

		$sidangModel = new \App\Models\SidangModel($this->db);

		$data = $this->request;

		$table = $edit ? $data->getRawInput()['table'] : $data->getPost("table");
		$nim = $edit ? $data->getRawInput()['nim'] : $data->getPost("nim");
		$dosen = $edit ? $data->getRawInput()['id_dosen'] : $data->getPost("id_dosen");
		$nilai = $edit ? $data->getRawInput()['nilai'] : $data->getPost("nilai");

		if (!is_numeric($nilai)) {
			return $this->respond($this->error_response(["error" => "The value you entered ($nilai) is not a number."], 400), 400);
		} else {
			if ($nilai > 0 && $nilai <= 100) {
				try {
					$result = $sidangModel->newEditNilai($table, $nim, $dosen, intval($nilai));

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

    public function is_first_run($id) {
        $authModel = new \App\Models\AuthModel($this->db);

        $user = $authModel->getUser($id);

        $result = !empty($user) && $user->password_changed == 0 ? true : false;

        return $this->respond(["result" => $result], 200);
    }

    public function ganti_sandi()
    {
        if (!$this->verifyApp()) return $this->respond($this->error_response(["error" => "You are denied access to this operation."], 401), 401);

        $data = $this->request;

		$identity = $data->getPost("identity");
		$password = $data->getPost("password");

        $fields = ["password" => password_hash($password, PASSWORD_DEFAULT),  "password_changed" => "1"];

        try {
            $result = $this->db->table("t_pengguna")
                ->where(["identity" => $identity, "password_changed" => "0"])
                ->update($fields);

            return $this->my_response($result);
        } catch (Exception $e) {
            return $this->my_response("92");
        }
    }
}
