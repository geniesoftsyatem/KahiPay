<?php
// Retrieve the session service
$session = \Config\Services::session();
// Get the success message from the flash data, or set it to an empty string if not present
$successMessage = $session->getFlashdata('success') ?: '';
// Get the error message from the flash data, or set it to an empty string if not present
$errorMessage = $session->getFlashdata('error') ?: '';
?>
<?php if ($successMessage) { ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo esc($successMessage); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <br>
<?php } elseif ($errorMessage) { ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        if (is_array($errorMessage)) {
            foreach ($errorMessage as $error) {
                echo esc($error) . '<br>';
            }
        } else {
            echo esc($errorMessage);
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>