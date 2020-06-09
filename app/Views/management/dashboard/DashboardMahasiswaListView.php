<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Mahasiswa</h1>
    <a href="<?= base_url("management/mahasiswa/add") ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Mahasiswa</a>
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
      <?php
      $i = 0;
      if (!empty(@$mhsList)) { ?>
        <table class="table table-hover dataTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">NIM</th>
              <th scope="col">Nama</th>
              <th scope="col">JK</th>
              <th scope="col">No. Tel/WA</th>
              <th scope="col">Jurusan</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($mhsList as $item) { ?>
              <tr>
                <th scope="row"><?= ++$i ?></th>
                <td><?= $item->nim ?></td>
                <td><?= $item->nama ?></td>
                <td><?= $item->jk ?></td>
                <td><?= !empty($item->telepon_seluler) ? $item->telepon_seluler : "-" ?></td>
                <td><?= $item->nama_jur ?></td>
                <td>
                  <a href="<?= base_url("management/mahasiswa/{$item->mhs_id}/edit") ?>" class="btn btn-primary">Edit</a>
                  <a href="<?= base_url("management/mahasiswa/{$item->mhs_id}/delete") ?>" class="btn btn-danger">Hapus</a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>

  </div>
</div>