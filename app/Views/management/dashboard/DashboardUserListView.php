<div class="container-fluid">

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Pengguna</h1>
    <a href="<?= base_url("management/users/add") ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengguna</a>
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
      if (!empty(@$userList)) { ?>
        <table class="table table-hover dataTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Identitas</th>
              <th scope="col">Nama</th>
              <th scope="col">Jenis</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($userList as $item) { ?>
              <tr>
                <th scope="row"><?= ++$i ?></th>
                <td><?= $item->identity ?></td>
                <td><?= $item->nama ?></td>
                <td><?= $item->type ?></td>
                <td>
                  <a href="<?= base_url("management/users/{$item->reset_id}/reset_password") ?>" class="btn btn-warning">Reset password</a>
                  <a href="<?= base_url("management/users/{$item->reset_id}/delete") ?>" class="btn  btn-outline-danger">Hapus</a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>

  </div>
</div>