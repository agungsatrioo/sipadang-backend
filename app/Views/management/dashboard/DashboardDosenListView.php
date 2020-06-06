<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Dosen</h1>
    <a href="<?= base_url("management/dosen/add") ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Dosen</a>
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
      if (!empty(@$dosenList)) { ?>
        <table class="table table-hover dataTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">ID</th>
              <th scope="col">NIP</th>
              <th scope="col">NIK</th>
              <th scope="col">NIDN</th>
              <th scope="col">Nama</th>
              <th scope="col">Fakultas/Jurusan</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($dosenList as $item) { ?>
              <tr>
                <th scope="row"><?= ++$i ?></th>
                <td><?= $item->id_dosen ?></td>
                <td><?= !empty($item->nip) ? $item->nip : "-" ?></td>
                <td><?= !empty($item->nik) ? $item->nik : "-" ?></td>
                <td><?= !empty($item->nidn) ? $item->nidn : "-" ?></td>
                <td><?= $item->nama_dosen ?></td>
                <td><?= "$item->nama_fak/$item->nama_jur" ?></td>
                <td>
                  <a href="<?= base_url("management/dosen/id_dosen~{$item->id_dosen}/edit") ?>" class="btn btn-primary">Edit</a>
                  <a href="<?= base_url("management/dosen/id_dosen~{$item->id_dosen}/delete") ?>" class="btn btn-danger">Hapus</a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>

  </div>
</div>