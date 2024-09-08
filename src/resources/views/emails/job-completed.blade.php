<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Completed</title>
</head>
<body>
    <h1>Hello {{ $customer->name }},</h1>
    <p>Your vehicle job (Job Number: {{ $vehicleJob->id }}) has been successfully completed.</p>
    <p>Thank you for choosing our services!</p>
    <p>Regards,<br>Auto Shine Car Service</p>
</body>
</html>
