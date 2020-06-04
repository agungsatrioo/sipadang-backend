<?php if (!empty(@$detailSidang)) foreach ($detailSidang as $item) { ?>
    <div class="row">
        <div class="col-lg-12">
            <!-- Basic Card Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Pencarian</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2">
                            <div class="avatar-circle bg-primary float-right">
                                <span class="initials"> <?= $item->nama_mhs[0] ?></span>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="row">
                                <div class="col-lg-3">
                                    <b>NIM</b>
                                </div>
                                <div class="col-lg-9">
                                    <?= $item->nim ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <b>Nama Mahasiswa</b>
                                </div>
                                <div class="col-lg-9">
                                    <?= $item->nama_mhs ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <b>Jurusan</b>
                                </div>
                                <div class="col-lg-9">
                                    <?= $item->nama_jur ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-lg-3">
                                    <b>Tanggal Sidang</b>
                                </div>
                                <div class="col-lg-9">
                                    <?= $item->sidang_date_fmtd ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <b>Kelompok Sidang</b>
                                </div>
                                <div class="col-lg-9">
                                    <?= $item->nama_kelompok_sidang ?>
                                </div>
                            </div>
                            <hr>
                            <?php if (!empty($item->judul_proposal)) { ?>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <b>Judul</b>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <?= $item->judul_proposal ?>
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="<?= base_url("management/judul_proposal/{$item->nim}/edit") ?>" class="btn btn-link">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                            <?php } ?>
                            <?php if (!empty($item->judul_munaqosah)) { ?>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <b>Judul</b>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <?= $item->judul_munaqosah ?>
                                            </div>
                                            <!--
                                            <div class="col-lg-2">
                                            <a href="<?= base_url("management/judul_munaqosah/{$item->nim}/edit") ?>" class="btn btn-link">Edit</a>
                                            </div>
                                            -->
                                        </div>
                                    </div>
                                </div>
                                <br>
                            <?php } ?>
                            <?php if (!empty($item->penguji))  foreach (@$item->penguji as $eachPenguji) {  ?>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <b><?= $eachPenguji->nama_status ?></b>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <?= $eachPenguji->nama_dosen ?>
                                            </div>
                                            
                                            <div class="col-lg-2">
                                                <a href="<?= base_url("management/$sidangType/penguji/{$item->nim}/{$eachPenguji->id_status}/edit") ?>" class="btn btn-link">Edit</a>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($item->pembimbing)) foreach (@$item->pembimbing as $eachPembimbing) {  ?>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <b><?= $eachPembimbing->nama_status ?> </b>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-10">
                                                <?= $eachPembimbing->nama_dosen ?>
                                            </div>
                                            <div class="col-lg-2">
                                                <a href="<?= base_url("management/$sidangType/pembimbing/{$item->nim}/{$eachPembimbing->id_status}/edit") ?>" class="btn btn-link">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <br>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>