<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-logo {
            max-height: 60px;
        }

        .salary-slip-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }

        .employee-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .employee-info td {
            padding: 5px;
            border: 1px solid #ddd;
        }

        .employee-info .label {
            font-weight: bold;
            background-color: #f5f5f5;
            width: 30%;
        }

        .earnings-deductions {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .earnings-deductions th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }

        .earnings-deductions td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .amount {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
        }

        .signature {
            margin-top: 40px;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            display: inline-block;
            margin: 0 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <?php if (file_exists($company_logo)): ?>
            <img src="<?= $company_logo ?>" class="company-logo" alt="Company Logo">
        <?php endif; ?>
        <h1><?= $company_name ?></h1>
        <p><?= $company_address ?></p>
        <?php if (!empty($company_gst_number)): ?>
            <p><strong>GSTIN:</strong> <?= $company_gst_number ?></p>
        <?php endif; ?>
        <div class="salary-slip-title">SALARY SLIP</div>
        <p>For the month of <?= $month_year ?></p>
    </div>

    <table class="employee-info">
        <tr>
            <td class="label">Employee ID</td>
            <td><?= $employee_id ?></td>
            <td class="label">Employee Name</td>
            <td><?= $employee_name ?></td>
        </tr>
        <tr>
            <td class="label">Designation</td>
            <td><?= $designation ?></td>
            <td class="label">Department</td>
            <td><?= $department ?></td>
        </tr>
        <!-- Remove bank account and PAN if not in your employee table -->
    </table>

    <table class="earnings-deductions">
        <thead>
            <tr>
                <th>Earnings</th>
                <th class="amount">Amount (₹)</th>
                <th>Deductions</th>
                <th class="amount">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="amount"><?= number_format($basic_salary, 2) ?></td>
                <td>All Deductions</td>
                <td class="amount"><?= number_format($deductions, 2) ?></td>
            </tr>
            <tr>
                <td>Allowances</td>
                <td class="amount"><?= number_format($allowances, 2) ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td>Total Earnings</td>
                <td class="amount"><?= number_format(($basic_salary + $allowances), 2) ?></td>
                <td>Total Deductions</td>
                <td class="amount"><?= number_format($deductions, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div style="text-align: right; font-size: 14px; font-weight: bold;">
        Net Salary: ₹<?= number_format($net_salary, 2) ?>
    </div>

    <div class="footer">
        <p>This is a system generated salary slip and does not require signature.</p>
        <div class="signature">
            <span>Employee Signature</span>
            <span class="signature-line"></span>
            <span>Authorized Signatory</span>
            <span class="signature-line"></span>
        </div>
    </div>
</body>

</html>