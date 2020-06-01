<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pendaftar Sidang</h1>
        <a href="<?= base_url("management/tambah_mhs") ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-add fa-sm text-white-50"></i> Tambah Mahasiswa</a>
    </div>

    <div class="row">
        <?php if (!empty($error)) { ?>
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <div class="col-lg-12">
                <div class="alert alert-success">
                    <?= $success ?>
                </div>
            </div>
        <?php } ?>
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    Pencarian Mahasiswa Sidang
                </div>
                <div class="card-body">
                    <p>Cari mahasiswa sidang dengan mengetik nama mahasiswa yang bersangkutan pada form di bawah ini.</p>
                </div>
                <form action="<?= base_url("management/list_sidang") ?>" method="post">
                    <div class="row m-2">
                        <div class="col-lg-6">
                            <input type="text" class="form-control" name="mahasiswaKeyword" required>
                        </div>
                        <div class="col-lg-5">
                            <select class="form-control" id="jenis_sidang" name="sidang" required>
                                <option disabled selected>Pilih jenis sidang</option>
                                <option value="proposal">Ujian Proposal</option>
                                <option value="kompre">Ujian Komprehensif</option>
                                <option value="munaqosah">Ujian Munaqosah</option>
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <button type="submit" class="btn btn-primary mb-2">Cari</button>
                        </div>
                    </div>
                </form>
                <div class="row m-4">
                    <?= @$mhs_result ?>
                </div>
            </div>
        </div>
    </div>

    <?= $result ?>
</div>