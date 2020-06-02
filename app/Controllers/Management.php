<?php

namespace App\Controllers;

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
            ["type" => "item", "Pendaftar Sidang", base_url("management/list_sidang"), "fa-table"],
            ["type" => "item", "Jadwal Sidang", base_url("management/jadwal"), "fa-clock"],
            ["type" => "item", "Tanggal Sidang", base_url("management/tanggal"), "fa-clock"],
            ["type" => "item", "Ruangan Sidang", base_url("management/ruangan"), "fa-door"],
            ["type" => "hr"],
            ["type" => "header", "Cetak"],
            ["type" => "item", "Cetak Rekapitulasi", base_url("management/cetak"), "fa-print"],
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

            $viewDetails = "";

            $sidangModel = new \App\Models\SidangModel($this->db);
            $mhsModel = new \App\Models\MahasiswaModel($this->db);

            $keyword = $request->getPost("mahasiswaKeyword");
            $sidangType = $request->getPost("sidang");

            $listMhs = $mhsModel->findMahasiswaByKeyword($keyword);

            if (!empty($listMhs)) {
                switch ($sidangType) {
                    case "proposal":
                        $detailUP = [];
                        try {
                            foreach ($listMhs as $eachMhs) {
                                $detailUP[] = $sidangModel->getUP("", $eachMhs->nim);
                            }
                            $data['detailUP'] = $detailUP;
                        } catch (Exception $e) {
                            session()->setFlashdata("error", "Data sidang tidak ditemukan.");
                        }
                        break;
                    case "kompre":
                        break;
                    case "munaqosah":
                        break;
                }
            } else {
                session()->setFlashdata("error", "Data mahasiswa tidak ditemukan.");
            }
        }elseif (!empty($request->getPost('act'))) {
            $authModel = new \App\Models\AuthModel($this->db);
            $sidangModel = new \App\Models\SidangModel($this->db);
            
            $identity = $request->getPost("nim");
            $penguji = $request->getPost("penguji");
            $pembimbing = $request->getPost("pembimbing");
            $kelompok = $request->getPost("kelompok");
            $cekUser = $authModel->getUser($identity);

            if (empty($cekUser)) {
                //$result = $authModel->createUserMahasiswa($identity);
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
                            $statusUP = $sidangModel->addStatusUP($identity, $penguji);
                            $sttsSidang = $sidangModel->addStatusSidang($statusUP, $kelompok);
                            $detailSidang =  $sidangModel->addDetailSidangProposal($statusUP, $judul);

                            if ($detailSidang) {
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
                            $statusUP = $sidangModel->addStatusKompre($identity, $penguji);
                            $sttsSidang = $sidangModel->addStatusSidang($statusUP, $kelompok);
                            $detailSidang =  $sidangModel->addDetailSidangKompre($statusUP);

                            if ($detailSidang) {
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
                            $statusUP = $sidangModel->addStatusMunaqosah($identity, $penguji, $pembimbing);
                            $sttsSidang = $sidangModel->addStatusSidang($statusUP, $kelompok);
                            $detailSidang =  $sidangModel->addDetailSidangMunaqosah($statusUP, $judul);

                            if ($detailSidang) {
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
        $data['dashboard_page'] = view("management/dashboard/DashboardPendaftarSidangView", $data);

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

        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['tgl_list'] = $sidangModel->getTanggalSidang();

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

        $data['list_jadwal'] = $sidangModel->showJadwalSidang();

        $data['dashboard_page'] = view("management/dashboard/DashboardJadwalListView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function jadwal_form($id = "")
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $data['title'] = "Management - Tambah Jadwal Sidang";
        $data['header'] = 'Tambah Jadwal';

        $data['listKelompok'] = "";

        $data['dashboard_page'] = view("management/dashboard/DashboardJadwalFormView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function ruangan()
    {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $request = $this->request;

        $data['title'] = "Management - Daftar Ruangan Sidang";

        if (!empty($request->getPost("add"))) {
            $tanggal = $request->getPost("tanggal");
            $edit_id = $request->getPost("id_tanggal");

            if (empty($edit_id)) {
                $result = $sidangModel->addTanggalSidang($tanggal);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil menambahkan ruangan.");
                } else {
                    session()->setFlashdata("error", "Gagal menambahkan ruangan.");
                }
            } else {
                $result = $sidangModel->editTanggalSidang($edit_id, $tanggal);

                if ($result) {
                    session()->setFlashdata("success", "Berhasil mengedit ruangan.");
                } else {
                    session()->setFlashdata("error", "Gagal mengedit ruangan.");
                }
            }
        }

        $data['error'] = session()->getFlashdata("error");
        $data['success'] = session()->getFlashdata("success");

        $data['ruang_list'] = $sidangModel->showRuanganSidang();

        $data['dashboard_page'] = view("management/dashboard/DashboardRuanganListView", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function ruangan_form($id = "") {
        $sidangModel = new \App\Models\SidangModel($this->db);

        $data['title'] = "Management - Tambah Ruangan Sidang";
        $data['header'] = 'Tambah Ruangan';

        $data['dashboard_page'] = view("management/dashboard/DashboardRuanganFormView.php", $data);

        echo $this->renderPage('management/dashboard/DashboardBaseView', $this->_merge($data));
    }

    public function cetak()
    {
        $data['title'] = "Management - Cetak";

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

        switch ($type) {
            case "proposal":
                return $this->rekap_up("$tanggal");
            case "kompre":
                return $this->rekap_kompre("$tanggal");
            case "munaqosah":
                return $this->rekap_munaqosah("$tanggal");
            default:

                break;
        }
    }

    private function rekap_up($tanggal)
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

        $dataUP = array_filter($dataUP, function ($obj) {
            static $idList = array();

            $includeTidakLulus = true;

            if ($includeTidakLulus) {
                //if (!is_numeric($obj->nilai['nilai'])) return false;
            }

            if (in_array($obj->nim, $idList)) {
                return false;
            }

            $idList[] = $obj->nim;
            return true;
        });

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

        echo $this->renderPaper($data);
    }

    public function rekap_kompre($tanggal)
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

        $dataUP = array_filter($dataUP, function ($obj) {
            static $idList = array();

            $includeTidakLulus = false;

            if ($includeTidakLulus) {
                if (!is_numeric($obj->nilai['nilai'])) return false;
            }

            if (in_array($obj->nim, $idList)) {
                return false;
            }

            $idList[] = $obj->nim;
            return true;
        });

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

        echo $this->renderPaper($data);
    }

    public function rekap_munaqosah($tanggal)
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

        $dataUP = array_filter($dataUP, function ($obj) {
            static $idList = array();

            $includeTidakLulus = true;

            if ($includeTidakLulus) {
                //if (!is_numeric($obj->nilai['nilai'])) return false;
            }

            if (in_array($obj->nim, $idList)) {
                return false;
            }

            $idList[] = $obj->nim;
            return true;
        });

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

        echo $this->renderPaper($data);
    }

    public function logout()
    {
        session()->destroy();

        return redirect("management");
    }
}
