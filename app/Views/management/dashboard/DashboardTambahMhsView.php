<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Mahasiswa</h1>
    </div>

    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php } ?>

    <?php if (!empty($success)) { ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php } ?>

    <form action="<?= base_url("management/list_sidang") ?>" method="post">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">NIM</label>
            <div class="col-sm-10">
                <select class="form-control" id="nim" name="nim" required>
                    <option disabled selected>Masukkan NIM mahasiswa</option>
                    <?= $option_mhs ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Jenis Sidang</label>
            <div class="col-sm-10">
                <select class="form-control" id="jenis_sidang" name="sidang" required>
                    <option disabled selected>Pilih jenis sidang</option>
                    <option value="proposal">Ujian Proposal</option>
                    <option value="kompre">Ujian Komprehensif</option>
                    <option value="munaqosah">Ujian Munaqosah</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Kelompok Sidang</label>
            <div class="col-sm-10">
                <select class="form-control" name="kelompok" required>
                    <option disabled selected>Pilih kelompok sidang</option>
                    <?= $option_kelompok ?>
                </select>
            </div>
        </div>
        <div class="form-group row" id="jdul">
            <label class="col-sm-2 col-form-label">Judul</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="judul" name="judul">
            </div>
        </div>
        <div class="form-group row" id="p01">
            <label class="col-sm-2 col-form-label">Penguji I</label>
            <div class="col-sm-10">
                <select class="form-control" id="penguji1" name="penguji[]">
                    <option disabled selected>Masukkan nama dosen</option>
                    <?= $option_dosen ?>
                </select>
            </div>
        </div>
        <div class="form-group row" id="p02">
            <label class="col-sm-2 col-form-label">Penguji II</label>
            <div class="col-sm-10">
                <select class="form-control" id="penguji2" name="penguji[]">
                    <option disabled selected value>Masukkan nama dosen</option>
                    <?= $option_dosen ?>
                </select>
            </div>
        </div>
        <div class="form-group row" id="p03">
            <label class="col-sm-2 col-form-label">Penguji III</label>
            <div class="col-sm-10">
                <select class="form-control" id="penguji3" name="penguji[]">
                    <option disabled selected value>Masukkan nama dosen</option>
                    <?= $option_dosen ?>
                </select>
            </div>
        </div>
        <div class="form-group row" id="p11">
            <label class="col-sm-2 col-form-label">Pembimbing I</label>
            <div class="col-sm-10">
                <select class="form-control" id="pembimbing1" name="pembimbing[]">
                    <option disabled selected value>Masukkan nama dosen</option>
                    <?= $option_dosen ?>
                </select>
            </div>
        </div>
        <div class="form-group row" id="p12">
            <label class="col-sm-2 col-form-label">Pembimbing II</label>
            <div class="col-sm-10">
                <select class="form-control" id="pembimbing2" name="pembimbing[]">
                    <option disabled selected value>Masukkan nama dosen</option>
                    <?= $option_dosen ?>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary mb-2" name="act" value="tambah">Tambahkan</button>
            </div>
        </div>
    </form>
</div>