<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .search-form {
        display: none;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .search-form.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">EMS</a></li>
                        <li class="breadcrumb-item active">Track Location</li>
                    </ol>
                    <div class="page-title-right">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <button id="toggleSearchBtn" type="button" class="btn btn-outline-primary waves-effect waves-light">
                                <i class="mdi mdi-magnify me-1"></i> Advanced Search
                            </button>
                            <a class="btn btn-outline-secondary waves-effect waves-light" onclick="window.history.back();">
                                <i class="mdi mdi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="search-form <?= isset($txtsearch) ? 'show' : '' ?>">
            <div class="card customer-card">
                <div class="card-body">
                    <form action="">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="txtsearch" class="form-label">Search Employees</label>
                                <input class="form-control" name="txtsearch" type="text" value="<?= isset($searchArray["txtsearch"]) ? esc($searchArray["txtsearch"]) : ''; ?>" placeholder="Search by name, email, or phone...">
                            </div>
                            <?php if (session('user_type') === 'admin'): ?>
                                <div class="col-md-3">
                                    <label for="company_id" class="form-label">Company</label>
                                    <select class="form-select" id="company_id" name="company_id">
                                        <option value="">Select Company</option>
                                        <?php foreach ($companies as $company): ?>
                                            <option value="<?= esc($company['company_id']) ?>"
                                                <?= (isset($searchArray['company_id']) && $searchArray['company_id'] == $company['company_id']) ? 'selected' : '' ?>>
                                                <?= esc($company['company_name']) ?> (<?= esc($company['company_code']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-3">
                                <label for="manager" class="form-label">Reporting Manager</label>
                                <select class="form-select" name="manager" id="manager">
                                    <option value="">Select Reporting Manager</option>
                                    <?php foreach ($managers as $manager): ?>
                                        <option value="<?= esc($manager['employee_id']) ?>"
                                            <?= isset($searchArray['manager']) && $searchArray['manager'] == $manager['employee_id'] ? 'selected' : '' ?>>
                                            <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status" id="imstatus">
                                    <option value="">Select Status</option>
                                    <option value="Active" <?= isset($searchArray['status']) && $searchArray['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= isset($searchArray['status']) && $searchArray['status'] == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                                    <i class="mdi mdi-filter-outline me-1"></i> Apply Filters
                                </button>
                                <a href="<?= site_url('employee-locations'); ?>" class="btn btn-light waves-effect">
                                    <i class="mdi mdi-autorenew me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <?php echo view('admin/_topmessage'); ?>
                    <div class="card-body">
                        <?php if ($pagination["totalRecords"] > 0) { ?>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered dt-responsive w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sl</th>
                                            <th class="text-center">Employee Company</th>
                                            <th class="text-center">Employee Name</th>
                                            <th class="text-center">Employee Email</th>
                                            <th class="text-center">Employee Mobile</th>
                                            <th class="text-center">Employee Designation</th>
                                            <th class="text-center">Employee Manager</th>
                                            <th class="text-center">Last Location At</th>
                                            <th data-sortable="false">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $item) { ?>
                                            <?php
                                            $employeeId = $item->employee_id;

                                            // Inject location data from $locations into $item
                                            if (isset($locations[$employeeId])) {
                                                $item->latitude = $locations[$employeeId]['latitude'];
                                                $item->longitude = $locations[$employeeId]['longitude'];
                                            } else {
                                                $item->latitude = null;
                                                $item->longitude = null;
                                            }

                                            if (!empty($item->latitude) && !empty($item->longitude)) {
                                                $item->locality = getLocalityFromLatLng($item->latitude, $item->longitude);
                                            } else {
                                                $item->locality = 'N/A';
                                            }

                                            ?>

                                            <tr id="employee-row-<?= $item->employee_id ?>">
                                                <td class="text-center"><?= ++$startLimit; ?></td>
                                                <td class="text-center"><?= $item->company_name ?? 'N/A'; ?></td>
                                                <td class="text-center"><?= esc($item->first_name . ' ' . $item->last_name) ?></td>
                                                <td class="text-center"><?= esc($item->email); ?></td>
                                                <td class="text-center"><?= esc($item->phone); ?></td>
                                                <td class="text-center"><?= esc($item->designation); ?></td>
                                                <td class="text-center"><?php echo $item->manager_name ?? "Not Assigned"; ?></td>
                                                <td class="text-center"><?= esc($item->locality ?? 'N/A'); ?></td>

                                                <td class="text-center">
                                                    <!-- View Button -->
                                                    <button type="button" class="btn btn-primary m-1 view-location" data-lat="<?= $item->latitude ?>" data-lng="<?= $item->longitude ?>" data-name="<?= $item->first_name . ' ' . $item->last_name ?>" title="View Location">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                    </button>

                                                    <!-- View Details Button -->
                                                    <a href="<?= base_url('employee-locations/view/' . $item->employee_id) ?>" class="btn btn-info m-1" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($pagination['totalRecords']) { ?>
                                <br>
                                <?= view('admin/_paging', ['paginate' => $pagination, 'siteurl' => $action, 'varExtra' => $searchArray]); ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= view('admin/_noresult'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Location Modal (Google Maps) -->
<div class="modal fade" id="viewLocationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="locationMap" style="height: 400px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#datatable').DataTable({
            dom: 'Bfrtip',
            searching: false,
            paging: false,
            info: false,
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        $('#toggleSearchBtn').click(function() {
            $('.search-form').toggleClass('show');
            localStorage.setItem('isSearchFormVisible', $('.search-form').hasClass('show'));
            localStorage.setItem('searchFormVisible', new Date().getTime());

            if ($('.search-form').hasClass('show')) {
                $(this).html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
            } else {
                $(this).html('<i class="mdi mdi-magnify me-1"></i> Advanced Search').removeClass('btn-primary').addClass('btn-outline-primary');
            }
        });

        const currentTime = new Date().getTime();
        const visibilityDuration = 10 * 60 * 1000;
        const isFormVisible = localStorage.getItem('isSearchFormVisible');
        const formVisibleTimestamp = localStorage.getItem('searchFormVisible');

        if (isFormVisible === 'true' && formVisibleTimestamp && (currentTime - formVisibleTimestamp < visibilityDuration)) {
            $('.search-form').addClass('show');
            $('#toggleSearchBtn').html('<i class="mdi mdi-close me-1"></i> Close Search').removeClass('btn-outline-primary').addClass('btn-primary');
        }

        // Google Maps Variables
        let map = null;
        let marker = null;

        // Load Google Maps API dynamically
        function loadGoogleMaps() {
            return new Promise((resolve, reject) => {
                if (typeof google === 'object' && typeof google.maps === 'object') {
                    resolve();
                } else {
                    const script = document.createElement('script');
                    script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyB5JXg6FAxRZ5Asf7Yj8isqEay5ogDxkzc&libraries=places`;
                    script.onload = resolve;
                    script.onerror = reject;
                    document.head.appendChild(script);
                }
            });
        }

        // View Location Button Click
        $('.view-location').on('click', async function() {
            const lat = parseFloat($(this).data('lat'));
            const lng = parseFloat($(this).data('lng'));
            const name = $(this).data('name');

            // Show modal immediately with employee name
            $('#viewLocationModal .modal-title').html(`Location of ${name}`);
            $('#viewLocationModal').modal('show');

            // Clear previous map or messages
            $('#locationMap').empty();

            if (isNaN(lat) || isNaN(lng)) {
                // Show error message in the modal
                $('#locationMap').html(`
                    <div class="alert alert-warning m-3 d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Location Unavailable</strong>
                            <p class="mb-0">No valid location data available for ${name}</p>
                        </div>
                    </div>
                `);
                return;
            }

            // Show loading state
            $('#locationMap').html(`
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading map...</p>
                </div>
            `);

            try {
                // Load Google Maps if not already loaded
                await loadGoogleMaps();

                // Initialize map
                const mapElement = document.getElementById('locationMap');
                const location = new google.maps.LatLng(lat, lng);

                // Clear existing map if it exists
                if (map) {
                    map.setCenter(location);
                    if (marker) {
                        marker.setPosition(location);
                    } else {
                        marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            title: `${name}'s Location`,
                            animation: google.maps.Animation.DROP
                        });
                    }
                } else {
                    // Create new map
                    map = new google.maps.Map(mapElement, {
                        center: location,
                        zoom: 15,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        streetViewControl: true,
                        mapTypeControl: true,
                        fullscreenControl: true
                    });

                    marker = new google.maps.Marker({
                        position: location,
                        map: map,
                        title: `${name}'s Location`,
                        animation: google.maps.Animation.DROP
                    });
                }

                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                            <div style="padding: 10px; min-width: 200px;">
                                <h6 style="margin-bottom: 5px; color: #333;">${name}</h6>
                                <p style="margin: 0; font-size: 13px;">
                                    <i class="fas fa-map-marker-alt text-danger"></i> 
                                    Latitude: ${lat.toFixed(6)}
                                </p>
                                <p style="margin: 0; font-size: 13px;">
                                    <i class="fas fa-map-marker-alt text-danger"></i> 
                                    Longitude: ${lng.toFixed(6)}
                                </p>
                            </div>
                        `
                });

                // Open info window by default
                infoWindow.open(map, marker);

                // Add click listener to marker
                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                // Fit map to marker with padding
                map.panTo(marker.getPosition());

            } catch (error) {
                console.error('Error loading map:', error);
                $('#locationMap').html(`
                    <div class="alert alert-danger m-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>Map Loading Error</strong>
                                <p class="mb-0">Failed to load map for ${name}</p>
                                ${error.message ? `<small class="text-muted">${error.message}</small>` : ''}
                            </div>
                        </div>
                    </div>
                `);
            }
        });

        // Clean up when modal closes
        $('#viewLocationModal').on('hidden.bs.modal', function() {
            if (marker) marker.setMap(null);
            $('#locationMap').empty(); // Clear the map div
        });

        // ========================
        // PAGE RELOAD AFTER MODAL CLOSE
        // ========================
        $('[data-dismiss="modal"]').on('click', function() {
            setTimeout(function() {
                location.reload();
            }, 300);
        });

        $('#company_id').on('change', function() {
            var companyId = $(this).val();

            if (companyId) {
                $.ajax({
                    url: '<?= site_url("companies/get-managers") ?>',
                    type: 'POST',
                    data: {
                        company_id: companyId
                    },
                    dataType: 'json',
                    success: function(response) {
                        var managerSelect = $('#manager');
                        managerSelect.empty();
                        managerSelect.append('<option value="">Select Reporting Manager</option>');

                        $.each(response, function(index, emp) {
                            managerSelect.append(
                                '<option value="' + emp.employee_id + '">' +
                                emp.first_name + ' ' + emp.last_name +
                                ' (' + emp.employee_code + ')' +
                                '</option>'
                            );
                        });
                    },
                    error: function() {
                        alert('Error fetching managers.');
                    }
                });
            }
        });
    });
</script>

<?= $this->endSection() ?>