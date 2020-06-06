<?php

namespace App\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Model;

class JurusanModel extends Model
{
    public function getJurusan($kode_jur = "", $kode_fak = ""): BaseResult
    {
        $builder = $this->db->table("t_jurusan")
        ->join("t_fakultas", "t_fakultas.kode_fak = t_jurusan.fak_kode");

        if(!empty($kode_jur)) $builder = $builder->where("kode_jur", $kode_jur);
        if(!empty($kode_fak)) $builder = $builder->where("fak_kode", $kode_fak);

        return $builder->get();
    }
}
