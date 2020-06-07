<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $header ?></h1>
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
                    Form tambah pengguna
                </div>
                <form action="<?= $action_url ?>" method="post" class="m-4">
                <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Level</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="level" name="level" required>
                                <option disabled selected>Pilih level</option>
                                <option value="4">Mahasiswa</option>
                                <option value="5">Dosen</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row"  id="f_dosen">
                        <label class="col-sm-2 col-form-label">Dosen</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="dosen" name="id_dosen">
                                <option disabled selected>Pilih dosen</option>
                                <?php foreach ($option_dosen as $item) { ?>
                                    <option value="<?= $item->id_dosen ?>"><?= "($item->id_dosen) $item->nama_dosen" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row"  id="f_mahasiswa">
                        <label class="col-sm-2 col-form-label">Mahasiswa</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="mahasiswa" name="mhs_id">
                                <option disabled selected>Pilih mahasiswa</option>
                                <?php foreach ($option_mhs as $item) { ?>
                                    <option value="<?= $item->nim ?>"><?= "($item->nim) $item->nama" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="add" value="true">Masukkan</button>
                </form>
            </div>
        </div>
    </div>
</div>