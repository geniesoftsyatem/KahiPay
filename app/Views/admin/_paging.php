<?php if ($paginate['hasPagination']) { ?>
    <div class="row">
        <div class="col-md-6">
            <div class="dataTables_info">
                Showing <?php echo $paginate['firstIndex']; ?> to <?php echo $paginate['lastIndex']; ?> of <?php echo $paginate['totalRecords']; ?> entries
            </div>
        </div>
        <div class="col-md-6 text-right">
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination justify-content-end">
                    <?php
                    $extraVars = '';
                    if (is_array($varExtra)) {
                        foreach ($varExtra as $key => $value) {
                            $extraVars .= '&' . $key . '=' . $value;
                        }
                    }

                    // Previous Button
                    if ($paginate['currentPage'] == 1) {
                        echo "<li class='paginate_button page-item disabled'><a href='javascript:void(0)' title='Previous' class='page-link'>&laquo; Previous</a></li>";
                    } else {
                        echo "<li class='paginate_button page-item'><a href='" . site_url($siteurl) . "?page=" . $paginate['previousPage'] . $extraVars . "' title='Previous' class='page-link'>&laquo; Previous</a></li>";
                    }

                    // Number of links to show
                    $linksToShow = 3;
                    $currentPage = $paginate['currentPage'];
                    $totalPages = $paginate['lastPage'];

                    // Calculate start and end pages
                    $startPage = max(1, $currentPage - floor($linksToShow / 2));
                    $endPage = min($totalPages, $startPage + $linksToShow - 1);

                    // Adjust start page if it goes below 1
                    if ($endPage - $startPage + 1 < $linksToShow) {
                        $startPage = max(1, $endPage - $linksToShow + 1);
                    }

                    // Show page links
                    for ($page = $startPage; $page <= $endPage; $page++) {
                        echo $page == $currentPage
                            ? "<li class='paginate_button page-item active'><a href='#' class='page-link'>" . $page . "</a></li>"
                            : "<li class='paginate_button page-item'><a href='" . site_url($siteurl) . "?page=" . $page . $extraVars . "' class='page-link'>" . $page . "</a></li>";
                    }

                    // Next Button
                    if ($paginate['currentPage'] != $paginate['lastPage']) {
                        echo "<li class='paginate_button page-item'><a href='" . site_url($siteurl) . "?page=" . $paginate['nextPage'] . $extraVars . "' title='Next' class='page-link'>Next &raquo;</a></li>";
                    } else {
                        echo "<li class='paginate_button page-item disabled'><a href='javascript:void(0);' title='Next' class='page-link'>Next &raquo;</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>
