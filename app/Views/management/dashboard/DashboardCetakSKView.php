<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cetak Rekapitulasi</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    Cetak Rekapitulasi
                </div>
                <div class="card-body">
                    <p>Cetak rekapitulasi nilai sidang berdasarkan tanggal. Isi dahulu tanggal pada form di bawah, lalu tekan salahsatu dari tiga tombol yag juga terdapat di bawah.</p>
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
                        </div
                        ><div class="col-lg-3">
                            <button type="submit" class="form-control btn btn-primary mb-2" name="rekap" value="munaqosah">Cetak utk Munaqosah</button>
                        </div>
                    </div>
                </form>
                <div class="row m-4">
                    <?= @$mhs_result ?>
                </div>
            </div>
        </div>
    </div>
</div>