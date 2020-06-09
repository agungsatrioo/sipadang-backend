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
                    Form mahasiswa
                </div>
                <form action="<?= $action_url ?>" method="post" class="m-4">
                    <?= @$input_hidden_mhs_id ?>
                    <input type="hidden" name="id_jns_daftar" value="1">
                    <input type="hidden" name="id_jalur_masuk" value="5">
                    <input type="hidden" name="id_agama" value="1">
                    <input type="hidden" name="stat_pd" value="A">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">NIM</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"  name="nim" value="<?= @$mhs->nim ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nama Mahasiswa</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="nama" value="<?= @$mhs->nama ?>">
                        </div>
                    </div> 
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">No. Telepon/WhatsApp</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="telepon_seluler" value="<?= @$mhs->telepon_seluler ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jurusan</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="jur_kode" required>
                                <option disabled selected>Pilih jurusan</option>
                                <?php foreach ($option_jurusan as $item) { ?>
                                    <option value="<?= $item->kode_jur ?>" <?= $item->kode_jur == @$mhs->jur_kode ? "selected" : "" ?>><?= "($item->kode_jur) $item->nama_resmi/$item->nama_jur" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="jk" required>
                                <option disabled selected>Pilih jenis kelamin</option>
                                <option value="L" <?= @$mhs->jk == "L" ? "selected" : "" ?>>Laki-laki</option>
                                <option value="P" <?= @$mhs->jk == "P" ? "selected" : "" ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit" name="add" value="true">Masukkan</button>
                </form>
            </div>
        </div>
    </div>
</div>