<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use DateTime;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        helper('date');

        // Configuration
        $startDate = '2025-05-01'; // Start date of the month
        $endDate = '2025-05-31';   // End date of the month
        $employeeIds = range(1, 2); // Employee IDs 1 through 5

        // Status options from your ENUM
        $statusOptions = [
            'Present',
            'Half Day',
            'Absent',
            'Casual Leave',
            'Sick Leave',
            'Work From Home',
            'On Duty / Official Visit',
            'Paid Leave',
            'Unpaid Leave',
            'Compensatory Off',
            'Holiday / Weekend'
        ];

        // Initialize the database
        $db = \Config\Database::connect();
        $attendanceTable = $db->table('attendance');

        // Generate dates for the month
        $begin = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end = $end->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($begin, $interval, $end);

        foreach ($employeeIds as $employeeId) {
            foreach ($dateRange as $date) {
                $dateStr = $date->format('Y-m-d');
                $dayOfWeek = $date->format('w'); // 0 (Sunday) to 6 (Saturday)

                // Initialize record data
                $record = [
                    'employee_id' => $employeeId,
                    'attendance_date' => $dateStr,
                    'in_time' => null,
                    'out_time' => null,
                    'total_work_hours' => null,
                    'status' => 'Present',
                    'remarks' => null
                ];

                // Check for weekend
                if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                    $record['status'] = 'Holiday / Weekend';
                } else {
                    // 15% chance of leave
                    if (rand(1, 100) <= 15) {
                        $leaveTypes = ['Casual Leave', 'Sick Leave', 'Paid Leave', 'Unpaid Leave'];
                        $record['status'] = $leaveTypes[array_rand($leaveTypes)];
                        $record['remarks'] = $this->getRandomRemark($record['status']);
                    } else {
                        // Generate normal working day
                        $record = $this->generateWorkingDay($record, $dateStr);
                    }
                }

                // Insert the record
                $attendanceTable->insert($record);
            }
        }

        echo "Successfully generated attendance records for employees 1-5 from $startDate to $endDate\n";
    }

    protected function generateWorkingDay(array $record, string $dateStr): array
    {
        // 10% chance of half day
        $isHalfDay = rand(1, 10) == 1;

        // Set in time between 8:00 AM and 10:00 AM
        $inHour = rand(8, 9);
        $inMinute = rand(0, 59);
        $record['in_time'] = "$dateStr " . sprintf('%02d:%02d:00', $inHour, $inMinute);

        // Set out time
        if ($isHalfDay) {
            $record['status'] = 'Half Day';
            $outHour = $inHour + 4; // Half day = 4 hours
        } else {
            // Full day = 7 to 9 hours
            $workHours = rand(7, 9);
            $outHour = $inHour + $workHours;
        }

        // Add some random minutes to out time
        $outMinute = rand(0, 59);
        $record['out_time'] = "$dateStr " . sprintf('%02d:%02d:00', $outHour, $outMinute);

        // Calculate total work hours
        $inTime = new DateTime($record['in_time']);
        $outTime = new DateTime($record['out_time']);
        $diff = $inTime->diff($outTime);
        $record['total_work_hours'] = $diff->h + ($diff->i / 60);

        // 10% chance of being late with remarks
        if ($inHour >= 9 && rand(1, 10) == 1) {
            $record['remarks'] = 'Late arrival due to traffic';
        }

        // 5% chance of early departure with remarks
        if (!$isHalfDay && $workHours < 8 && rand(1, 20) == 1) {
            $record['remarks'] = 'Left early with permission';
        }

        return $record;
    }

    protected function getRandomRemark(string $status): string
    {
        $remarks = [
            'Casual Leave' => ['Personal work', 'Family event', 'Vacation'],
            'Sick Leave' => ['Fever', 'Doctor appointment', 'Not feeling well'],
            'Paid Leave' => ['Annual leave', 'Earned leave'],
            'Unpaid Leave' => ['Personal emergency', 'Family matter']
        ];

        if (array_key_exists($status, $remarks)) {
            return $remarks[$status][array_rand($remarks[$status])];
        }

        return '';
    }
}
