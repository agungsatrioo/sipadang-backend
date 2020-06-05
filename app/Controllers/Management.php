<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use Exception;

class Management extends BaseController
{

    private function _createMenu()
    {
        $result = "";

        $menus = [
            ["type" => "item", "Dashboard", base_url("management"), "fa-tachometer-alt"],
            ["type" => "hr"],
            ["type" => "header", "Akademik"],
            ["type" => "item", "Pendaftar Sidang", base_url("management/list_sidang"), "fa-user-friends"],
            ["type" => "item", "Jadwal Sidang", base_url("management/jadwal"), "fa-calendar-alt"],
            ["type" => "item", "Tanggal Sidang", base_url("management/tanggal"), "fa-tasks"],
            ["type" => "item", "Ruangan Sidang", base_url("management/ruangan"), "fa-building"],
            ["type" => "hr"],
            ["type" => "header", "Cetak"],
            ["type" => "item", "Cetak Rekapitulasi", base_url("management/cetak"), "fa-print"],
            ["type" => "hr"],
            ["type" => "header", "Autentikasi"],
            ["type" => "item", "Reset Kata Sandi", base_url("management/reset_password"), "fa-key"],
        ];

        foreach ($menus as $menuItem) {
            switch ($menuItem['type']) {
                case "hr":
                    $result .= '<hr class="sidebar-divider">';
                    break;
                case "header":
                    $result .= '<div class="sidebar-heading">' . $menuItem[0] . '</div>';
                    break;
                case "item":
                    $active = current_url() == $menuItem[1] ? "active" : "";
                    $result .= '<li class="nav-item ' . $active . '">
                    <a class="nav-link" href="' . $menuItem[1] . '">
                      <i class="fas fa-fw ' . $menuItem[2] . '"></i>
                      <span>' . $menuItem[0] . '</span></a>
                  </li>';
                    break;
            }
        }

        return $result;
    }

    private function _merge($array)
    {
        $data['username'] = session("username");
        $data['logout_url'] = base_url("management/logout");
        $data['sidebar_menu'] = $this->_createMenu();

        return array_merge($data, $array);
    }

