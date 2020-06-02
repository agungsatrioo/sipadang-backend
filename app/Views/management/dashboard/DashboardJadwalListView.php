<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Jadwal</h1>
    <a href="<?= base_url("management/jadwal/add") ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Tanggal</a>

  </div>

  <div class="row">
    <div class="col-lg-12">
      <?php
      $i = 0;
      if (!empty(@$list_jadwal)) { ?>
        <table class="table table-hover dataTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Tanggal Sidang</th>
              <th scope="col">Kelompok</th>
              <th scope="col">Ruangan</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($list_jadwal as $item) { ?>
              <tr>
                <th scope="row"><?= ++$i ?></th>
                <td><?= $item->tgl_sidang_fmtd ?></td>
                <td><?= $item->nama_kelompok_sidang ?></td>
                <td><?= "$item->kode_ruang / $item->nama_ruang" ?></td>
                <td>Edit, hapus</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>

  </div>
</div>