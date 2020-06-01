<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    public function findMahasiswa($nim = null)
	{
		$where = $nim != null ? ["nim" => $nim] : [];

		return $this->db->table("t_mahasiswa mhs")
			->select([
				"nim as user_identity",
				"nama_mhs as user_name",
				"jk",
				"tanggal_lahir",
				"tanggal_masuk",
				"nama_jur"
			])
			->join("t_jurusan", "t_jurusan.kode_jur = mhs.kode_jurusan")
			->where($where)
			->get();
	}

	public function findMahasiswaByKeyword($keyword) {
		return $this->db->table("t_mahasiswa mhs")
			->select([
				"nim",
				"nama_mhs",
				"jk",
				"tanggal_lahir",
				"tanggal_masuk",
				"nama_jur"
			])
			->join("t_jurusan", "t_jurusan.kode_jur = mhs.kode_jurusan")
			->where("nim", $keyword)
			->orLike("nama_mhs", $keyword)
			->get()->getResultObject();
	}

	public function populateMhs($wildCard) {
		$query =  $this->db->table("t_mahasiswa mhs")
			->select([
				"nim",
				"nama_mhs"
			])
			->join("t_jurusan", "t_jurusan.kode_jur = mhs.kode_jurusan")
			->like("nim", $wildCard)
			->get()->getResultObject();

		$result = "";

		foreach($query as $item) {
			$result .= "<option value='{$item->nim}'>({$item->nim}) {$item->nama_mhs}</option>";
		}

		return $result;
	}
}
