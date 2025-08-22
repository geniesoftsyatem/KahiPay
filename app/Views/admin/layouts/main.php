<!doctype html>
<html lang="en">
<?php
$session = session();
/* 
Include the header view file
This will load the content from header.php and insert it at this point
*/
echo $this->include('admin/layouts/header');
?>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper"> <!-- Main layout wrapper -->
        <?= view('admin/layouts/topmenu'); ?>

        <?= view('admin/layouts/leftpanel'); ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content"> <!-- Main content area -->
            <?php
            /* 
            Render the 'content' section
            This is where the specific content from the extending view (e.g., home.php) will be inserted
            */
            echo $this->renderSection('content');
            ?>
        </div><!-- end main content -->

    </div><!-- END layout-wrapper -->
    <script>
        function updateDateTime() {
            var indiaTimezoneOffset = 5.5;
            var currentDate = new Date();
            var utc = currentDate.getTime() + (currentDate.getTimezoneOffset() * 60000);
            var indiaTime = new Date(utc + (3600000 * indiaTimezoneOffset));

            var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            var dayOfWeek = daysOfWeek[indiaTime.getDay()];
            var month = months[indiaTime.getMonth()];
            var day = indiaTime.getDate();
            var year = indiaTime.getFullYear();
            var hours = indiaTime.getHours();
            var minutes = indiaTime.getMinutes();
            var seconds = indiaTime.getSeconds();
            var ampm = hours >= 12 ? 'PM' : 'AM';

            hours = hours % 12;
            hours = hours ? hours : 12;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            var formattedDate = dayOfWeek + ' , ' + month + ' ' + day + ' , ' + year;
            var formattedTime = hours + ' : ' + minutes + ' : ' + seconds + ' ' + ampm;

            document.getElementById('day-yearDisplay').innerHTML = formattedDate;
            document.getElementById('timeDisplay').innerHTML = formattedTime;
        }

        // Update the time every second
        setInterval(updateDateTime, 1000);
        // Initial update
        updateDateTime();
    </script>
    <?php
    /* 
    Include the footer view file
    This will load the content from footer.php and insert it at this point
    */
    echo $this->include('admin/layouts/footer');
    ?>
</body>

</html>