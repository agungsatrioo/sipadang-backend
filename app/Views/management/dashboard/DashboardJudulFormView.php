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
                    <?= $nim ?>
                    <div class="row m-2">
                        <div class="col-lg-9">
                            <input type="text" class="form-control" name="judul" value="<?= @$judulSidang ?>" required>
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