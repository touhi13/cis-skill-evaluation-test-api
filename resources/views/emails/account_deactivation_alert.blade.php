<!DOCTYPE html>
<html>
<head>
    <title>Account Deactivation Alert</title>
</head>
<body>
    <p>Dear {{ $user->name }},</p>
    <p>Your account will be deactivated on {{ $user->activation_expires }}. Please renew your subscription to continue using our services.</p>
    <p>Best regards,</p>
    <p>CIS Tech LTD</p>
</body>
</html>
