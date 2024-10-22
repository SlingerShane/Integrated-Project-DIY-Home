<?php
// Database connection
$host = 'localhost';
$db = 'forum';
$user = 'root'; // Default XAMPP username
$pass = ''; // Default XAMPP password is empty

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request to add a new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $thread_id = $_POST['thread_id'];
    $author = $_POST['author'];
    $comment = $_POST['comment'];
    $date = date('Y-m-d H:i:s'); // Current date and time

    // Insert comment into database
    $stmt = $conn->prepare("INSERT INTO comments (thread_id, author, comment, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $thread_id, $author, $comment, $date);

    if ($stmt->execute()) {
        // Redirect back to the thread page
        header("Location: thread.php?thread_id=" . $thread_id); // Change to the appropriate thread view page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch the thread ID from GET request
if (isset($_GET['thread_id'])) {
    $thread_id = $_GET['thread_id'];
    
    // Display the comment form
    echo '<h3>Leave a Comment</h3>';
    echo '<form method="POST" action="comments.php">
            <input type="hidden" name="thread_id" value="' . htmlspecialchars($thread_id) . '">
            <input type="text" name="author" placeholder="Your name" required>
            <textarea name="comment" placeholder="Write your comment here..." rows="4" required></textarea>
            <button type="submit">Post Comment</button>
          </form>';
    
    // Fetch and display existing comments
    $comments_query = $conn->prepare("SELECT author, comment, date FROM comments WHERE thread_id = ? ORDER BY date ASC");
    $comments_query->bind_param("i", $thread_id);
    $comments_query->execute();
    $result = $comments_query->get_result();

    if ($result->num_rows > 0) {
        echo '<h4>Comments:</h4>';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="comment">';
            echo '<strong>' . htmlspecialchars($row['author']) . '</strong> <span class="comment-date">on ' . htmlspecialchars($row['date']) . ':</span>';
            echo '<p>' . nl2br(htmlspecialchars($row['comment'])) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No comments yet. Be the first to comment!</p>';
    }

    $comments_query->close();
}

$conn->close();
?>

<style>
    .comment {
        background-color: #f9f9f9;
        border-left: 4px solid #3498db;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .comment strong {
        color: #2c3e50;
    }
    .comment p {
        margin: 5px 0 0;
        color: #555;
    }
    .comment-date {
        font-size: 0.9em;
        color: #888;
    }
</style>
