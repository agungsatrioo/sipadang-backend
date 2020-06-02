<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= @$header ?></h1>
    </div>

    <form action="<?= $action_url ?>" method="post">
        <?= @$id_ruangan ?>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Kode Ruangan</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="kode" value="<?= @$kd_ruangan ?>">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Nama Ruangan</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" name="nama" value="<?= @$nama_ruangan ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary mb-2" name="act" value="tambah">Tambahkan</button>
            </div>
        </div>
    </form>
</div>