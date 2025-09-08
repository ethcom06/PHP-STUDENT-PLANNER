<?php
/* ============================
   STUDENT STUDY PLANNER
   ============================
   - Save this file as studyplanner.php inside htdocs
   - Start Apache & MySQL in XAMPP
   - Open: http://localhost/studyplanner.php
*/

// ---------- Database Connection ----------
$host = "localhost";   // default for XAMPP
$user = "root";        // default username
$pass = "";            // default password
$dbname = "study_planner";

// Create connection
$conn = new mysqli($host, $user, $pass);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ---------- Create Database if not exists ----------
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// ---------- Create Table if not exists ----------
$conn->query("CREATE TABLE IF NOT EXISTS planner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(100) NOT NULL,
    task TEXT NOT NULL,
    due_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// ---------- Handle Form Submission ----------
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $conn->real_escape_string($_POST['subject']);
    $task = $conn->real_escape_string($_POST['task']);
    $due_date = $_POST['due_date'];

    $sql = "INSERT INTO planner (subject, task, due_date) VALUES ('$subject', '$task', '$due_date')";
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Task added successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}

// ---------- Fetch Planner Data ----------
$result = $conn->query("SELECT * FROM planner ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Study Planner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0e0e0e;
            color: white;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px;
        }
        .planner-box {
            background: #1a1a1a;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px purple;
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #5b9dff;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
            margin-bottom: 15px;
        }
        input, textarea {
            background: #2a2a2a;
            color: white;
        }
        button {
            background: #8c52ff;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #6b35cc;
        }
        .task-list {
            margin-top: 20px;
        }
        .task-item {
            background: #262626;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
        }
        .success {
            color: lightgreen;
        }
    </style>
</head>
<body>
    <div class="planner-box">
        <h2>Student Study Planner</h2>
        <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>

        <form method="POST">
            <label>Subject</label>
            <input type="text" name="subject" placeholder="e.g. Mathematics" required>

            <label>Task</label>
            <textarea name="task" placeholder="Describe the study task..." required></textarea>

            <label>Due Date</label>
            <input type="date" name="due_date" required>

            <button type="submit">Add To Planner</button>
        </form>

        <div class="task-list">
            <h3>Upcoming Tasks</h3>
            <?php while($row = $result->fetch_assoc()) { ?>
                <div class="task-item">
                    <strong><?php echo htmlspecialchars($row['subject']); ?></strong> <br>
                    <?php echo htmlspecialchars($row['task']); ?> <br>
                    <small>📅 Due: <?php echo $row['due_date']; ?></small>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>