<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= @$header ?></h1>
    </div>

    <form action="<?= base_url("management/jadwal") ?>" method="post">
        <?= @$id_jadwal ?>
        <?php if (@$can_edit_tanggal) { ?>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tanggal</label>
                <div class="col-sm-10">
                    <select class="form-control" name="tanggal" required>
                        <option disabled selected>Pilih tanggal sidang</option>
                        <?php foreach ($listTanggal as $item) { ?>
                            <option value="<?= $item->id_tanggal_sidang ?>" <?= $item->id_tanggal_sidang == @$value_id_tanggal ? "selected" : "" ?>><?= $item->tgl_sidang ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Kelompok</label>
            <div class="col-sm-10">
                <select class="form-control" name="kelompok" required>
                    <option disabled selected>Pilih kelompok sidang</option>
                    <?php foreach ($listKelompok as $item) { ?>
                        <option value="<?= $item->id_kelompok_sidang ?>" <?= $item->id_kelompok_sidang == @$value_id_kelompok ? "selected" : "" ?>><?= $item->nama_kelompok_sidang ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Ruangan</label>
            <div class="col-sm-10">
                <select class="form-control" name="ruangan" required>
                    <option disabled selected>Pilih ruangan sidang</option>
                    <?php foreach ($listRuangan as $item) { ?>
                        <option value="<?= $item->id_ruang ?>" <?= $item->id_ruang == @$value_id_ruangan ? "selected" : "" ?>><?= "$item->kode_ruang/$item->nama_ruang" ?></option>
                    <?php } ?>
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