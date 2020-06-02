<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= @$header ?></h1>
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

    <form action="<?= base_url("management/ruangan") ?>" method="post">
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Tanggal</label>
            <div class="col-sm-10">
                <select class="form-control" name="tanggal" required>
                    <option disabled selected>Pilih tanggal sidang</option>
                    <option value="proposal">Ujian Proposal</option>
                    <option value="kompre">Ujian Komprehensif</option>
                    <option value="munaqosah">Ujian Munaqosah</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Kelompok</label>
            <div class="col-sm-10">
                <select class="form-control" name="kelompok" required>
                    <option disabled selected>Pilih kelompok sidang</option>
                    <option value="proposal">Ujian Proposal</option>
                    <option value="kompre">Ujian Komprehensif</option>
                    <option value="munaqosah">Ujian Munaqosah</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Ruangan</label>
            <div class="col-sm-10">
                <select class="form-control" name="ruangan" required>
                    <option disabled selected>Pilih ruangan sidang</option>
                    <option value="proposal">Ujian Proposal</option>
                    <option value="kompre">Ujian Komprehensif</option>
                    <option value="munaqosah">Ujian Munaqosah</option>
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