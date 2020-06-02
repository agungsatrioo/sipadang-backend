<?php

namespace App\Models;

use CodeIgniter\Model;
use DateTime;
use Exception;

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

        $result = $this->getSidangDetails("proposal", ["s_sidang.id_status", "mhs.nim", "nama_mhs", "judul_proposal", "tanggal_sidang as sidang_date_fmtd", "tanggal_sidang as sidang_date", "ruangan.kode_ruang", "nama_kelompok_sidang", "kelompok.id_kelompok_sidang", "nilai", "nama_jur"], $id_dosen, $nim, $date);

        foreach ($result as $key => $item) {
            $ada_nilai = true;

            if (!empty($nim)) {
                $penguji        = $this->getStatusDosenDiSidang($item->nim, "Penguji Sidang Proposal %");
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
                    $item->nilai = ["nilai" => floor($nilai), "mutu" => $this->_mutu($nilai), "color" => $this->warna($nilai)];
                }

                break;
            } else {
                $item->nilai = $this->cekNilai($item->id_status)[0];
            }
        }

        return !empty($nim) ? $result[0] : $result;
    }

    public function getMunaqosah($id_dosen, $nim,  $date = "")
    {
        $query = $this->getSidangDetails("munaqosah", ["s_sidang.id_status", "mhs.nim", "nama_mhs", "judul_munaqosah", "tanggal_sidang as sidang_date_fmtd", "tanggal_sidang as sidang_date", "ruangan.kode_ruang", "nama_kelompok_sidang", "kelompok.id_kelompok_sidang", "nilai", "nama_jur"], $id_dosen, $nim, $date);

        foreach ($query as $key => $item) {
            $ada_nilai = true;

            if (!empty($nim)) {
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

                break;
            } else {
                $item->nilai = $this->cekNilai($item->id_status)[0];
            }
        }

        return !empty($nim) ? $query[0] : $query;
    }

    public function getKompre($id_dosen, $nim,  $date = "")
    {
        $query = $this->getSidangDetails("kompre", ["s_sidang.id_status", "mhs.nim", "nama_mhs",  "tanggal_sidang as sidang_date_fmtd", "tanggal_sidang as sidang_date", "ruangan.kode_ruang", "nama_kelompok_sidang", "kelompok.id_kelompok_sidang", "nilai", "nama_jur"], $id_dosen, $nim, $date);

        $presentase_kompre = .333333333; //must be precise!

        foreach ($query as $key => $item) {
            $ada_nilai = true;

            if (!empty($nim)) {
                $penguji        = $this->getStatusDosenDiSidang($item->nim, "Penguji Sidang Komprehensif %");
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

                    $item->nilai = ["nilai" => floor($nilai), "mutu" => $this->_mutu($nilai), "color" => $this->warna($nilai)];
                }

                break;
            } else {
                $item->nilai = $this->cekNilai($item->id_status)[0];
            }
        }

        return !empty($nim) ? $query[0] : $query;
    }

    private function getSidangDetails($table, $fields, $id_dosen, $nim, $date = "")
    {
        $conditions = [];

        if (!empty($date)) {
            $conditions = ["tanggal_sidang" => $date];
        } elseif (!empty($id_dosen)) {
            $conditions = ["dsn.id_dosen" => $id_dosen];
        } elseif (!empty($nim)) {
            $conditions = ["mhs.nim" => $nim];
        }

        $query = $this->db->table("t_u_$table")
            ->select($fields)
            ->join("t_status_sidang s_sidang", "s_sidang.id_status = t_u_$table.id_status_sidang")
            ->join("t_status status", "s_sidang.id_status = status.id_status")
            ->join("t_sidang sidang", "s_sidang.id_sidang = sidang.id_sidang")
            ->join("t_mahasiswa mhs", "status.nim = mhs.nim")
            ->join("t_jurusan jur", "mhs.kode_jurusan = jur.kode_jur")
            ->join("t_dosen dsn", "status.id_dosen = dsn.id_dosen")
            ->join("t_kelompok_sidang kelompok", "sidang.id_kelompok_sidang = kelompok.id_kelompok_sidang")
            ->join("t_ruangan ruangan", "sidang.id_ruangan = ruangan.id_ruang")
            ->join("t_tanggal_sidang jadwal", "sidang.id_tanggal_sidang = jadwal.id_tanggal_sidang")
            ->join("t_nilai nilai", "s_sidang.id_status = nilai.id_status", "left")
            ->where($conditions)
            ->orderBy('sidang_date', 'DESC');

        $query = $query
            ->get()->getResultObject();

        foreach ($query as $it) {
            if (!empty($id_dosen)) {
                $it->keterangan_sidang = $it->nilai != null ?  "Sidang sudah dinilai" : "Belum sidang";
            } elseif (!empty($nim)) {
                unset($it->id_status);
            }

            $it->sidang_date_fmtd = $this->tglIndonesia($it->sidang_date_fmtd, true);
        }

        return $query;
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

    public function inputNilai($status, $nilai)
    {
        if ($this->cekIDStatus($status)) {
            $a = $this->db->table("t_nilai")
                ->insert(
                    [
                        "id_status" => $status,
                        "nilai"     => $nilai,
                    ]
                );

            if ($a) {
                return "ok";
            } else {
                return "400";
            }
        } else return "400";
    }

    public function editNilai($status, $nilai)
    {
        if ($this->cekIDStatus($status)) {
            $a = $this->db->table("t_nilai")
                ->where("id_status", $status)
                ->update(
                    [
                        "nilai"     => $nilai,
                    ]
                );

            if ($a) {
                return "ok";
            } else {
                return "400";
            }
        } else return "400";
    }

    public function getRevisi($where)
    {
        $query = $this->db->table("t_revisi")
            ->select("id_revisi, t_mahasiswa.nim, t_dosen.id_dosen, t_mahasiswa.nama_mhs, CONCAT(t_dosen.nama_dosen, ', ', IFNULL(t_dosen.gelar_depan, '')) as nama_dosen, t_jenis_status.nama_status, t_revisi.id_status, detail_revisi, tgl_revisi_input, tgl_revisi_deadline, status as status_revisi")
            ->where($where)
            ->join("t_status", "t_revisi.id_status = t_status.id_status")
            ->join("t_dosen", "t_status.id_dosen = t_dosen.id_dosen")
            ->join("t_mahasiswa", "t_status.nim = t_mahasiswa.nim")
            ->join("t_jenis_status", "t_status.id_jenis_status = t_jenis_status.id_jenis_status")
            ->get()->getResultObject();

        foreach ($query as $item) {
            $item->status_revisi = $item->status_revisi > 0 ? true : false;
        }

        return $query;
    }

    public function addRevisi($id_status, $detail_revisi, $deadline = "NULL", $status = false)
    {
        if ($this->cekIDStatus($id_status)) {
            $result = $this->db->table("t_revisi")
                ->insert([
                    "id_status"             => $id_status,
                    "detail_revisi"         => $detail_revisi,
                    "tgl_revisi_deadline"   => $deadline,
                    "status"                => $status ? 1 : 0
                ]);

            if ($result) {
                return "ok";
            } else {
                return "400";
            }
        } else return "400";
    }

    public function editRevisi($id_revisi, $id_status, $detail_revisi, $deadline = "NULL")
    {
        if ($this->cekIDStatus($id_status)) {
            $a = $this->db->table("t_revisi")
                ->where("id_revisi", $id_revisi)
                ->update(
                    [
                        "detail_revisi"         => $detail_revisi,
                        "tgl_revisi_edit"       => date('m/d/Y h:i:s a', time()),
                        "tgl_revisi_deadline"   => $deadline,
                    ]
                );

            if ($a) {
                return "ok";
            } else {
                return "400";
            }
        } else return "400";
    }

    public function deleteRevisi($id_revisi, $id_status)
    {
        if ($this->cekIDStatus($id_status)) {
            $a = $this->db->table("t_revisi")
                ->where("id_revisi", $id_revisi)
                ->delete();

            if ($a != false) {
                return "ok";
            } else {
                return "400";
            }
        } else return "400";
    }

    public function markRevisi($id_revisi, $id_status, $status = false)
    {
        if ($this->cekIDStatus($id_status)) {
            $a = $this->db->table("t_revisi")
                ->where("id_revisi", $id_revisi)
                ->update(
                    [
                        "status"                => $status ? 1 : 0
                    ]
                );

            if ($a) {
                return "ok";
            } else {
                return "400";
            }
        } else return "400";
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
        $query = $this->getKompre("", "", $date);

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
}
