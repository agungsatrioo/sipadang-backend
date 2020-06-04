<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;
use Exception;
use stdClass;

class SidangModel extends Model
{
    private function isDateLatest($date1, $date2): bool
    {
        $dateTimestamp1 = strtotime($date1);
        $dateTimestamp2 = strtotime($date2);

        if ($dateTimestamp1 > $dateTimestamp2) return true;
        else return false;
    }

    public function _mutu($n)
    {
        if ($n > 78 && $n <= 100) {
            return "A";
        } elseif ($n > 67 && $n >= 78) {
            return "B";
        } elseif ($n > 56 && $n >= 67) {
            return "C";
        } elseif ($n > 41 && $n >= 56) {
            return "D";
        } elseif ($n > 0 && $n >= 41) {
            return "E";
        } else {
            return "-";
        }
    }

    public function warna($n)
    {
        if ($n > 78 && $n <= 100) {
            return "#4CAF50";
        } elseif ($n > 67 && $n >= 78) {
            return "#8BC34A";
        } elseif ($n > 56 && $n >= 67) {
            return "#FF9800";
        } elseif ($n > 41 && $n >= 56) {
            return "#FF5722";
        } elseif ($n > 0 && $n >= 41) {
            return "#F44336";
        } else {
            return "#000000";
        }
    }

    public function tglIndonesia($tanggal, $withHari = false)
    {
        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $hari = [
            "Minggu",
            "Senin",
            "Selasa",
            "Rabu",
            "Kamis",
            "Jumat",
            "Sabtu"
        ];

        $dateObj = new DateTime($tanggal);

        $hariNumber = $dateObj->format("w");
        $bulanNumber = $dateObj->format("m");

        $tglNumber = $dateObj->format("d");
        $thnNumber = $dateObj->format("Y");

        $hariString = "";

        if ($withHari) {
            $hariString = $hari[$hariNumber] . ",";
        }

        return "$hariString $tglNumber {$bulan[((int)$bulanNumber) - 1]} $thnNumber";
    }

    public function getUP($id_dosen, $nim, $date = "")
    {
        return $this->newUP($nim, $id_dosen, $date);
    }

    public function getMunaqosah($id_dosen, $nim,  $date = "")
    {
        return $this->newMunaqosah($nim, $id_dosen);
    }

    public function getKompre($id_dosen, $nim,  $date = "")
    {
        return $this->newKompre($nim, $id_dosen, $date);
    }

    public function getStatusDosenDiSidang($nim, $jenis_status)
    {
        $query =  $this->db->table("t_status")
            ->select("t_status.id_status, t_status.id_dosen, CONCAT(t_dosen.nama_dosen, '', IFNULL(t_dosen.gelar_depan, '')) as nama_dosen, nama_status, IFNULL(nilai, 'Belum ada') as nilai")
            ->where("t_status.nim", $nim)
            ->like("nama_status", $jenis_status)
            ->join("t_jenis_status", "t_jenis_status.id_jenis_status = t_status.id_jenis_status")
            ->join("t_dosen", "t_dosen.id_dosen = t_status.id_dosen")
            ->join("t_nilai", "t_nilai.id_status = t_status.id_status", "left")
            ->get()->getResultObject();

        foreach ($query as $item) {
            $item->color = $this->warna($item->nilai);
            $item->mutu = $this->_mutu($item->nilai);
            $item->revisi = $this->getRevisi(["t_status.id_status" => $item->id_status]);
        }

        return $query;
    }

    public function cekNilai($id)
    {
        $query  = $this->db->table("t_status")
            ->select("t_status.id_status, t_status.id_dosen, CONCAT(t_dosen.nama_dosen, '', IFNULL(t_dosen.gelar_depan, '')) as nama_dosen, nama_status, IFNULL(nilai, 'Belum ada') as nilai")
            ->where("t_status.id_status", $id)
            ->join("t_jenis_status", "t_jenis_status.id_jenis_status = t_status.id_jenis_status")
            ->join("t_dosen", "t_dosen.id_dosen = t_status.id_dosen")
            ->join("t_nilai", "t_nilai.id_status = t_status.id_status", "left")
            ->get()->getResultObject();

        foreach ($query as $item) {
            $item->color = $this->warna($item->nilai);
            $item->mutu = $this->_mutu($item->nilai);

            $item->revisi = $this->getRevisi(["t_status.id_status" => $item->id_status]);
        }

        return $query;
    }

    public function cekIDStatus($id)
    {
        $query = $this->db->table("t_status")
            ->select("id_status")
            ->where("id_status", $id)
            ->get()->getResultObject();

        if (count($query) > 0) return true;
        else return false;
    }


    // FOR DASHBOARD

