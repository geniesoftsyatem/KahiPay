<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to [Your Salon Name]!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        .container {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff6f61;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>

</head>

<body>
    <h1>Welcome to Stylo!</h1>
    <p>Dear <?= $first_name ?>,</p>
    <p>Welcome to Stylo! We're excited to have you as a part of our community.</p>
    <p>At Stylo, we're dedicated to providing top-notch services and ensuring you leave feeling rejuvenated and satisfied.</p>
    <p>Feel free to explore our range of services and schedule an appointment at your convenience.</p>
    <p>If you have any questions or need assistance, don't hesitate to reach out to us.</p>
    <p>We look forward to seeing you soon!</p>
    <p>Best regards,<br> The Stylo Team</p>
    <p><a href="#" class="btn">Book Appointment</a></p>
</body>

</html>