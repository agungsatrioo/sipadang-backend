<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Tanggal Sidang</h1>
    <a href="<?= base_url("management/tanggal/add") ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Tanggal</a>
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
      if (!empty(@$tgl_list)) { ?>
        <table class="table table-hover dataTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Tanggal Sidang</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tgl_list as $item) { ?>
              <tr>
                <th scope="row"><?= ++$i ?></th>
                <td><?= $item->tgl_sidang ?></td>
                <td>
                  <a href="<?= base_url("management/tanggal/{$item->id_tanggal_sidang}/edit") ?>" class="btn btn-primary">Edit</a>
                  <a href="<?= base_url("management/tanggal/{$item->id_tanggal_sidang}/delete") ?>" class="btn btn-danger">Hapus</a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>

  </div>
</div>