    public function getKelompokSidang()
    {
        $query = $this->db->table("t_sidang")
            ->join("t_tanggal_sidang", "t_sidang.id_tanggal_sidang = t_tanggal_sidang.id_tanggal_sidang")
            ->join("t_kelompok_sidang", "t_sidang.id_kelompok_sidang = t_kelompok_sidang.id_kelompok_sidang")
            ->join("t_ruangan", "t_sidang.id_ruangan = t_ruangan.id_ruang")
            ->orderBy("id_sidang")
            ->get()->getResultObject();

        $result = "";

        foreach ($query as $item) {
            $tgl = $this->tglIndonesia($item->tanggal_sidang, true);
            $data =
                $result .= "<option value='{$item->id_sidang}'>{$item->nama_kelompok_sidang} ({$tgl}, {$item->kode_ruang}/{$item->nama_ruang})</option>";
        }

        return $result;
    }

    public function getSidangDetailsDashboard($table, $fields, $keyword)
    {
        $query = $this->db->table("t_u_$table")
            ->select($fields)
            ->join("t_status_sidang s_sidang", "s_sidang.id_status = t_u_$table.id_status_sidang")
            ->join("t_status status", "s_sidang.id_status = status.id_status")
            ->join("t_sidang sidang", "s_sidang.id_sidang = sidang.id_sidang")
            ->join("t_mahasiswa mhs", "status.nim = mhs.nim")
            ->join("t_dosen dsn", "status.id_dosen = dsn.id_dosen")
            ->join("t_kelompok_sidang kelompok", "sidang.id_kelompok_sidang = kelompok.id_kelompok_sidang")
            ->join("t_ruangan ruangan", "sidang.id_ruangan = ruangan.id_ruang")
            ->join("t_tanggal_sidang jadwal", "sidang.id_tanggal_sidang = jadwal.id_tanggal_sidang")
            ->join("t_nilai nilai", "s_sidang.id_status = nilai.id_status", "left")
            ->where("mhs.nim", $keyword)
            ->orLike("mhs.nama_mhs", $keyword)
            ->get()->getResultObject();

        if (empty($query)) return [];

        foreach ($query as $it) {
            if (isset($id_dosen)) {
                $it->keterangan_sidang = $it->nilai != null ?  "Sidang sudah dinilai" : "Belum sidang";
            } elseif (isset($nim)) {
                unset($it->id_status);
            }

            $str = strftime("%d %B %Y", strtotime($it->sidang_date_fmtd));
            $it->sidang_date_fmtd = $str;
        }

        return $query;
    }

    public function getSidangRecord($keyword)
    {
        $up = $this->getSidangDetailsDashboard("proposal", ["mhs.nim", "mhs.nama_mhs", "tanggal_sidang as sidang_date_fmtd"], $keyword);
        $kompre = $this->getSidangDetailsDashboard("kompre", ["mhs.nim", "mhs.nama_mhs", "tanggal_sidang as sidang_date_fmtd"], $keyword);
        $munaqosah = $this->getSidangDetailsDashboard("munaqosah", ["mhs.nim", "mhs.nama_mhs", "tanggal_sidang as sidang_date_fmtd"], $keyword);

        if (!empty($up)) return $up;
        elseif (!empty($kompre)) return $kompre;
        elseif (!empty($munaqosah)) return $munaqosah;
    }

    public function getRekapUP($date)
    {
        $query = $this->getUP("", "", $date);

        foreach ($query as $key => $item) {
            $ada_nilai = true;

            $penguji        = $this->getStatusDosenDiSidang($item->nim, "Penguji Sidang Proposal %");
            $item->penguji  = $penguji;

            foreach ($item->penguji as $k1 => $v1) {
                if (!is_numeric($v1->nilai)) {
                    $item->nilai = ["nilai" => $v1->nilai, "mutu" => $v1->mutu, "color" => $v1->color];
                    $ada_nilai   = false;
                    $item->jumlah = 0;
                    break;
                }
            }

            if ($ada_nilai) {
                $nilai = (.5 * $item->penguji[0]->nilai) + (.5 * $item->penguji[1]->nilai);
                $item->nilai = ["nilai" => floor($nilai), "mutu" => $this->_mutu($nilai), "color" => $this->warna($nilai)];
                $item->jumlah =  $item->penguji[0]->nilai +  $item->penguji[1]->nilai;
            }
        }

        return $query;
    }

    public function getRekapKompre($date)
    {
        $query = $this->getKompre("", "", $date);
        $presentase_kompre = .333333333; //must be precise!

        foreach ($query as $key => $item) {
            $ada_nilai = true;

            $penguji        = $this->getStatusDosenDiSidang($item->nim, "Penguji Sidang Komprehensif %");
            $item->penguji  = $penguji;

            foreach ($item->penguji as $k1 => $v1) {
                if (!is_numeric($v1->nilai)) {
                    $item->nilai = ["nilai" => $v1->nilai, "mutu" => $v1->mutu, "color" => $v1->color];
                    $ada_nilai   = false;
                    $item->jumlah = 0;
                    break;
                }
            }

            if ($ada_nilai) {
                $nilai = ($presentase_kompre * $item->penguji[0]->nilai) + ($presentase_kompre * $item->penguji[1]->nilai) + ($presentase_kompre * $item->penguji[2]->nilai);

                if ($nilai > 100) $nilai = 100;

                $item->nilai = ["nilai" => floor($nilai), "mutu" => $this->_mutu($nilai), "color" => $this->warna($nilai)];

                $item->jumlah = $item->penguji[0]->nilai + $item->penguji[1]->nilai + $item->penguji[2]->nilai;
            }
        }

        return $query;
    }

