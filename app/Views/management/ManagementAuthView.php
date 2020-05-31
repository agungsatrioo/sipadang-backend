<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-5 col-lg-6 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">SIPADANG FISIP UIN</h1>
                  </div>

                  <?php if(!empty($sess_error)) { ?>
                    <div class="alert alert-danger">
                      <?= $sess_error ?>
                    </div>
                  <?php } ?>

                  <?= $form_open ?>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="mgmtUsername" placeholder="Nama pengguna" required>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="mgmtPassword" placeholder="Kata sandi" required>
                    </div>
                    <?= $form_submit ?>
                    <?= $form_close ?>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>