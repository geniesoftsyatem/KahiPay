<?php
$session = \Config\Services::session();
$successMessage = $session->getFlashdata('success') ? $session->getflashdata('success') : '';
$errorMessage = $session->getFlashdata('error') ? $session->getFlashdata('error') : '';
if ($successMessage) {
?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $successMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <br>
<?php } else if ($errorMessage) { ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $errorMessage; ?><br>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>