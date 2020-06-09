<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cetak Rekapitulasi</h1>
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
                    Cetak Daftar Majelis
                </div>
                <div class="card-body">
                    <p>Cetak rekapitulasi majelis.</p>
                </div>
                <form action="<?= base_url("management/cetak_print") ?>" method="post">
                    <div class="row m-2">
                        <div class="col-lg-9">
                            <input type="text" class="form-control date-range" name="rekap_tgl" required readonly>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="form-control btn btn-primary mb-2" name="rekap" value="majelis">Cetak</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    Cetak Rekapitulasi
                </div>
                <div class="card-body">
                    <p>Cetak rekapitulasi nilai sidang berdasarkan tanggal. Isi dahulu tanggal pada form, lalu tekan salahsatu dari tiga tombol yang juga terdapat di bawah.</p>
                </div>
                <form action="<?= base_url("management/cetak_print") ?>" method="post">
                    <div class="row m-2">
                        <div class="col-lg-3">
                            <input type="date" class="form-control" name="rekap_tgl" required>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="form-control btn btn-primary mb-2" name="rekap" value="proposal">Cetak utk. Proposal</button>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="form-control btn btn-primary mb-2" name="rekap" value="kompre">Cetak utk. Komprehensif</button>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" class="form-control btn btn-primary mb-2" name="rekap" value="munaqosah">Cetak utk Munaqosah</button>
                        </div>
                    </div>
                    <div class="row m-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="ya" id="withTidakLulus" name="withTidakLulus">
                            <label class="form-check-label" for="withTidakLulus">
                                Sertakan dengan mahasiswa yang tidak lulus
                            </label>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>