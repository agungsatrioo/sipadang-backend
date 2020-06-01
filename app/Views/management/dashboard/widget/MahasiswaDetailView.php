<?php if (!empty(@$detailUP)) foreach ($detailUP as $item) { ?>
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
                                <span class="initials">A</span>
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
                            <?php if (!empty($item->penguji))  foreach (@$item->penguji as $eachPenguji) {  ?>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <b><?= $eachPenguji->nama_status ?></b>
                                    </div>
                                    <div class="col-lg-9">
                                        <?= $eachPenguji->nama_dosen ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($item->pembimbing)) foreach (@$item->pembimbing as $eachPembimbing) {  ?>
                                <div class="row">
                                    <div class="col-lg-3">
                                        <b><?= $eachPembimbing->nama_status ?></b>
                                    </div>
                                    <div class="col-lg-9">
                                        <?= $eachPembimbing->nama_dosen ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <hr>
                        </div>
                        <br>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>