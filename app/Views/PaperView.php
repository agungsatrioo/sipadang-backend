<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <title><?= @$title ?></title>
    <?= @$css ?>
    <style>
        @page {
            size: <?= $paperSize ?>
        }

        .font-times {
            font-family: 'Times New Roman';
            font-size: 11pt;
        }

        .text-center {
            text-align: center;
        }

        .kop-surat>i {
            font-size: 10pt;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th, td {
            padding: 5px;
        }

        thead {
            text-align: center;
            font-weight: bold;
            background-color: gray;
        }
    </style>
</head>

<body class="<?= $paperSize ?> font-times" onLoad="window.print()">
    <?= @$content ?>
</body>

</html>