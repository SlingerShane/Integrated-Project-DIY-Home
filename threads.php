<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'forum'); // Adjust your DB credentials

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle new reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $reply_message = $_POST['reply_message'];
    $thread_id = $_POST['thread_id'];
    $author = $_POST['author'];

    // Insert reply into database
    $stmt = $conn->prepare("INSERT INTO replies (thread_id, author, message, date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $thread_id, $author, $reply_message);

    if ($stmt->execute()) {
        // Redirect to the thread page to see the new reply
        header("Location: thread.php?thread_id=" . $thread_id); // Ensure this points to the correct thread
        exit();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch thread details
if (isset($_GET['thread_id'])) {
    $thread_id = $_GET['thread_id'];

    // Fetch thread
    $thread_stmt = $conn->prepare("SELECT title, author, message, date FROM threads WHERE id = ?");
    $thread_stmt->bind_param("i", $thread_id);
    $thread_stmt->execute();
    $thread_stmt->store_result();
    $thread_stmt->bind_result($title, $author, $message, $date);
    $thread_stmt->fetch();
    
    if ($thread_stmt->num_rows === 0) {
        echo "<p>Thread not found.</p>";
        exit();
    }
    $thread_stmt->close();

    // Fetch replies
    $reply_stmt = $conn->prepare("SELECT author, message, date FROM replies WHERE thread_id = ? ORDER BY date ASC");
    $reply_stmt->bind_param("i", $thread_id);
    $reply_stmt->execute();
    $reply_stmt->store_result();
    $reply_stmt->bind_result($reply_author, $reply_message, $reply_date);
    
    // HTML Structure
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Thread: $title | DIY Haven</title>
        <link rel='stylesheet' href='css/styles.css'>
    </head>
    <body>
        <header>
            <h1>DIY Haven Community Forum</h1>
        </header>
        <div class='container'>
            <h2>" . htmlspecialchars($title) . "</h2>
            <p><strong>By: " . htmlspecialchars($author) . "</strong> | Posted on: " . htmlspecialchars($date) . "</p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
            <h3>Replies</h3>";
    
    if ($reply_stmt->num_rows > 0) {
        while ($reply_stmt->fetch()) {
            echo "<div class='reply'>
                <p><strong>" . htmlspecialchars($reply_author) . "</strong> | " . htmlspecialchars($reply_date) . "</p>
                <p>" . nl2br(htmlspecialchars($reply_message)) . "</p>
            </div>";
        }
    } else {
        echo "<p>No replies yet. Be the first to reply!</p>";
    }

    $reply_stmt->close();

    // Reply form
    echo "<div class='reply-form'>
            <h3>Post a Reply</h3>
            <form method='POST' action=''>
                <input type='hidden' name='thread_id' value='$thread_id'>
                <input type='hidden' name='author' value='You'> <!-- Placeholder for actual user -->
                <textarea name='reply_message' placeholder='Enter your reply here...' rows='4' required></textarea>
                <button type='submit'>Post Reply</button>
            </form>
        </div>
        </div>
    </body>
    </html>";
} else {
    echo "<p>Invalid thread ID.</p>";
}

$conn->close();
?>