    public function index()
    {
        $data['title'] = "Management - Home";

        $data['dashboard_page'] = view("management/dashboard/DashboardHomeView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function list_sidang()
    {
        $data['title'] = "Management - Pendaftar Sidang";

        $request = $this->request;

        if (!empty($request->getPost("mahasiswaKeyword"))) {
            $sidangModel = new \App\Models\SidangModel($this->db);
            $mhsModel = new \App\Models\MahasiswaModel($this->db);

            $keyword = $request->getPost("mahasiswaKeyword");
            $sidangType = $request->getPost("sidang");

            $listMhs = $mhsModel->findMahasiswaByKeyword($keyword);
            $data['sidangType'] = $sidangType;

            if (!empty($listMhs)) {
                $detailSidang = [];
                switch ($sidangType) {
                    case "proposal":
                        try {
                            foreach ($listMhs as $eachMhs) {
                                $sidang = $sidangModel->newUP($eachMhs->nim);
                                if (!empty($sidang)) $detailSidang[] = $sidang;
                            }

                            if (empty($detailSidang)) throw new Exception("");

                            $data['detailSidang'] = $detailSidang;
                        } catch (Exception $e) {
                            session()->setFlashdata("error", "Data sidang tidak ditemukan.");
                        }
                        break;
                    case "kompre":
                        try {
                            foreach ($listMhs as $eachMhs) {
                                $sidang = $sidangModel->newKompre($eachMhs->nim);
                                if (!empty($sidang)) $detailSidang[] = $sidang;
                            }

                            if (empty($detailSidang)) throw new Exception("");

                            $data['detailSidang'] = $detailSidang;
                        } catch (Exception $e) {
                            session()->setFlashdata("error", "Data sidang tidak ditemukan.");
                        }
                        break;
                    case "munaqosah":
                        try {
                            foreach ($listMhs as $eachMhs) {
                                $sidang = $sidangModel->newMunaqosah($eachMhs->nim);
                                if (!empty($sidang)) $detailSidang[] = $sidang;
                            }

                            if (empty($detailSidang)) throw new Exception("");

                            $data['detailSidang'] = $detailSidang;
                        } catch (Exception $e) {
                            session()->setFlashdata("error", "Data sidang tidak ditemukan.");
                        }
                        break;
                }
            } else {
                session()->setFlashdata("error", "Data mahasiswa tidak ditemukan.");
            }
        } elseif (!empty($request->getPost('act'))) {
            $authModel = new \App\Models\AuthModel($this->db);
            $sidangModel = new \App\Models\SidangModel($this->db);

            $identity = $request->getPost("nim");
            $penguji = $request->getPost("penguji");
            $pembimbing = $request->getPost("pembimbing");
            $kelompok = $request->getPost("kelompok");
            $cekUser = $authModel->getUser($identity);

            if (empty($cekUser)) {
                $result = $authModel->createUserMahasiswa($identity);
            }

            if (empty($penguji)) {
                session()->setFlashdata("error", "Data penguji jangan dikosongkan.");
            } else {
                $sidang = $request->getPost("sidang");

                switch ($sidang) {
                    case "proposal":
                        $judul = $request->getPost("judul");

                        if (empty($judul)) {
                            session()->setFlashdata("error", "Judul jangan dikosongkan.");
                        } elseif (has_dupes($penguji)) {
                            session()->setFlashdata("error", "Tiap penguji harus satu orang yang berbeda.");
                        } elseif (!empty($penguji[0]) && !empty($penguji[1])) {
                            $result = $sidangModel->newAddMahasiswaSidang($identity, "t_sidang_proposal", $kelompok, $judul, $penguji);

                            if ($result) {
                                session()->setFlashdata("success", "Data peserta sidang berhasil dimasukkan.");
                            } else {
                                session()->setFlashdata("error", "Data peserta sidang gagal dimasukkan.");
                            }
                        } else {
                            session()->setFlashdata("error", "Ada salahsatu data penguji yang tidak ada.");
                        }
                        break;
                    case "kompre":
                        if (has_dupes($penguji)) {
                            session()->setFlashdata("error", "Tiap penguji harus satu orang yang berbeda.");
                        } elseif (!empty($penguji[0]) && !empty($penguji[1]) && !empty($penguji[2])) {
                            $result = $sidangModel->newAddMahasiswaSidang($identity, "t_sidang_kompre", $kelompok, "", $penguji);

                            if ($result) {
                                session()->setFlashdata("success", "Data peserta sidang berhasil dimasukkan.");
                            } else {
                                session()->setFlashdata("error", "Data peserta sidang gagal dimasukkan.");
                            }
                        } else {
                            session()->setFlashdata("error", "Ada salahsatu data penguji yang tidak ada.");
                        }
                        break;
                    case "munaqosah":
                        $judul = $request->getPost("judul");

                        if (empty($judul)) {
                            session()->setFlashdata("error", "Tiap penguji/pembimbing harus satu orang yang berbeda.");
                        } elseif (has_dupes($penguji) && has_dupes($pembimbing)) {
                            session()->setFlashdata("error", "Data penguji jangan duplikat.");
                        } elseif (!empty($penguji[0]) && !empty($penguji[1]) && !empty($pembimbing[0])  && !empty($pembimbing[1])) {
                            $result = $sidangModel->newAddMahasiswaSidang($identity, "t_sidang_munaqosah", $kelompok, $judul, $penguji, $pembimbing);

                            if ($result) {
                                session()->setFlashdata("success", "Data peserta sidang berhasil dimasukkan.");
                            } else {
                                session()->setFlashdata("error", "Data peserta sidang gagal dimasukkan.");
                            }
                        } else {
                            session()->setFlashdata("error", "Ada salahsatu data penguji yang tidak ada.");
                        }
                        break;
                }
            }
        }

        $data['result'] = view("management/dashboard/widget/MahasiswaDetailView.php", $data);

        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");
        $data['dashboard_page'] = view("management/dashboard/DashboardPendaftarSidangView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function ganti_judul($type, $nim)
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $detailSidang = [];
        $namaJudul = "";
        $request = $this->request;
        $table = "";

        switch ($type) {
            case "proposal":
                $data['title'] = "Management - Ganti Judul Proposal";
                $data['header'] = "Ganti Judul Proposal";
                $namaJudul = "judul_proposal";
                $table = "t_sidang_proposal";
                $data['action_url'] = base_url("management/judul_proposal/$nim/edit");
                break;
            case "munaqosah":
                $data['title'] = "Management - Ganti Judul Munaqosah";
                $data['header'] = "Ganti Judul Munaqosah";
                $namaJudul = "judul_munaqosah";
                $table = "t_sidang_munaqosah";
                $data['action_url'] = base_url("management/judul_munaqosah/$nim/edit");
                break;
        }

        if (!empty($request->getPost("add"))) {
            $judul = $request->getPost("judul");

            $result = $sidangModel->editJudul($table, $nim, $namaJudul, $judul);

            if ($result) {
                session()->setFlashdata("success", "Berhasil mengedit judul.");
            } else {
                session()->setFlashdata("error", "Gagal mengedit judul.");
            }
        }

        switch ($type) {
            case "proposal":
                $detailSidang = $sidangModel->newUP($nim);
                break;
            case "munaqosah":
                $detailSidang = $sidangModel->newMunaqosah($nim);
                break;
        }

        if (!empty($detailSidang)) {
            $data['nim'] = "<input type='hidden' name='nim' value='$nim'>";

            $data['judulSidang'] = $detailSidang->$namaJudul;
        }

        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['dashboard_page'] = view("management/dashboard/DashboardJudulFormView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function ganti_dosen($table, $type, $nim, $idDosenSidang)
    {
        $sidangModel = new \App\Models\SidangModel($this->db);
        $dosenModel = new DosenModel($this->db);

        $detailSidang = [];
        $request = $this->request;

        $data['title'] = "Management - Ganti Dosen";
        $data['header'] = "Ganti Dosen";

        $data['option_dosen'] =  $dosenModel->dosenOptionList();

        $data['action_url'] = base_url("management/$table/$type/$nim/$idDosenSidang/edit");

        switch ($table) {
            case "proposal":
                $table = "t_sidang_proposal";
                break;
            case "kompre":
                $table = "t_sidang_kompre";
                break;
            case "munaqosah":
                $table = "t_sidang_munaqosah";
                break;
        }

        if (!empty($request->getPost("add"))) {
            $dosen = $request->getPost("dosen");

            $result = $sidangModel->newEditDosen($table, $nim, $idDosenSidang, $dosen);

            if ($result) {
                session()->setFlashdata("success", "Berhasil mengedit judul.");
            } else {
                session()->setFlashdata("error", "Gagal mengedit judul.");
            }
        }

        switch ($table) {
            case "t_sidang_proposal":
                $detailSidang = $sidangModel->newUP($nim);
                break;
            case "t_sidang_kompre":
                $detailSidang = $sidangModel->newKompre($nim);
                break;
            case "t_sidang_munaqosah":
                $detailSidang = $sidangModel->newMunaqosah($nim);
                break;
        }

        if (!empty($detailSidang)) {
            $data['val_id_dosen'] = $detailSidang->$type[$idDosenSidang]->id_dosen;
        }


        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['dashboard_page'] = view("management/dashboard/DashboardGantiDosenView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function tanggal()
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $request = $this->request;

        $data['title'] = "Management - Daftar Tanggal Sidang";

        if (!empty($request->getPost("add"))) {
            $tanggal = $request->getPost("tanggal");
            $edit_id = $request->getPost("id_tanggal");

            if (empty($edit_id)) {
                $result = $sidangModel->addTanggalSidang($tanggal);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil menambahkan tanggal.");
                } else {
                    session()->setFlashdata("error", "Gagal menambahkan tanggal.");
                }
            } else {
                $result = $sidangModel->editTanggalSidang($edit_id, $tanggal);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil mengedit tanggal.");
                } else {
                    session()->setFlashdata("error", "Gagal mengedit tanggal.");
                }
            }
        }

        $data['tgl_list'] = $sidangModel->getTanggalSidang();


        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        if (empty($data['tgl_list'])) {
            $data['error'] = "Tidak ada data tanggal.";
        }

        $data['dashboard_page'] = view("management/dashboard/DashboardTanggalListView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function tanggal_form($id = "")
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        if (!empty($id)) {
            $data['header'] = "Edit Tanggal";
            $data['title'] = "Management - Edit Tanggal Sidang";
        } else {
            $data['title'] = "Management - Tambah Tanggal Sidang";
            $data['header'] = "Tambah Tanggal";
        }

        $data['action_url'] = base_url("management/tanggal");

        if (!empty($id)) {
            $tglList = $sidangModel->getTanggalSidang("", "$id");

            $data['tglSidang'] = $tglList[0]->tgl_jadwal_sidang;
            $data['id_tanggal'] = "<input type='hidden' name='id_tanggal' value='$id'>";
        }

        $data['min_date'] = strftime("%Y-%m-%d");

        $data['dashboard_page'] = view("management/dashboard/DashboardAddTanggalView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function jadwal()
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $data['title'] = "Management - Jadwal Sidang";

        $request = $this->request;

        if (!empty($request->getPost("act"))) {
            $tanggalId = $request->getPost("tanggal");
            $kelompokID = $request->getPost("kelompok");
            $ruanganID = $request->getPost("ruangan");
            $edit_id = $request->getPost("id_jadwal");

            if (empty($edit_id)) {
                $result = $sidangModel->addJadwalSidang($tanggalId, $kelompokID, $ruanganID);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil menambahkan jadwal.");
                } else {
                    session()->setFlashdata("error", "Gagal menambahkan jadwal.");
                }
            } else {
                $result = $sidangModel->editJadwalSidang($edit_id, $tanggalId, $kelompokID, $ruanganID);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil mengedit jadwal.");
                } else {
                    session()->setFlashdata("error", "Gagal mengedit jadwal.");
                }
            }
        }


        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['list_jadwal'] = $sidangModel->showJadwalSidang();

        if (empty($data['list_jadwal'])) {
            $data['error'] = "Tidak ada data jadwal.";
        }


        $data['dashboard_page'] = view("management/dashboard/DashboardJadwalListView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function jadwal_form($id = "")
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        if (!empty($id)) {
            $data['header'] = "Edit Jadwal";
            $data['title'] = "Management - Edit Jadwal Sidang";
        } else {
            $data['title'] = "Management - Tambah Jadwal Sidang";
            $data['header'] = "Tambah Jadwal";
        }


        $data['listTanggal']    = $sidangModel->getTanggalSidang("", "", true);
        $data['listKelompok']    = $sidangModel->showKelompokSidang();
        $data['listRuangan']    = $sidangModel->showRuanganSidang();
        $data['can_edit_tanggal'] = true;

        if (!empty($id)) {
            $jadwalDetails = $sidangModel->showJadwalSidang($id)[0];

            $data['id_jadwal'] = "<input type='hidden' name='id_jadwal' value='$id'>";
            $data['tanggal_sidang']    = $sidangModel->getTanggalSidang("", "$id", true);

            $data['can_edit_tanggal'] = $jadwalDetails->can_edit_tanggal;
            $data['value_id_tanggal'] = $jadwalDetails->id_tanggal_sidang;
            $data['value_id_kelompok'] = $jadwalDetails->id_kelompok_sidang;
            $data['value_id_ruangan'] = $jadwalDetails->id_ruang;
        }

        $data['dashboard_page'] = view("management/dashboard/DashboardJadwalFormView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function jadwal_delete($id)
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $result = $sidangModel->deleteJadwalSidang($id);

        if ($result) {
            session()->setFlashdata("success", "Berhasil menghapus jadwal.");
        } else {
            session()->setFlashdata("error", "Gagal menghapus jadwal.");
        }

        return redirect("management/jadwal");
    }


    public function tanggal_delete($id)
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $result = $sidangModel->deleteTanggalSidang($id);

        if ($result) {
            session()->setFlashdata("success", "Berhasil menghapus tanggal.");
        } else {
            session()->setFlashdata("error", "Gagal menghapus tanggal.");
        }

        return redirect("management/tanggal");
    }


    public function ruangan_delete($id)
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $result = $sidangModel->deleteJadwalSidang($id);

        if ($result) {
            session()->setFlashdata("success", "Berhasil menghapus ruangan.");
        } else {
            session()->setFlashdata("error", "Gagal menghapus ruangan.");
        }

        return redirect("management/ruangan");
    }

    public function ruangan()
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $request = $this->request;

        $data['title'] = "Management - Daftar Ruangan Sidang";

        if (!empty($request->getPost("act"))) {
            $kodeRuang = $request->getPost("kode");
            $namaRuang = $request->getPost("nama");
            $edit_id = $request->getPost("id_ruangan");

            if (empty($edit_id)) {
                $result = $sidangModel->addRuanganSidang($kodeRuang, $namaRuang);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil menambahkan ruangan.");
                } else {
                    session()->setFlashdata("error", "Gagal menambahkan ruangan.");
                }
            } else {
                $result = $sidangModel->editRuanganSidang($edit_id, $kodeRuang, $namaRuang);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil mengedit ruangan.");
                } else {
                    session()->setFlashdata("error", "Gagal mengedit ruangan.");
                }
            }

            $data['error'] = session()->getFlashdata("error");
            $data['success'] = session()->getFlashdata("success");
        }

        $data['ruang_list'] = $sidangModel->showRuanganSidang();

        if (empty($data['ruang_list'])) {
            $data['error'] = "Tidak ada data ruangan.";
        }

        $data['dashboard_page'] = view("management/dashboard/DashboardRuanganListView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function ruangan_form($id = "")
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        if (!empty($id)) {
            $data['header'] = "Edit Tanggal";
            $data['title'] = "Management - Edit Tanggal Sidang";
        } else {
            $data['title'] = "Management - Tambah Tanggal Sidang";
            $data['header'] = "Tambah Tanggal";
        }

        $data['action_url'] = base_url("management/ruangan");

        if (!empty($id)) {
            $tglList = $sidangModel->showRuanganSidang($id);

            $data['kd_ruangan'] = $tglList[0]->kode_ruang;
            $data['nama_ruangan'] = $tglList[0]->nama_ruang;

            $data['id_ruangan'] = "<input type='hidden' name='id_ruangan' value='$id'>";
        }

        $data['dashboard_page'] = view("management/dashboard/DashboardRuanganFormView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function cetak()
    {
        $data['title'] = "Management - Cetak";

        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['dashboard_page'] = view("management/dashboard/DashboardCetakSKView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function tambah_mhs()
    {
        $data['title'] = "Management - Tambah Mahasiswa";

        $authModel = new \App\Models\AuthModel($this->db);
        $sidangModel = new \App\Models\SidangModel($this->db);


        $mhsModel = new \App\Models\MahasiswaModel($this->db);
        $dosenModel = new \App\Models\DosenModel($this->db);

        $data['option_mhs'] = $mhsModel->populateMhs("___705____");
        $data['option_dosen'] = $dosenModel->populateDosen();
        $data['option_kelompok'] = $sidangModel->getKelompokSidang();
        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['dashboard_page'] = view("management/dashboard/DashboardTambahMhsView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function cetak_print()
    {
        $type = $this->request->getPost("rekap");
        $tanggal = $this->request->getPost("rekap_tgl");
        $withTidakLulus = $this->request->getPost("withTidakLulus");

        $result = [];

        switch ($type) {
            case "proposal":
                $result = $this->rekap_up("$tanggal", isset($withTidakLulus) ? true : false);
                break;
            case "kompre":
                $result = $this->rekap_kompre("$tanggal", isset($withTidakLulus) ? true : false);
                break;
            case "munaqosah":
                $result = $this->rekap_munaqosah("$tanggal", isset($withTidakLulus) ? true : false);
                break;
            default:
                break;
        }

        if (empty($result)) {
            session()->setFlashdata("error", "Tidak ada data pada tanggal yang dipilih.");
            return redirect()->to("cetak");
        } else echo $result;
    }

    private function rekap_up($tanggal, $withTidakLulus)
    {

        $maxMhs = 6;
        $line = 0;
        $page = 0;

        setlocale(LC_ALL, "id");

        $sidangModel = new \App\Models\SidangModel($this->db);
        $paperModel = new \App\Models\PaperModel($this->db);

        $tglInSurat = $sidangModel->tglIndonesia($tanggal, true);

        $data["title"] = "Rekapitulasi";
        $data["content"] = "";

        $dataUPfinal = [];
        $pagination = [];
        $paginationFinal = [];

        $dataUP = $sidangModel->getRekapUP($tanggal);

        if (empty($dataUP)) return [];

        if (!$withTidakLulus) {
            $dataUP = array_filter($dataUP, function ($obj) {
                if (!is_numeric($obj->nilai['nilai'])) return false;
                return true;
            });
        }

        foreach ($dataUP as $item) {
            $dataUPfinal[$item->id_kelompok_sidang]["name"] = $item->nama_kelompok_sidang;
            $dataUPfinal[$item->id_kelompok_sidang]["data"][] = $item;

            $pagination[$item->id_kelompok_sidang] = new \stdClass();
            $pagination[$item->id_kelompok_sidang]->id = $item->id_kelompok_sidang;
            $pagination[$item->id_kelompok_sidang]->nama = $item->nama_kelompok_sidang;
            $pagination[$item->id_kelompok_sidang]->count = count($dataUPfinal[$item->id_kelompok_sidang]["data"]);
        }

        ksort($dataUPfinal);
        ksort($pagination);

        foreach ($pagination as $pg) {
            $line += $pg->count;

            if ($line >= $maxMhs) {
                $pg->page = $page++;
                $line = 0;
            } else {
                $pg->page = $page;
            }
        }

        foreach ($pagination as $pg) {
            $paginationFinal[$pg->page][] = $dataUPfinal[$pg->id];
        }

        $a = 0;

        foreach ($paginationFinal as $halaman => $item) {
            $currPage = "";

            if ($halaman == 0) {
                $currPage .= $paperModel->kopSurat();

                $currPage .= '<h3 class="text-center">REKAPITULASI UJIAN PROPOSAL</h3>';
                $currPage .= '
                <div class="row">
                    <div class="col-lg-2">Hari/Tanggal</div>
                    <div class="col-lg-10">: ' . $tglInSurat . '</div>
                </div>';
                $currPage .= '
                <div class="row">
                    <div class="col-lg-2">Waktu</div>
                    <div class="col-lg-10">: 08:00 - selesai</div>
                </div><br>';
            }

            foreach ($item as $key => $datas) {
                $i = 0;
                $a++;

                $currPage .= $datas["name"];
                $nim = [];

                $currPage .= "
                    <table>
                    <thead>
                        <tr>
                            <td rowspan='2'>No.</td>
                            <td rowspan='2'>Nama</td>
                            <td rowspan='2'>NIM</td>
                            <td rowspan='2'>Jurusan</td>
                            <td colspan='2'>Penguji</td>
                            <td rowspan='2'>Jml</td>
                            <td rowspan='2'>Rata-rata</td>
                            <td rowspan='2'>Simbol</td>
                            <td rowspan='2'>Keterangan</td>
                            <td rowspan='2'>Majelis</td>
                        </tr>
                        <tr>
                            <td>I</td>
                            <td>II</td>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($datas["data"] as $key => $anggota) {
                    if (!in_array($anggota->nim, $nim)) {

                        foreach ($anggota->penguji as $eachPenguji) {
                            if ($eachPenguji->nilai == "Belum ada") $eachPenguji->nilai = 0;
                        }

                        if ($anggota->nilai['nilai'] == "Belum ada") {
                            $anggota->nilai['nilai'] = 0;
                            $anggota->keterangan = "TIDAK LULUS";
                        } else {
                            $anggota->keterangan = "LULUS";
                        }

                        $i++;

                        if ($i == 1) {
                            $mhsCount = count($datas["data"]);
                            $first = "<td rowspan='$mhsCount' class='text-center'>{$a}</td>";
                        } else {
                            $first = "";
                        }

                        $currPage .= "
                    <tr>
                        <td class='text-center'>$i</td>
                        <td>{$anggota->nama_mhs}</td>
                        <td class='text-center'>{$anggota->nim}</td>
                        <td class='text-center'>{$anggota->nama_jur}</td>
                        <td class='text-center'>{$anggota->penguji[0]->nilai}</td>
                        <td class='text-center'>{$anggota->penguji[1]->nilai}</td>
                        <td class='text-center'>{$anggota->jumlah}</td>
                        <td class='text-center'>{$anggota->nilai['nilai']}</td>
                        <td class='text-center'>{$anggota->nilai['mutu']}</td>
                        <td class='text-center'>{$anggota->keterangan}</td>
                        $first
                    </tr>
                    ";
                        $nim[] = $anggota->nim;
                    }
                }

                $currPage .= "</tbody></table><br>";
            }

            $newPage = "";

            $ttd = "
            <div class='row'>
                <div class='col-lg-7'>
                    <u>Keterangan</u>
                    <ol type='a'>
                        <li>70-79: Lulus dengan perbaikan</li>
                        <li>60-69: Lulus dengan perbaikan</li>
                        <li>50-59: Mengulang</li>
                        <li>0-49: Mengulang</li>
                    </ol>
                </div>                
                <div class='col-lg-5'>
                    Bandung, $tglInSurat<br>
                    Wakil Dekan I<br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    Dr.H. Moh. Dulkiah, M.Si.<br>
                    NIP. 197509242007101001 
                </div>
            </div>
        ";

            if ($halaman == count($paginationFinal) - 1) {
                if ($line < $maxMhs) {
                    $currPage .= $ttd;
                } else {
                    $newPage = $paperModel->newSheet($currPage);
                }
            }

            $currPage = $paperModel->newSheet($currPage);

            $data['content'] .= $currPage . $newPage;
        }

        return $this->renderPaper($data);
    }

    private function rekap_kompre($tanggal, $withTidakLulus)
    {

        $maxMhs = 6;
        $line = 0;
        $page = 0;

        setlocale(LC_ALL, "id");
        $sidangModel = new \App\Models\SidangModel($this->db);
        $paperModel = new \App\Models\PaperModel($this->db);

        $tglInSurat = $sidangModel->tglIndonesia($tanggal, true);


        $data["title"] = "Rekapitulasi";
        $data["content"] = "";

        $dataUPfinal = [];
        $pagination = [];
        $paginationFinal = [];

        $dataUP = $sidangModel->getRekapKompre($tanggal);

        if (empty($dataUP)) return [];

        if (!$withTidakLulus) {
            $dataUP = array_filter($dataUP, function ($obj) {
                if (!is_numeric($obj->nilai['nilai'])) return false;
                return true;
            });
        }

        foreach ($dataUP as $item) {
            $dataUPfinal[$item->id_kelompok_sidang]["name"] = $item->nama_kelompok_sidang;
            $dataUPfinal[$item->id_kelompok_sidang]["data"][] = $item;

            $pagination[$item->id_kelompok_sidang] = new \stdClass();
            $pagination[$item->id_kelompok_sidang]->id = $item->id_kelompok_sidang;
            $pagination[$item->id_kelompok_sidang]->nama = $item->nama_kelompok_sidang;
            $pagination[$item->id_kelompok_sidang]->count = count($dataUPfinal[$item->id_kelompok_sidang]["data"]);
        }

        ksort($dataUPfinal);
        ksort($pagination);

        foreach ($pagination as $pg) {
            $line += $pg->count;

            if ($line >= $maxMhs) {
                $pg->page = $page++;
                $line = 0;
            } else {
                $pg->page = $page;
            }
        }

        foreach ($pagination as $pg) {
            $paginationFinal[$pg->page][] = $dataUPfinal[$pg->id];
        }

        $a = 0;

        foreach ($paginationFinal as $halaman => $item) {
            $currPage = "";

            if ($halaman == 0) {
                $currPage .= $paperModel->kopSurat();

                $currPage .= '<h3 class="text-center">REKAPITULASI UJIAN KOMPREHENSIF</h3>';
                $currPage .= '
                <div class="row">
                    <div class="col-lg-2">Hari/Tanggal</div>
                    <div class="col-lg-10">: ' . $tglInSurat . '</div>
                </div>';
                $currPage .= '
                <div class="row">
                    <div class="col-lg-2">Waktu</div>
                    <div class="col-lg-10">: 08:00 - selesai</div>
                </div><br>';
            }

            foreach ($item as $key => $datas) {
                $i = 0;
                $a++;

                $currPage .= $datas["name"];
                $nim = [];

                $currPage .= "
                    <table>
                    <thead>
                        <tr>
                            <td rowspan='2'>No.</td>
                            <td rowspan='2'>Nama</td>
                            <td rowspan='2'>NIM</td>
                            <td rowspan='2'>Jurusan</td>
                            <td colspan='3'>Penguji</td>
                            <td rowspan='2'>Jml</td>
                            <td rowspan='2'>Rata-rata</td>
                            <td rowspan='2'>Simbol</td>
                            <td rowspan='2'>Keterangan</td>
                            <td rowspan='2'>Majelis</td>
                        </tr>
                        <tr>
                            <td>I</td>
                            <td>II</td>
                            <td>III</td>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($datas["data"] as $key => $anggota) {
                    if (!in_array($anggota->nim, $nim)) {

                        foreach ($anggota->penguji as $eachPenguji) {
                            if ($eachPenguji->nilai == "Belum ada") $eachPenguji->nilai = 0;
                        }

                        if ($anggota->nilai['nilai'] == "Belum ada") {
                            $anggota->nilai['nilai'] = 0;
                            $anggota->keterangan = "TIDAK LULUS";
                        } else {
                            $anggota->keterangan = "LULUS";
                        }

                        $i++;

                        if ($i == 1) {
                            $mhsCount = count($datas["data"]);
                            $first = "<td rowspan='$mhsCount' class='text-center'>{$a}</td>";
                        } else {
                            $first = "";
                        }

                        $currPage .= "
                    <tr>
                        <td class='text-center'>$i</td>
                        <td>{$anggota->nama_mhs}</td>
                        <td class='text-center'>{$anggota->nim}</td>
                        <td class='text-center'>{$anggota->nama_jur}</td>
                        <td class='text-center'>{$anggota->penguji[0]->nilai}</td>
                        <td class='text-center'>{$anggota->penguji[1]->nilai}</td>
                        <td class='text-center'>{$anggota->penguji[2]->nilai}</td>
                        <td class='text-center'>{$anggota->jumlah}</td>
                        <td class='text-center'>{$anggota->nilai['nilai']}</td>
                        <td class='text-center'>{$anggota->nilai['mutu']}</td>
                        <td class='text-center'>{$anggota->keterangan}</td>
                        $first
                    </tr>
                    ";
                        $nim[] = $anggota->nim;
                    }
                }

                $currPage .= "</tbody></table><br>";
            }

            $newPage = "";

            $ttd = "
            <div class='row'>
                <div class='col-lg-4'>
                    <u>Keterangan</u>
                    <ol type='a'>
                        <li>80–100 = Lulus (Amat Baik)</li>
                        <li>70–79 = Lulus (Baik)</li>
                        <li>60–69 = Lulus (Cukup)</li>
                        <li>0–59 = Tidak Lulus</li>
                    </ol>
                </div>                
                <div class='col-lg-4'>
                    <br>
                    Ketua Majelis Sidang,<br>
                    Wakil Dekan I,<br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    Dr.H. Moh. Dulkiah, M.Si.<br>
                    NIP. 197509242007101001 
                </div>
                <div class='col-lg-4'>
                    Bandung, $tglInSurat<br>
                    Sekretaris Sidang,<br>
                    Ketua Laboratorium,<br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    H. Wawan Setiawan Abdillah, M.Ag.<br>
                    NIP. 198002072011011004
                </div>
            </div>
        ";

            if ($halaman == count($paginationFinal) - 1) {
                if ($line < $maxMhs) {
                    $currPage .= $ttd;
                } else {
                    $newPage = $paperModel->newSheet($currPage);
                }
            }

            $currPage = $paperModel->newSheet($currPage);

            $data['content'] .= $currPage . $newPage;
        }

        return $this->renderPaper($data);
    }

    private function rekap_munaqosah($tanggal, $withTidakLulus)
    {

        $maxMhs = 6;
        $line = 0;
        $page = 0;

        setlocale(LC_ALL, "id");

        $sidangModel = new \App\Models\SidangModel($this->db);
        $paperModel = new \App\Models\PaperModel($this->db);

        $tglInSurat = $sidangModel->tglIndonesia($tanggal, true);

        $data["title"] = "Rekapitulasi";
        $data["content"] = "";

        $dataUPfinal = [];
        $pagination = [];
        $paginationFinal = [];

        $dataUP = $sidangModel->getRekapMunaqosah($tanggal);

        if (!$withTidakLulus) {
            $dataUP = array_filter($dataUP, function ($obj) {
                if (!is_numeric($obj->nilai['nilai'])) return false;
                return true;
            });
        }

        if (empty($dataUP)) return [];

        foreach ($dataUP as $item) {
            $dataUPfinal[$item->id_kelompok_sidang]["name"] = $item->nama_kelompok_sidang;
            $dataUPfinal[$item->id_kelompok_sidang]["data"][] = $item;

            $pagination[$item->id_kelompok_sidang] = new \stdClass();
            $pagination[$item->id_kelompok_sidang]->id = $item->id_kelompok_sidang;
            $pagination[$item->id_kelompok_sidang]->nama = $item->nama_kelompok_sidang;
            $pagination[$item->id_kelompok_sidang]->count = count($dataUPfinal[$item->id_kelompok_sidang]["data"]);
        }

        ksort($dataUPfinal);
        ksort($pagination);

        foreach ($pagination as $pg) {
            $line += $pg->count;

            if ($line >= $maxMhs) {
                $pg->page = $page++;
                $line = 0;
            } else {
                $pg->page = $page;
            }
        }

        foreach ($pagination as $pg) {
            $paginationFinal[$pg->page][] = $dataUPfinal[$pg->id];
        }

        $a = 0;

        foreach ($paginationFinal as $halaman => $item) {
            $currPage = "";

            if ($halaman == 0) {
                $currPage .= $paperModel->kopSurat();

                $currPage .= '<h3 class="text-center">REKAPITULASI NILAI UJIAN MUNAQOSAH</h3>';
                $currPage .= '
                <div class="row">
                    <div class="col-lg-2">Hari/Tanggal</div>
                    <div class="col-lg-10">: ' . $tglInSurat . '</div>
                </div>';
                $currPage .= '
                <div class="row">
                    <div class="col-lg-2">Waktu</div>
                    <div class="col-lg-10">: 08:00 - selesai</div>
                </div><br>';
            }

            foreach ($item as $key => $datas) {
                $i = 0;
                $a++;

                $currPage .= $datas["name"];
                $nim = [];

                $currPage .= "
                    <table>
                    <thead>
                        <tr>
                            <td rowspan='2'>No.</td>
                            <td rowspan='2'>Nama</td>
                            <td rowspan='2'>NIM</td>
                            <td rowspan='2'>Jurusan</td>
                            <td colspan='2'>Pembimbing</td>
                            <td colspan='2'>Penguji</td>
                            <td rowspan='2'>Rata-rata</td>
                            <td rowspan='2'>Simbol</td>
                            <td rowspan='2'>IPK</td>
                            <td rowspan='2'>Yudicium</td>
                            <td rowspan='2'>Majelis</td>
                        </tr>
                        <tr>
                            <td>I</td>
                            <td>II</td>
                            <td>I</td>
                            <td>II</td>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($datas["data"] as $key => $anggota) {
                    if (!in_array($anggota->nim, $nim)) {

                        foreach ($anggota->penguji as $eachPenguji) {
                            if ($eachPenguji->nilai == "Belum ada") $eachPenguji->nilai = 0;
                        }

                        foreach ($anggota->pembimbing as $eachPenguji) {
                            if ($eachPenguji->nilai == "Belum ada") $eachPenguji->nilai = 0;
                        }

                        if ($anggota->nilai['nilai'] == "Belum ada") {
                            $anggota->nilai['nilai'] = 0;
                            $anggota->keterangan = "TIDAK LULUS";
                        } else {
                            $anggota->keterangan = "LULUS";
                        }

                        $i++;

                        if ($i == 1) {
                            $mhsCount = count($datas["data"]);
                            $first = "<td rowspan='$mhsCount' class='text-center'>{$a}</td>";
                        } else {
                            $first = "";
                        }

                        $currPage .= "
                    <tr>
                        <td class='text-center'>$i</td>
                        <td>{$anggota->nama_mhs}</td>
                        <td class='text-center'>{$anggota->nim}</td>
                        <td class='text-center'>{$anggota->nama_jur}</td>
                        <td class='text-center'>{$anggota->pembimbing[0]->nilai}</td>
                        <td class='text-center'>{$anggota->pembimbing[1]->nilai}</td>                        
                        <td class='text-center'>{$anggota->penguji[0]->nilai}</td>
                        <td class='text-center'>{$anggota->penguji[1]->nilai}</td>
                        <td class='text-center'>{$anggota->nilai['nilai']}</td>
                        <td class='text-center'>{$anggota->nilai['mutu']}</td>
                        <td class='text-center'>?</td>
                        <td class='text-center'>{$anggota->keterangan}</td>
                        $first
                    </tr>
                    ";
                        $nim[] = $anggota->nim;
                    }
                }

                $currPage .= "</tbody></table><br>";
            }

            $newPage = "";

            $ttd = "
            <div class='row'>
                <div class='col-lg-7'>
                    <u>Keterangan</u>
                    <ol type='1'>
                        <li>Nilai Munaqosah 
                            <ol type='a'>
                                <li>80-100: 4</li>
                                <li>70-79,99: 3</li>
                                <li>60-69,99: 2</li>
                                <li>50-59,99: 1</li>
                            </ol>
                        </li>
                        <br>
                        <li>Indeks Prestasi Kumulatif 
                            <ol type='a'>
                                <li>IPK 3,51 - 4,00 = <i><b>Pujian</b></i> (<= 4 tahun)</li>
                                <li>IPK 3,01 - 3,50 = <i><b>Sangat Memuaskan</b></i> (> 4 tahun)</li>
                                <li>IPK 2,51 - 3,00 = <i><b> Memuaskan</b></i></li>
                            </ol>
                        </li>
                    </ol>
                </div>                
                <div class='col-lg-5'>
                    Bandung, $tglInSurat<br>
                    Wakil Dekan I<br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    Dr.H. Moh. Dulkiah, M.Si.<br>
                    NIP. 197509242007101001 
                </div>
            </div>
        ";

            if ($halaman == count($paginationFinal) - 1) {
                if ($line < $maxMhs) {
                    $currPage .= $ttd;
                } else {
                    $newPage = $paperModel->newSheet($currPage);
                }
            }

            $currPage = $paperModel->newSheet($currPage);

            $data['content'] .= $currPage . $newPage;
        }

        return $this->renderPaper($data);
    }

    public function reset_password()
    {
        $data['title'] = "Management- Ganti Kata Sandi";
        $data['action_url'] = base_url("management/reset_password");

        $request = $this->request;

        if(!empty($request->getPost("act"))) {
            $authModel = new AuthModel($this->db);
            $id = $request->getPost("identity");

            $cekUser = $authModel->getUser($id);

            if(empty($cekUser)) session()->setFlashdata("error", "Identitas yang dimasukkan tidak ada.");

            $result = $authModel->resetLupaPassword($id);

            if ($result) {
                session()->setFlashdata("success", "Berhasil mengatur ulang kata sandi.");
            } else {
                session()->setFlashdata("error", "Gagal mengatur ulang kata sandi.");
            }
        }

        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['dashboard_page'] = view("management/dashboard/DashboardResetPasswordView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function logout()
    {
        session()->destroy();

        return redirect("management");
    }


    public function mhs()
    {
        $mhsModel = new MahasiswaModel($this->db);

        $r = $this->db->table("t_mahasiswa")->get()->getResultObject();

        echo "INSERT INTO `t_mahasiswa_new`(`mhs_id`, `nim`, `nama`, `jur_kode`, `id_jns_daftar`, `id_jalur_masuk`, `id_agama`, `id_jns_keluar`, `mulai_smt`, `id_user`, `tgl_masuk_sp`, `jk`, `nisn`, `nik`, `tmpt_lahir`, `tgl_lahir`, `jln`, `rt`, `rw`, `nm_dsn`, `ds_kel`, `kode_pos`, `telepon_rumah`, `telepon_seluler`, `email`, `a_terima_kps`, `no_kps`, `stat_pd`, `nm_ayah`, `nik_ayah`, `tgl_lahir_ayah`, `id_jenjang_pendidikan_ayah`, `id_pekerjaan_ayah`, `id_penghasilan_ayah`, `nm_ibu_kandung`, `nik_ibu_kandung`, `tgl_lahir_ibu`, `id_jenjang_pendidikan_ibu`, `id_pekerjaan_ibu`, `id_penghasilan_ibu`, `nm_wali`, `tgl_lahir_wali`, `id_jenjang_pendidikan_wali`, `id_pekerjaan_wali`, `id_penghasilan_wali`, `id_jns_tinggal`, `kur_id`, `dosen_pemb`, `id_pembiayaan`, `kewarganegaraan`, `npwp`, `id_wil`, `id_alat_transport`, `kelas`, `pesan`, `sks_diakui`, `kode_pt_asal`, `kode_prodi_asal`) VALUES <br>";

       foreach($r as $a) {
        echo "(NULL, $a->nim, $a->nama_mhs, $a->kode_jurusan),<br>";
       }
    }
}
