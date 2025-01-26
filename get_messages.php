<?php
// Database connection
$servername = "";
$username = "";
$password = "";
$dbname = "";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$chatcode = isset($_GET['chatcode']) ? $_GET['chatcode'] : '';

// Get all messages for this chat
$getMessagesQuery = "SELECT * FROM messages WHERE chatcode = '$chatcode' ORDER BY timestamp ASC";
$messagesResult = $conn->query($getMessagesQuery);

// Output the messages in HTML format
while ($row = $messagesResult->fetch_assoc()) {
    echo '<div class="mb-2">';
    echo '<strong>' . htmlspecialchars($row['username']) . ':</strong> ' . htmlspecialchars($row['message']);
    echo '<div class="text-xs text-gray-500">' . $row['timestamp'] . '</div>';
    echo '</div>';
}

$conn->close();
?>
