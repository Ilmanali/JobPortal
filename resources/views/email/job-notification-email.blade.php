<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Job Notification Email</title>
</head>

<body>
    <h1>Hello {{ $mailData['employer']->name ?? 'Employer' }}</h1>
    <p>Job title: {{ $mailData['job']->title ?? 'Job Title' }}</p>

    <p>Employee Details</p>
    <p>Name: {{ $mailData['user']->name ?? 'Employee Name' }}</p>
    <p>Email: {{ $mailData['user']->email ?? 'Employee Email' }}</p>
    <p>Mobile: {{ $mailData['user']->mobile ?? 'Employee Mobile' }}</p>
</body>

</html>
