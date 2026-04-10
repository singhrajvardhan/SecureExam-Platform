<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            padding: 20px;
        }

        .nav-card {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
        }

        .logout {
            margin-top: 20px;
            text-align: center;
        }

        .logout a {
            color: red;
        }
    </style>
</head>
<body>

<header>
    <h2>Teacher Dashboard</h2>
</header>

<div class="container">
    <div class="nav-card">

        <div class="card">
            <a href="login.php">Login</a>
        </div>

        <div class="card">
            <a href="dashboard.php">Main Dashboard</a>
        </div>

        <div class="card">
            <a href="get_question.php">Manage Questions</a>
        </div>

        <div class="card">
            <a href="edit_student.php">Edit Student</a>
        </div>

        <div class="card">
            <a href="student_results.php">Student Results</a>
        </div>

        <div class="card">
            <a href="view_result_details.php">Result Details</a>
        </div>

        <div class="card">
            <a href="camera_logs.php">Camera Logs</a>
        </div>

    </div>

    <div class="logout">
        <a href="../logout.php">Logout</a>
    </div>
</div>

</body>
</html>