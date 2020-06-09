<!DOCTYPE html>

<head>
    <title><?= @$title ?></title>
    <?= @$css ?>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>

<body>
    <?= @$page ?>
    <?= @$js ?>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('.dataTable').DataTable();
        $('.date-range').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('.date-range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + '~' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('select').chosen();

        $("#p01").hide();
        $("#p02").hide();
        $("#p03").hide();
        $("#p11").hide();
        $("#p12").hide();
        $("#jdul").hide();

        $("#f_dosen").hide();
        $("#f_mahasiswa").hide();

        $("#level").on('change', function(evt, params) {
            let value = params.selected;

            switch (value) {
                case "4":
                    $("#f_dosen").hide();
                    $("#f_mahasiswa").show();

                    $("#dosen").removeAttr('required');
                    $("#mahasiswa").attr('required', 'true');
                    break;
                case "5":
                    $("#f_dosen").show();
                    $("#f_mahasiswa").hide();

                    $("#mahasiswa").removeAttr('required');
                    $("#dosen").attr('required', 'true');
                    break;
            }
        });

        $("#jenis_sidang").on('change', function(evt, params) {
            let value = params.selected;

            switch (value) {
                case "proposal":
                    $("#p01").show();
                    $("#p02").show();
                    $("#jdul").show();

                    $("#penguji1").attr('required', 'true');
                    $("#penguji2").attr('required', 'true');
                    $("#jdul").attr('required', 'true');

                    $("#p03").hide();
                    $("#p11").hide();
                    $("#p12").hide();

                    $("#penguji3").removeAttr('required');
                    $("#pembimbing1").removeAttr('required');
                    $("#pembimbing2").removeAttr('required');
                    break;
                case "kompre":
                    $("#p01").show();
                    $("#p02").show();
                    $("#p03").show();

                    $("#penguji1").attr('required', 'true');
                    $("#penguji2").attr('required', 'true');
                    $("#penguji3").attr('required', 'true');

                    $("#p11").hide();
                    $("#p12").hide();
                    $("#jdul").hide();

                    $("#p11").removeAttr('required');
                    $("#p12").removeAttr('required');
                    $("#judul").removeAttr('required');
                    break;
                case "munaqosah":
                    $("#p01").show();
                    $("#p02").show();
                    $("#p03").hide();
                    $("#p11").show();
                    $("#p12").show();
                    $("#jdul").show();

                    $("#penguji1").attr('required', 'true');
                    $("#penguji2").attr('required', 'true');
                    $("#penguji3").removeAttr('required');
                    $("#pembimbing1").attr('required', 'true');
                    $("#pembimbing2").attr('required', 'true');
                    $("#judul").attr('required', 'true');
                    break;
            }
        });
    </script>
</body>

</html>