<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?= base_url('assets/images/site.webmanifest'); ?>">

    <!-- Title and Meta tags -->
    <?= include_title(); ?>
    <?= include_metas(); ?>

    <!-- Include CSS files -->
    <link href="<?= base_url('assets/libs/select2/css/select2.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/magnific-popup/magnific-popup.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" />
    <link href="<?= base_url('assets/libs/datatables.select/css/bootstrap-select.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" />
    <link href="<?= base_url('assets/css/custom.css'); ?>" rel="stylesheet" />
    <!-- JavaScript Variables -->
    <script>
        var baseUrl = "<?= base_url('assets'); ?>";
    </script>

    <!-- Include JavaScript files -->
    <script src="<?= base_url('assets/libs/jquery/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/plugin.js'); ?>"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
</head>