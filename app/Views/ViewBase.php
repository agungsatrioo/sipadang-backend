<!DOCTYPE html>

<head>
    <title><?= @$title ?></title>
    <?= @$css ?>
</head>

<body>
    <?= @$page ?>
    <?= @$js ?>
    <script>
        $('select').chosen();

        $("#p01").hide();
        $("#p02").hide();
        $("#p03").hide();
        $("#p11").hide();
        $("#p12").hide();
        $("#jdul").hide();

        $("#jenis_sidang").on('change', function(evt, params) {
            let value = params.selected;

            switch (value) {
                case "proposal":
                    $("#p01").show();
                    $("#p02").show();
                    $("#jdul").show();

                    $("#penguji1").attr('required');
                    $("#penguji2").attr('required');
                    $("#jdul").attr('required');

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

                    $("#penguji1").attr('required');
                    $("#penguji2").attr('required');
                    $("#penguji3").attr('required');

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

                    $("#penguji1").attr('required');
                    $("#penguji2").attr('required');
                    $("#penguji3").removeAttr('required');
                    $("#pembimbing1").attr('required');
                    $("#pembimbing2").attr('required');
                    $("#judul").attr('required');
                    break;
            }
        });
    </script>
</body>

</html>