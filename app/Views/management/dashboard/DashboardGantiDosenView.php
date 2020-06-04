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
                    Ketikkan judul
                </div>
                <form action="<?= $action_url ?>" method="post">
                    <div class="row m-2">
                        <div class="col-lg-9">
                            <select class="form-control" name="dosen" required>
                                <option disabled selected>Pilih dosen sidang</option>
                                <?php foreach ($option_dosen as $item) { ?>
                                    <option value="<?= $item->id_dosen ?>" <?= $item->id_dosen == @$val_id_dosen ? "selected" : "" ?>><?= "($item->id_dosen) $item->nama_dosen" ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="form-control btn btn-primary mb-2" name="add" value="true">Masukkan</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>