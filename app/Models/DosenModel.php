<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
	public function findDosen($id = null)
	{
		$where = $id != null ? ["id_dosen" => $id] : [];

		return $this->db->table("t_dosen")
			->select([
				"id_dosen as user_identity",
				"CONCAT(t_dosen.nama_dosen, '', IFNULL(t_dosen.gelar_depan, '')) as user_name",
				"nip",
				"IFNULL(nik, '') as nik",
				"nidn"
			])
			->where($where)
			->get();
	}

	public function populateDosen()
	{

		$query = $this->db->table("t_dosen")
			->select([
				"id_dosen",
				"CONCAT(t_dosen.nama_dosen, '', IFNULL(t_dosen.gelar_depan, '')) as nama_dosen",
			])
			->get()->getResultObject();

		$result = "";

		foreach ($query as $item) {
			$result .= "<option value='{$item->id_dosen}'>({$item->id_dosen}) {$item->nama_dosen}</option>";
		}

		return $result;
	}
}
