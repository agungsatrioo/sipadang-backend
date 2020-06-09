<?php

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\ResultInterface;
use CodeIgniter\Model;

class DosenModel extends Model
{
	private function findDosenBuilder()
	{
		return $this->db->table("t_dosen")
			->select([
				"id_dosen",
				"id_dosen as user_identity",
				"nama_dosen as nama_dosen_orig",
				"namaDosenWithGelar(id_dosen) as nama_dosen",
				"namaDosenWithGelar(id_dosen) as user_name",
				"nip",
				"IFNULL(nik, '') as nik",
				"nidn",
				"t_jurusan.kode_jur",
				"IFNULL(nama_jur, '') as nama_jur",
				"kode_fak",
				"nama_resmi as nama_fak",
				"jk"
				
			])
			->join("t_jurusan", "t_jurusan.kode_jur = t_dosen.kode_jur")
			->join("t_fakultas", "t_fakultas.kode_fak = t_jurusan.fak_kode");
	}

	public function findDosen($id = null)
	{
		$where = $id != null ? ["id_dosen" => $id] : [];

		return $this->findDosenBuilder()
			->where($where)
			->get();
	}

	public function findDosenByKeyword($keyword)
	{

		$explode1 	= explode(":", $keyword, 2);
		$explode2 	= explode("~", $keyword, 2);

		$explode	= [];
		$builder	= $this->findDosenBuilder();

		if (!empty($explode1) && count($explode1) == 2) {
			$explode = $explode1;
		} elseif (!empty($explode2) && count($explode2) == 2) {
			$explode = $explode2;
		} else {
			return $builder
				->where("id_dosen", $keyword)
				->orWhere("nip", $keyword)
				->orWhere("nik", $keyword)
				->orWhere("nidn", $keyword)
				->orWhere("nama_dosen", $keyword)
				->get();
		}

		switch ($explode[0]) {
			case "id_dosen":
			case "nip":
			case "nik":
			case "nidn":
				$builder = $builder->where($explode[0], $explode[1]);
				break;
			default:
				return false;
		}

		return $builder->get();
	}

	public function populateDosen($kode_fak = "", $kode_jur = ""): string
	{

		$query = $this->dosenOptionList($kode_fak, $kode_jur);

		$result = "";

		foreach ($query as $item) {
			$result .= "<option value='{$item->id_dosen}'>({$item->id_dosen}) {$item->nama_dosen}</option>";
		}

		return $result;
	}

	public function dosenOptionList($kode_fak = "", $kode_jur = ""): array
	{
		$builder = $this->findDosenBuilder();

		if(!empty($kode_fak)) {
			$builder = $builder->where("kode_fak", $kode_fak);
		} elseif(!empty($kode_fak)) {
			$builder = $builder->where("kode_jur", $kode_jur);
		}

		return $builder->get()->getResultObject();
	}

	public function ubahDosen($keyword, $data)
	{
		return $this->db->table("t_dosen")
			->where("nik", $keyword)
			->orWhere("id_dosen", $keyword)
			->update($data);
	}

	public function deleteDosen($keyword)
	{
		return $this->db->table("t_dosen")
			->where("id_dosen", $keyword)
			->delete();
	}

	public function addDosen($data)
	{
		return $this->db->table("t_dosen")
			->insert($data);
	}
}
