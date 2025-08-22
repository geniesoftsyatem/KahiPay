<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>


<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-body {
        padding: 2rem;
    }

    .card-title {
        color: #2c3e50;
        font-weight: 600;
    }



    .input-group-text {
        background-color: #f8f9fa;
        min-width: 40px;
        justify-content: center;
    }

    .btn-outline-secondary {
        border-color: #d1d3e2;
    }

    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        padding: 0.5rem 1.75rem;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }

    .file-upload-container {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .file-upload-container:hover {
        border-color: #adb5bd;
    }

    .file-upload-label {
        display: block;
        padding: 0.5rem;
        font-size: 1rem;
        font-weight: 400;
        color: #495057;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        transition: all 0.15s;
        cursor: pointer;
        text-align: center;
    }

    .file-upload-label:hover {
        background-color: #f8f9fa;
    }

    .file-upload-input {
        display: none;
    }

    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .image-preview {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 5px;
        overflow: hidden;
        border: 1px solid #ddd;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .image-counter {
        font-size: 0.8rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .existing-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .existing-image {
        position: relative;
        width: 100px;
        height: 100px;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-2">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="fas <?= isset($letter) ? 'fa-edit' : 'fa-plus-circle'; ?> mr-2 text-primary"></i>
                                <?= $pagetitle; ?>
                            </h4>
                            <a href="<?= site_url('request-letters'); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>

                        <?= view('admin/_topmessage'); ?>

                        <form class="needs-validation" method="post" action="<?= site_url('request-letters/save'); ?>" enctype="multipart/form-data" novalidate>
                            <input type="hidden" name="request_id" value="<?= isset($letter) ? $letter['request_id'] : ''; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="fw-bold">Title <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        <input type="text" class="form-control" id="title" name="title"
                                            placeholder="Enter request letter title"
                                            value="<?= isset($letter) ? esc($letter['title']) : ''; ?>" required>
                                        <div class="invalid-feedback">Please provide a title.</div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="employee_id" class="fw-bold">Employee <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <select class="form-control select2" id="employee_id" name="employee_id" required>
                                            <option value="">Select Employee</option>
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?= $employee['employee_id'] ?>"
                                                    <?= (isset($letter) && $letter['employee_id'] == $employee['employee_id']) ? 'selected' : '' ?>>
                                                    <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select an employee.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="reporting_employee_id" class="fw-bold">Reporting Manager <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="reporting_employee_id" name="reporting_employee_id" required>
                                    <option value="">Select Reporting Manager</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?= $employee['employee_id'] ?>"
                                            <?= (isset($letter) && $letter['reporting_employee_id'] == $employee['employee_id']) ? 'selected' : '' ?>>
                                            <?= esc($employee['first_name'] . ' ' . $employee['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a reporting manager.</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="fw-bold">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="Enter request details" required><?= isset($letter) ? esc($letter['description']) : ''; ?></textarea>
                                <div class="invalid-feedback">Please provide a description.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Upload Images (Max 5)</label>
                                <div class="file-upload-container">
                                    <label for="imageUpload" class="file-upload-label">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Choose Images
                                    </label>
                                    <input type="file" id="imageUpload" name="images[]" accept="image/*" multiple <?= empty($letter) ? 'required' : ''; ?>>
                                    <div class="image-counter" id="imageCounter">0/5 images selected</div>
                                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                                </div>

                                <?php if (!empty($letter['images'])): ?>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold">Existing Images:</label>
                                        <div class="existing-images">
                                            <?php foreach (explode(',', $letter['images']) as $img): ?>
                                                <div class="existing-image">
                                                    <img src="<?= base_url($img); ?>" class="img-thumbnail" alt="Existing Image" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mt-4 d-flex justify-content-between">
                                <a href="<?= site_url('request-letters'); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-save me-2"></i> <?= isset($letter) ? 'Update' : 'Create'; ?> Letter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const maxFiles = 5;
        const $imageUpload = $('#imageUpload');
        const $imageCounter = $('#imageCounter');
        const $imagePreviewContainer = $('#imagePreviewContainer');

        $imageUpload.on('change', function() {
            const files = this.files;

            // Check file limit
            if (files.length > maxFiles) {
                alert(`You can upload a maximum of ${maxFiles} images.`);
                $imageUpload.val('');
                $imagePreviewContainer.html('');
                $imageCounter.text(`0/${maxFiles} images selected`);
                return;
            }

            $imagePreviewContainer.html('');

            $.each(files, function(index, file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const $previewDiv = $(`
                        <div class="image-preview me-2 mb-2">
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    `);
                    $imagePreviewContainer.append($previewDiv);
                };
                reader.readAsDataURL(file);
            });

            $imageCounter.text(`${files.length}/${maxFiles} images selected`);
        });

        // Bootstrap validation (unchanged but written in jQuery context)
        (function() {
            'use strict';
            var forms = $('.needs-validation');
            forms.each(function() {
                var form = $(this);
                form.on('submit', function(event) {
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.addClass('was-validated');
                });
            });
        })();
    });
</script>

<?= $this->endSection() ?>