    public function getRekapMunaqosah($date)
    {
        $query = $this->getMunaqosah("", "", $date);

        foreach ($query as $key => $item) {
            $ada_nilai = true;

            $penguji        = $this->getStatusDosenDiSidang($item->nim, "Penguji Sidang Munaqosah %");
            $pembimbing        = $this->getStatusDosenDiSidang($item->nim, "Pembimbing Munaqosah %");
            $dosenku        = $this->getStatusDosenDiSidang($item->nim, "Munaqosah %");

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

                $item->nilai = ["nilai" => floor($nilai), "mutu" => $this->_mutu($nilai), "color" => $this->warna($nilai)];
            }
        }

        return $query;
    }

    public function changeDosen($id_status, $id_dosen)
    {
        $fields = [
            "id_dosen" => $id_dosen
        ];
    }

    public function addStatusUP($nim, $penguji)
    {
        $fields = [];
        $pengujiID = [6, 7];
        $insertID = [];

        foreach ($penguji as $id => $eachPenguji) {
            $fields[] = [
                "id_jenis_status" => $pengujiID[$id],
                "nim"             => $nim,
                "id_dosen"        => $eachPenguji
            ];
        }


        foreach ($fields as $key => $value) {
            $query = $this->db->table('t_status')
                ->insert($value);

            $insertID[] = $this->db->insertID();
        }

        return $insertID;
    }

    public function addStatusKompre($nim, $penguji)
    {
        $fields = [];
        $pengujiID = [8, 9, 10];
        $insertID = [];

        foreach ($penguji as $id => $eachPenguji) {
            $fields[] = [
                "id_jenis_status" => $pengujiID[$id],
                "nim"             => $nim,
                "id_dosen"        => $eachPenguji
            ];
        }


        foreach ($fields as $key => $value) {
            $query = $this->db->table('t_status')
                ->insert($value);

            $insertID[] = $this->db->insertID();
        }

        return $insertID;
    }

    public function addStatusMunaqosah($nim, $penguji, $pembimbing)
    {
        $fields = [];
        $pengujiID = [3, 4,];
        $pembimbingID = [11, 12];
        $insertID = [];

        foreach ($penguji as $id => $eachPenguji) {
            $fields[] = [
                "id_jenis_status" => $pengujiID[$id],
                "nim"             => $nim,
                "id_dosen"        => $eachPenguji
            ];
        }

        foreach ($pembimbing as $id => $eachPembimbing) {
            $fields[] = [
                "id_jenis_status" => $pembimbingID[$id],
                "nim"             => $nim,
                "id_dosen"        => $eachPembimbing
            ];
        }


        foreach ($fields as $key => $value) {
            $query = $this->db->table('t_status')
                ->insert($value);

            $insertID[] = $this->db->insertID();
        }

        return $insertID;
    }

    public function addStatusSidang($id_status, $id_sidang)
    {
        $fields = [];
        $insertID = [];

        foreach ($id_status as $eachIDStatus) {
            $fields[] = [
                "id_status" => $eachIDStatus,
                "id_sidang" => $id_sidang
            ];
        }

        foreach ($fields as $key => $value) {
            $query = $this->db->table('t_status_sidang')
                ->insert($value);

            $insertID[] = $this->db->insertID();
        }

        return $insertID;
    }

    public function addDetailSidangProposal($id_status_sidang, $judul)
    {
        $fields = [];

        foreach ($id_status_sidang as $eachIDStatus) {
            $fields[] = [
                "id_status_sidang" => $eachIDStatus,
                "judul_proposal" => $judul
            ];
        }

        return $this->db->table("t_u_proposal")
            ->insertBatch($fields);
    }

    public function addDetailSidangKompre($id_status_sidang)
    {
        $fields = [];

        foreach ($id_status_sidang as $eachIDStatus) {
            $fields[] = [
                "id_status_sidang" => $eachIDStatus,
            ];
        }

        return $this->db->table("t_u_kompre")
            ->insertBatch($fields);
    }

    public function addDetailSidangMunaqosah($id_status_sidang, $judul)
    {
        $fields = [];

        foreach ($id_status_sidang as $eachIDStatus) {
            $fields[] = [
                "id_status_sidang" => $eachIDStatus,
                "judul_munaqosah" => $judul
            ];
        }

        return $this->db->table("t_u_munaqosah")
            ->insertBatch($fields);
    }

    public function getTanggalSidang($tgl = "", $id = "", $fromToday = false)
    {
        $result = $this->db->table("t_tanggal_sidang")
            ->orderBy("tanggal_sidang", "asc");

        if (!empty($tgl)) $result = $result->where("tanggal_sidang", $tgl);

        if (!empty($id)) $result = $result->where("id_tanggal_sidang", $id);

        if ($fromToday) $result = $result->where("tanggal_sidang >", strftime("%Y-%m-%d"));

        $result = $result->get()->getResultObject();

        if (!empty($result))
            foreach ($result as $item) {
                $item->tgl_sidang = $this->tglIndonesia($item->tanggal_sidang, true);
            }

        return $result;
    }

    public function addTanggalSidang($tgl)
    {
        $fields = ["tanggal_sidang" => $tgl];

        try {
            return $this->db->table("t_tanggal_sidang")
                ->insert($fields);
        } catch (Exception $e) {
            return false;
        }
    }

    public function editTanggalSidang($id, $tgl)
    {

        $fields = ["tanggal_sidang" => $tgl];

        try {
            return $this->db->table("t_tanggal_sidang")
                ->where("id_tanggal_sidang", $id)
                ->update($fields);
        } catch (Exception $e) {
            return false;
        }
    }

    public function showJadwalSidang($id_sidang = "", $idTanggal = "", $idKelompok = "", $idRuangan = "")
    {
        $result = $this->db->table("t_sidang")
            ->select("*")
            ->join("t_tanggal_sidang", "t_sidang.id_tanggal_sidang = t_tanggal_sidang.id_tanggal_sidang")
            ->join("t_kelompok_sidang", "t_sidang.id_kelompok_sidang = t_kelompok_sidang.id_kelompok_sidang")
            ->join("t_ruangan", "t_sidang.id_ruangan = t_ruangan.id_ruang")
            ->orderBy("id_sidang");

        if (!empty($id_sidang)) $result = $result->where("id_sidang", $id_sidang);

        if (!empty($idTanggal)) $result = $result->where("t_sidang.id_tanggal_sidang", $idTanggal);
        if (!empty($idKelompok)) $result = $result->where("t_sidang.id_kelompok_sidang", $idTanggal);
        if (!empty($idRuangan)) $result = $result->where("id_ruangan", $idRuangan);

        $result = $result->get()->getResultObject();

        if (!empty($result))
            foreach ($result as $item) {
                $item->tgl_sidang_fmtd = $this->tglIndonesia($item->tanggal_sidang, true);
                $item->can_edit_tanggal = $this->isDateLatest($item->tanggal_sidang, strftime("%Y-%m-%d"));
            }

        return $result;
    }

    public function addJadwalSidang($idTanggal, $idKelompok, $idRuangan)
    {

        $dataJadwal = $this->showJadwalSidang("", $idTanggal, $idKelompok, $idRuangan);

        $fields = [
            "id_tanggal_sidang" => $idTanggal,
            "id_kelompok_sidang" => $idKelompok,
            "id_ruangan" => $idRuangan
        ];

        try {
            if (!empty($dataJadwal)) return false;

            return $this->db->table("t_sidang")
                ->insert($fields);
        } catch (Exception $e) {
            return false;
        }
    }

    public function editJadwalSidang($idJadwal, $idTanggal, $idKelompok, $idRuangan)
    {

        $dataJadwal = $this->showJadwalSidang("", $idTanggal, $idKelompok, $idRuangan);

        $fields = [
            "id_tanggal_sidang" => $idTanggal,
            "id_kelompok_sidang" => $idKelompok,
            "id_ruangan" => $idRuangan
        ];

        try {
            if (!empty($dataJadwal)) return false;

            return $this->db->table("t_sidang")
                ->where("id_sidang", $idJadwal)
                ->update($fields);
        } catch (Exception $e) {
            return false;
        }
    }

    public function showKelompokSidang($idKelompok = "")
    {
        $query = $this->db->table("t_kelompok_sidang");

        return $query->get()->getResultObject();
    }

    public function showRuanganSidang($idRuangan = "")
    {
        $query = $this->db->table("t_ruangan");

        if (!empty($idRuangan)) $query = $query->where("id_ruang", $idRuangan);

        $query = $query->get()->getResultObject();

        return $query;
    }

    public function addRuanganSidang($kdRuang, $namaRuang)
    {
        $fields = [
            "kode_ruang" => $kdRuang,
            "nama_ruang" => $namaRuang,
        ];

        try {
            return $this->db->table("t_ruangan")
                ->insert($fields);
        } catch (Exception $e) {
            return false;
        }
    }

    public function editRuanganSidang($id, $kdRuang, $namaRuang)
    {

        $fields = [
            "kode_ruang" => $kdRuang,
            "nama_ruang" => $namaRuang,
        ];

        try {
            return $this->db->table("t_ruangan")
                ->where("id_ruang", $id)
                ->update($fields);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteJadwalSidang($id)
    {
        try {
            return $this->db->table("t_sidang")
                ->where("id_sidang", $id)
                ->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteTanggalSidang($id)
    {
        try {
            return $this->db->table("t_tanggal_sidang")
                ->where("id_tanggal_sidang", $id)
                ->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteRuangSidang($id)
    {
        try {
            return $this->db->table("t_ruangan")
                ->where("id_ruang", $id)
                ->delete();
        } catch (Exception $e) {
            return false;
        }
    }

    //----

    private function getStatusDosen($idStatus)
    {
        return $this->db->table("t_jenis_status")->where("id_jenis_status", $idStatus)
            ->get()->getFirstRow();
    }

    private function updateWrapper($table, $nim, $type, $details)
    {
        $fields = [$type => json_encode($details->$type)];
        return $this->db->table($table)->where("nim", $nim)->update($fields);
    }

    private function olahDataDosen($mahasiswa, $result, $dosenModel, $adaNilaiCallback, $nim = "", $idDosen = "")
    {
        foreach ($result as $key => $item) {
            $dosen = $dosenModel->findDosen($item->id_dosen)->getFirstRow();
            $status = $this->getStatusDosen($item->jenis_status);

            $item->id_status    = $key;
            $item->nama_dosen   = $dosen->user_name;
            $item->nama_status   = $status->nama_status;

            if ($item->nilai == null) $item->nilai = "Belum ada";

            $item->mutu         = $this->_mutu($item->nilai);
            $item->color        = $this->warna($item->nilai);

            $revisi             = [];

            if (!property_exists($item, "revisi")) {
                $item->revisi = [];
            } else {
                foreach ($item->revisi as $keyRevisi => $valueRevisi) {
                    $valueRevisi->id_revisi = $keyRevisi;
                    $valueRevisi->nim = $mahasiswa->nim;
                    $valueRevisi->id_dosen = $item->id_dosen;
                    $valueRevisi->nama_dosen   = $dosen->user_name;
                    $valueRevisi->nama_status   = $status->nama_status;

                    if($valueRevisi->id_dosen == $idDosen) {
                        $revisi[] = $valueRevisi;
                    }
                }
            }

            if($item->id_dosen == $idDosen) {
                $mahasiswa->nilai = [
                    "nilai"     => $item->nilai,
                    "mutu"      => $this->_mutu($item->nilai),
                    "color"     => $this->warna($item->nilai),
                    "revisi"    => $revisi
                ];
            }


            if (!is_numeric($item->nilai)) {
                $adaNilaiCallback(false);
            }
        }

        return $result;
    }

    private function olahResult($result, $dosenModel, $nilaiTotal, $nim = "", $idDosen = "")
    {
        if (empty($result)) return [];

        foreach ($result as $eachMahasiswa) {
            $ada_nilai = true;

            $eachMahasiswa->sidang_date_fmtd = $this->tglIndonesia($eachMahasiswa->sidang_date, true);

            $eachMahasiswa->penguji = $this->olahDataDosen(
                $eachMahasiswa,
                json_decode($eachMahasiswa->penguji),
                $dosenModel,
                function ($value) use (&$ada_nilai) {
                    if ($ada_nilai) $ada_nilai = $value;
                },$nim, $idDosen
            );

            if (property_exists($eachMahasiswa, "pembimbing")) {
                $eachMahasiswa->pembimbing = $this->olahDataDosen(
                    $eachMahasiswa,
                    json_decode($eachMahasiswa->pembimbing),
                    $dosenModel,
                    function ($value) use (&$ada_nilai) {
                        if ($ada_nilai) $ada_nilai = $value;
                    }, $nim, $idDosen
                );
            }

            if(empty($idDosen)) {
                $nilaiFinal = $nilaiTotal($ada_nilai, $eachMahasiswa->penguji, property_exists($eachMahasiswa, "pembimbing") ? property_exists($eachMahasiswa, "pembimbing") : []);

                $eachMahasiswa->nilai = [
                    "nilai"     => $nilaiFinal,
                    "mutu"      => $this->_mutu($nilaiFinal),
                    "color"     => $this->warna($nilaiFinal)
                ];
            }
        }

        return array_values($result);
    }

    public function newUP($nim = "", $idDosen = "", $date = "")
    {
        $dosenModel = new DosenModel($this->db);

        $result = $this->newSidangDetails("t_sidang_proposal", ["mhs.nim", "nama_mhs", "judul_proposal", "tanggal_sidang as sidang_date", "ruangan.kode_ruang", "nama_kelompok_sidang", "kelompok.id_kelompok_sidang", "nama_jur", "penguji"], $nim, $idDosen, $date);

        $result = $this->olahResult($result, $dosenModel, function ($ada_nilai, $penguji, $pembimbing) {
            return $ada_nilai ? ((.5 * $penguji[0]->nilai) + (.5 * $penguji[1]->nilai)) : "Belum ada";
        }, $nim, $idDosen);

        if(!empty($result)) {
            return !empty($nim) ? $result[0] : $result;
        } else return [];
    }

    public function newKompre($nim = "", $idDosen = "", $date = "")
    {
        $dosenModel = new DosenModel($this->db);
        $presentase_kompre = .333333333; //must be precise!

        $result = $this->newSidangDetails("t_sidang_kompre", ["mhs.nim", "nama_mhs", "tanggal_sidang as sidang_date", "ruangan.kode_ruang", "nama_kelompok_sidang", "kelompok.id_kelompok_sidang", "nama_jur", "penguji"], $nim, $idDosen, $date);

        $result = $this->olahResult($result, $dosenModel, function ($ada_nilai, $penguji, $pembimbing) use (&$presentase_kompre) {
            if ($ada_nilai) {
                $nilai = ($presentase_kompre * $penguji[0]->nilai) + ($presentase_kompre * $penguji[1]->nilai) + ($presentase_kompre * $penguji[2]->nilai);

                if ($nilai > 100) $nilai = 100;
                return $nilai;
            } else {
                return "Belum ada";
            }
        }, $nim, $idDosen);


       if(!empty($result)) {
            return !empty($nim) ? $result[0] : $result;
        } else return [];
    }

    public function newMunaqosah($nim = "", $idDosen = "", $date = "")
    {
        $dosenModel = new DosenModel($this->db);

        $result = $this->newSidangDetails("t_sidang_munaqosah", ["mhs.nim", "nama_mhs", "judul_munaqosah", "tanggal_sidang as sidang_date", "ruangan.kode_ruang", "nama_kelompok_sidang", "kelompok.id_kelompok_sidang", "nama_jur", "penguji", "pembimbing"], $nim, $idDosen, $date);

        $result = $this->olahResult($result, $dosenModel, function ($ada_nilai, $penguji, $pembimbing) {
            return $ada_nilai ? (.3 * $item->penguji[0]->nilai) + (.3 * $item->penguji[1]->nilai) +  (.2 * $item->pembimbing[0]->nilai) + (.2 * $item->pembimbing[1]->nilai) : "Belum ada";
        }, $nim, $idDosen);

        if(!empty($result)) {
            return !empty($nim) ? $result[0] : $result;
        } else return [];
    }

    public function newSidangDetails($table, array $fields, $nim = "", $idDosen = "", $date = "")
    {
        $conditions = [];
        $orCond     = "";

        if (!empty($date)) {
            $conditions = ["tanggal_sidang" => $date];
        } elseif (!empty($nim)) {
            $conditions = ["mhs.nim" => $nim];
        } elseif (!empty($idDosen)) {
            $conditions = "JSON_SEARCH(JSON_EXTRACT(penguji, \"$[*].id_dosen\"), 'one', \"$idDosen\") is not null";
            if ($table == "t_sidang_munaqosah")
                $orCond = "JSON_SEARCH(JSON_EXTRACT(pembimbing, \"$[*].id_dosen\"), 'one', \"$idDosen\") is not null";
        }

        $result = $this->db->table("$table")
            ->select($fields)
            ->where($conditions, NULL, TRUE);

        if (!empty($orCond)) $result = $result->orWhere($orCond, NULL, TRUE);

        $result = $result
            ->join("t_mahasiswa mhs", "$table.nim = mhs.nim")
            ->join("t_jurusan jur", "mhs.kode_jurusan = jur.kode_jur")
            ->join("t_sidang sidang", "$table.id_sidang = sidang.id_sidang")
            ->join("t_tanggal_sidang tanggal", "sidang.id_tanggal_sidang = tanggal.id_tanggal_sidang")
            ->join("t_ruangan ruangan", "sidang.id_ruangan = ruangan.id_ruang")
            ->join("t_kelompok_sidang kelompok", "sidang.id_kelompok_sidang = kelompok.id_kelompok_sidang")
            ->orderBy("mhs.nim")
            ->get()->getResultObject();

        return $result;
    }

    public function newCekRevisi($table, $nim, $idDosen, $idRevisi = "")
    {
        //DONE!
        $dataSidang = [];
        $ketemu = false;

        switch ($table) {
            case "t_sidang_proposal":
                $dataSidang = $this->newUP($nim, $idDosen);
                break;
            case "t_sidang_kompre":
                $dataSidang = $this->newKompre($nim, $idDosen);
                break;
            case "t_sidang_munaqosah":
                $dataSidang = $this->newMunaqosah($nim, $idDosen);
                break;
            default:
                return [];
        }

        if(empty($dataSidang)) return [];
        else $details = $dataSidang;

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                if(!empty($idRevisi) || $idRevisi == 0) return $dosen->revisi[$idRevisi];
                else return $dosen->revisi;
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen || $idRevisi == 0) {
                    if(!empty($idRevisi)) return $dosen->revisi[$idRevisi];
                    else return $dosen->revisi;
                }
            }
        }
    }

    public function newAddRevisi($table, $nim, $idDosen, $detailRevisi, $deadineRevisi)
    {
        //DONE!
        date_default_timezone_set('Asia/Jakarta');

        $ketemu         = false;
        $fields         = [];
        $type           = "";
        $select         = ["penguji", "mhs.nim"];

        if ($table == "t_sidang_munaqosah") $select[] = "pembimbing";

        $details = $this->newSidangDetails($table, $select, $nim, $idDosen);

        if (!empty($details)) $details = $details[0];
        else return false;

        $details->penguji = json_decode($details->penguji);
        if (property_exists($details, "pembimbing")) $details->pembimbing = json_decode($details->pembimbing);

        $buatRevisi = function ($detailRevisi, $deadineRevisi)  {
            $revisiItem = new stdClass();

            $revisiItem->detail_revisi = $detailRevisi;
            $revisiItem->tgl_revisi_input = date("Y-m-d H:i:s");
            $revisiItem->tgl_revisi_deadline = $deadineRevisi;
            $revisiItem->status_revisi = false;

            return $revisiItem;
        };

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                $dosen->revisi[] = $buatRevisi($detailRevisi, $deadineRevisi);
                $ketemu = true;
                $type = "penguji";
                break;
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen) {
                    $dosen->revisi[] = $buatRevisi($detailRevisi, $deadineRevisi);
                    $ketemu = true;
                    $type = "pembimbing";
                    break;
                }
            }
        }

        if (!empty($type)) {
            return $this->updateWrapper($table, $nim, $type, $details);
        } else return false;
    }

    public function newDeleteRevisi($table, $nim, $idRevisi, $idDosen)
    {
        //DONE!
        date_default_timezone_set('Asia/Jakarta');

        $ketemu         = false;
        $fields         = [];
        $type           = "";
        $select         = ["penguji", "mhs.nim"];

        if ($table == "t_sidang_munaqosah") $select[] = "pembimbing";

        $details = $this->newSidangDetails($table, $select, $nim, $idDosen);

        if (!empty($details)) $details = $details[0];
        else return false;

        $details->penguji = json_decode($details->penguji);
        if (property_exists($details, "pembimbing")) $details->pembimbing = json_decode($details->pembimbing);

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                unset($dosen->revisi[$idRevisi]);
                $dosen->revisi = array_values($dosen->revisi);

                $ketemu = true;
                $type = "penguji";
                break;
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen) {
                    unset($dosen->revisi[$idRevisi]);
                    $dosen->revisi = array_values($dosen->revisi);

                    $ketemu = true;
                    $type = "pembimbing";
                    break;
                }
            }
        }

        if (!empty($type)) {
            return $this->updateWrapper($table, $nim, $type, $details);
        } else return false;
    }

    public function newEditRevisi($table, $nim, $idRevisi, $idDosen, $detailRevisi, $deadineRevisi, $booleanStatus = false)
    {
        //DONE
        date_default_timezone_set('Asia/Jakarta');

        $ketemu         = false;
        $fields         = [];
        $type           = "";
        $select         = ["penguji", "mhs.nim"];

        if ($table == "t_sidang_munaqosah") $select[] = "pembimbing";

        $details = $this->newSidangDetails($table, $select, $nim, $idDosen);

        if (!empty($details)) $details = $details[0];
        else return false;

        $details->penguji = json_decode($details->penguji);
        if (property_exists($details, "pembimbing")) $details->pembimbing = json_decode($details->pembimbing);

        $editRevisi = function ($revisiItem, $detailRevisi, $deadineRevisi, $booleanStatus) {
            $revisiItem->detail_revisi = $detailRevisi;
            $revisiItem->tgl_revisi_deadline = $deadineRevisi;
            $revisiItem->status_revisi = $booleanStatus;

            return $revisiItem;
        };

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                $dosen->revisi[$idRevisi] = $editRevisi($dosen->revisi[$idRevisi], $detailRevisi, $deadineRevisi, $booleanStatus);
                $ketemu = true;
                $type = "penguji";
                break;
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen) {
                $dosen->revisi[$idRevisi] = $editRevisi($dosen->revisi[$idRevisi], $detailRevisi, $deadineRevisi, $booleanStatus);
                    $ketemu = true;
                    $type = "pembimbing";
                    break;
                }
            }
        }

        if (!empty($type)) {
            return $this->updateWrapper($table, $nim, $type, $details);
        } else return false;
    }

    public function newSetRevisiStatus($table, $nim, $idRevisi, $idDosen, $booleanStatus)
    {
        //DONE
        date_default_timezone_set('Asia/Jakarta');

        $ketemu         = false;
        $fields         = [];
        $type           = "";
        $select         = ["penguji", "mhs.nim"];

        if ($table == "t_sidang_munaqosah") $select[] = "pembimbing";

        $details = $this->newSidangDetails($table, $select, $nim, $idDosen);

        if (!empty($details)) $details = $details[0];
        else return false;

        $details->penguji = json_decode($details->penguji);
        if (property_exists($details, "pembimbing")) $details->pembimbing = json_decode($details->pembimbing);

        $editRevisi = function ($revisiItem, $booleanStatus) {
            $revisiItem->status_revisi = $booleanStatus=="true" ? true : false;

            return $revisiItem;
        };

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                $dosen->revisi[$idRevisi] = $editRevisi($dosen->revisi[$idRevisi], $booleanStatus);
                $ketemu = true;
                $type = "penguji";
                break;
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen) {
                    $dosen->revisi[] = $editRevisi($dosen->revisi[$idRevisi], $booleanStatus);
                    $ketemu = true;
                    $type = "pembimbing";
                    break;
                }
            }
        }

        if (!empty($type)) {
            return $this->updateWrapper($table, $nim, $type, $details);
        } else return false;
    }

    public function newEditNilai($table, $nim, $idDosen, $nilai)
    {
        //DONE
        date_default_timezone_set('Asia/Jakarta');

        $ketemu         = false;
        $fields         = [];
        $type           = "";
        $select         = ["penguji", "mhs.nim"];

        if ($table == "t_sidang_munaqosah") $select[] = "pembimbing";

        $details = $this->newSidangDetails($table, $select, $nim, $idDosen);

        if (!empty($details)) $details = $details[0];
        else return false;

        $details->penguji = json_decode($details->penguji);
        if (property_exists($details, "pembimbing")) $details->pembimbing = json_decode($details->pembimbing);

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                $dosen->nilai = $nilai;
                $ketemu = true;
                $type = "penguji";
                break;
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen) {
                    $dosen->nilai = $nilai;
                    $ketemu = true;
                    $type = "pembimbing";
                    break;
                }
            }
        }

        if (!empty($type)) {
            return $this->updateWrapper($table, $nim, $type, $details);
        } else return false;
    }

    public function newAddMahasiswaSidang($nim, $table, $idSidang, $judul = "", $penguji = [], $pembimbing = [])
    {
        //DONE!
        $dosenModel = new DosenModel($this->db);
        $authModel  = new \App\Models\AuthModel($this->db);

        $lengkap = true;

        foreach ($penguji as $eachPenguji) {
            $detailDosen = $dosenModel->findDosen($eachPenguji);

            if (empty($detailDosen)) {
                if ($lengkap) $lengkap = false;
            }
        }

        if ($lengkap) {
            foreach ($pembimbing as $eachPembimbing) {
                $detailDosen = $dosenModel->findDosen($eachPembimbing);

                if (empty($detailDosen)) {
                    if ($lengkap) $lengkap = false;
                }
            }
        }

        if (!$lengkap) return false;

        $cekUser = $authModel->getUser($nim);

        if (empty($cekUser)) {
            $result = $authModel->createUserMahasiswa($nim);
        }

        $pengujiJSON = [];
        $pembimbingJSON = [];
        $idStatusPenguji = [];
        $idStatusPembimbing = [];
        $judulFieldName = "";

        switch ($table) {
            case "t_sidang_proposal":
                $idStatusPenguji = [6, 7];
                $judulFieldName = "judul_proposal";
                break;
            case "t_sidang_kompre":
                $idStatusPenguji = [8, 9, 10];
                break;
            case "t_sidang_munaqosah":
                $idStatusPenguji = [11, 12];
                $idStatusPembimbing = [3, 4];
                $judulFieldName = "judul_munaqosah";
                break;
            default:
                return false;
        }

        foreach ($penguji as $index => $eachPenguji) {
            $detailDosen = new stdClass();

            $detailDosen->nilai = null;
            $detailDosen->revisi = [];
            $detailDosen->id_dosen = $eachPenguji;
            $detailDosen->jenis_status = $idStatusPenguji[$index];

            $pengujiJSON[] = $detailDosen;
        }

        if (!empty($pembimbing)) {
            foreach ($pembimbing as $index => $eachPembimbing) {
                $detailDosen = new stdClass();

                $detailDosen->nilai = null;
                $detailDosen->revisi = [];
                $detailDosen->id_dosen = $eachPembimbing;
                $detailDosen->jenis_status = $idStatusPembimbing[$index];

                $pembimbingJSON[] = $detailDosen;
            }
        }

        $fields = [
            "nim" => $nim,
            "id_sidang" => $idSidang
        ];

        if (!empty($judulFieldName)) {
            $fields = array_merge($fields, [$judulFieldName => $judul]);
        }

        if (!empty($penguji)) {
            $fields = array_merge($fields, ["penguji" => json_encode($pengujiJSON)]);
        }

        if (!empty($pembimbing)) {
            $fields = array_merge($fields, ["pembimbing" => json_encode($pembimbingJSON)]);
        }

        return $this->db->table($table)->insert($fields);
    }

    public function newCekNilai($table, $nim, $idDosen)
    {
        //DONE!
        $dataSidang = [];
        $ketemu = false;

        switch ($table) {
            case "t_sidang_proposal":
                $dataSidang = $this->newUP($nim, $idDosen);
                break;
            case "t_sidang_kompre":
                $dataSidang = $this->newKompre($nim, $idDosen);
                break;
            case "t_sidang_munaqosah":
                $dataSidang = $this->newMunaqosah($nim, $idDosen);
                break;
            default:
                return [];
        }

        if (!empty($dataSidang)) $details = $dataSidang;
        else return false;

        foreach ($details->penguji as $dosen) {
            if ($dosen->id_dosen == $idDosen) {
                return [$dosen];
            }
        }

        if (property_exists($details, "pembimbing") && !$ketemu) {
            foreach ($details->pembimbing as $dosen) {
                if ($dosen->id_dosen == $idDosen) {
                    return [$dosen];
                }
            }
        }
    }
}
