<?php

namespace App\Models;

use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;

class MahasiswaModel extends Model
{

	private function findMhsBuilder() {
		return $this->db->table("t_mahasiswa mhs")
		->select([
			"mhs_id",
			"nim as user_identity",
			"nama as user_name",
			"nama as nama_mhs",
			"nim",
			"nama",
			"jk",
			"jur_kode",
			"jur_kode as kode_jur",
			"nama_jur"
		])
		->join("t_jurusan", "t_jurusan.kode_jur = mhs.jur_kode");
	}
    public function findMahasiswa($keyword = ""): ResultInterface
	{
		$builder = $this->findMhsBuilder();

		if(!empty($keyword)) 
		$builder = $builder->where("nim", $keyword)
		->orWhere("mhs_id", $keyword);

		return $builder->get();
	}

	public function findMahasiswaByKeyword($keyword) {
		return $this->findMhsBuilder()
		->like("nama", $keyword)
		->get()->getResultObject();
	}

	public function populateMhs($wildCard) {
		$query =  $this->db->table("t_mahasiswa mhs")
			->select([
				"nim",
				"nama"
			])
			->join("t_jurusan", "t_jurusan.kode_jur = mhs.jur_kode")
			->like("nim", $wildCard)
			->get()->getResultObject();

		$result = "";

		foreach($query as $item) {
			$result .= "<option value='{$item->nim}'>({$item->nim}) {$item->nama_mhs}</option>";
		}

		return $result;
	}

	public function nimToId($nim) {
		$result = $this->db->table("t_mahasiswa mhs")
		->where("nim", "$nim")
		->get()->getFirstRow();

		return $result->id_mhs;
	}

	public function ubahMhs($keyword, $data) {
		return $this->db->table("t_mahasiswa")
		->where("nim", $keyword)
		->orWhere("mhs_id", $keyword)
		->update($data);
	}	
	
	public function deleteMhs($keyword) {
		return $this->db->table("t_mahasiswa")
		->where("nim", $keyword)
		->orWhere("mhs_id", $keyword)
		->delete();
	}
	
	public function addMhs($data) {
		return $this->db->table("t_mahasiswa")
		->insert($data);
	}
}
