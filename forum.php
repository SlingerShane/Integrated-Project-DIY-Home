<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Forum | DIY Haven</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Your CSS styles remain here */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        header {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 15px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            margin: 0;
            font-size: 2em;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-size: 1.75em;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
            margin-bottom: 20px;
            color: #3498db;
        }
        .thread-card {
            background-color: #ecf0f1;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .thread-card h3 {
            margin: 0 0 10px;
            color: #2c3e50;
            font-size: 1.5em;
        }
        .thread-card p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #7f8c8d;
        }
        .reply-button {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: inline-block;
            margin-top: 10px;
        }
        .reply-button:hover {
            background-color: #2980b9;
        }
        .new-thread {
            margin-top: 40px;
            padding: 20px;
            background-color: #ecf0f1;
            border-radius: 8px;
        }
        form input, form textarea, form button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 1em;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        form input:focus, form textarea:focus {
            border-color: #3498db;
        }
        form button {
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #2980b9;
        }
        .back-button {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #c0392b;
        }
        footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
        <h1>DIY Haven Community Forum</h1>
    </header>

    <div class="container">
        <a href="index.html" class="back-button">Back to Home</a> <!-- Updated link -->

        <h2>Discussed Topics</h2>
        
        <?php 
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'forum'); // Adjust your DB credentials

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Handle new thread submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['message'])) {
            $title = $_POST['title'];
            $message = $_POST['message'];
            $author = $_POST['author'];

            // Insert new thread into database
            $stmt = $conn->prepare("INSERT INTO threads (title, author, message, date) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $title, $author, $message);

            if ($stmt->execute()) {
                // Redirect to the same page to see the new thread
                header("Location: forum.php"); // Updated link
                exit();
            } else {
                echo "<p>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }

        // Fetch threads from database
        $sql = "SELECT id, title, author, message, date FROM threads ORDER BY date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="thread-card">';
                echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                echo '<p><strong>By: ' . htmlspecialchars($row['author']) . '</strong> | Posted on: ' . htmlspecialchars($row['date']) . '</p>';
                echo '<p>' . nl2br(htmlspecialchars($row['message'])) . '</p>';
                echo '<a href="comments.php?thread_id=' . $row['id'] . '" class="reply-button">Reply</a>'; // Assuming this is where you want to link to comments
                echo '</div>';
            }
        } else {
            echo '<p>No threads available. Be the first to start a discussion!</p>';
        }

        $conn->close();
        ?>

        <div class="new-thread">
            <h3>Start a New Topic</h3>
            <form method="POST" action="">
                <input type="text" name="title" placeholder="Enter thread title" required>
                <textarea name="message" placeholder="Enter your message here..." rows="4" required></textarea>
                <input type="hidden" name="author" value="You"> <!-- Placeholder for actual user -->
                <button type="submit">Post Thread</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 DIY Haven | Connect and Share Ideas</p>
    </footer>
</body>
</html>
