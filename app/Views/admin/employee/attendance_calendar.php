<?php
// Get number of days in month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
// Get first day of month (0=Sunday, 6=Saturday)
$firstDay = date('w', strtotime("$year-$month-01"));

// CSS class mapping for status
$statusClasses = [
    'present' => 'status-present',
    'half day' => 'status-halfday',
    'absent' => 'status-absent',
    'casual leave' => 'status-leave',
    'sick leave' => 'status-leave',
    'work from home' => 'status-wfh',
    'on duty / official visit' => 'status-duty',
    'paid leave' => 'status-leave',
    'unpaid leave' => 'status-leave',
    'compensatory off' => 'status-leave',
    'holiday / weekend' => 'status-holiday'
];

// Use controller-summarized data (already keyed by day number)
$attendanceMap = $attendanceData;
?>

<style>
    .attendance-calendar td {
        width: 120px;
        height: 100px;
        vertical-align: top;
        padding: 5px;
        text-align: center;
    }

    .calendar-cell {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .day-number {
        font-weight: bold;
        margin-bottom: 3px;
    }

    .attendance-status {
        font-size: 12px;
        font-weight: 600;
    }

    .attendance-time small {
        display: block;
        font-size: 11px;
        color: #555;
    }

    /* Colors */
    .status-present {
        background-color: #d4edda;
    }

    /* green */
    .status-halfday {
        background-color: #fff3cd;
    }

    /* yellow */
    .status-absent {
        background-color: #f8d7da;
    }

    /* red */
    .status-leave {
        background-color: #cce5ff;
    }

    /* blue */
    .status-wfh {
        background-color: #e2e3e5;
    }

    /* gray */
    .status-duty {
        background-color: #d1ecf1;
    }

    /* cyan */
    .status-holiday {
        background-color: #fefefe;
    }

    /* white */
</style>

<table class="attendance-calendar table-bordered">
    <thead>
        <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            // Fill empty cells before first day
            for ($i = 0; $i < $firstDay; $i++) {
                echo '<td>&nbsp;</td>';
            }

            $dayCount = 1;
            $today = date("Y-m-d");

            while ($dayCount <= $daysInMonth) {
                // New row each week
                if (($dayCount + $firstDay - 1) % 7 == 0 && $dayCount != 1) {
                    echo '</tr><tr>';
                }

                $currentDate = "$year-$month-" . str_pad($dayCount, 2, '0', STR_PAD_LEFT);
                $isFuture = strtotime($currentDate) > strtotime($today);

                $attendance = $attendanceMap[$dayCount] ?? null;

                // If it's a future date → don't mark absent
                if ($isFuture) {
                    $status = null;
                } else {
                    $status = $attendance ? strtolower($attendance['status']) : 'absent';
                }

                $statusClass = $status ? ($statusClasses[$status] ?? '') : '';

                echo '<td class="' . $statusClass . '">';
                echo '<div class="calendar-cell">';
                echo '<span class="day-number">' . $dayCount . '</span>';

                if ($status) {
                    // Display status
                    $statusDisplayMap = [
                        'present' => 'Present',
                        'half day' => 'Half Day',
                        'absent' => 'Absent',
                        'casual leave' => 'CL',
                        'sick leave' => 'SL',
                        'work from home' => 'WFH',
                        'on duty / official visit' => 'Duty',
                        'paid leave' => 'PL',
                        'unpaid leave' => 'UL',
                        'compensatory off' => 'CO',
                        'holiday / weekend' => 'Holiday'
                    ];

                    $displayStatus = $statusDisplayMap[$status] ?? ucfirst($status);
                    echo '<div class="attendance-status">' . $displayStatus . '</div>';

                    // Punch In / Out / Hours
                    if ($attendance) {
                        echo '<div class="attendance-time">';
                        echo '<small>In: ' . ($attendance['first_in'] ? date('h:i A', strtotime($attendance['first_in'])) : '-') . '</small><br>';
                        echo '<small>Out: ' . ($attendance['last_out'] ? date('h:i A', strtotime($attendance['last_out'])) : '-') . '</small><br>';
                        echo '<small>Hrs: ' . number_format($attendance['total_hours'], 2) . '</small>';
                        echo '</div>';
                    }
                }

                echo '</div>';
                echo '</td>';

                $dayCount++;
            }

            // Fill empty cells after month end
            while (($dayCount + $firstDay - 1) % 7 != 0) {
                echo '<td>&nbsp;</td>';
                $dayCount++;
            }
            ?>
        </tr>
    </tbody>
</table>