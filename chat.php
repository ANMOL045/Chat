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

$error = '';
$chatcode = isset($_GET['chatcode']) ? $_GET['chatcode'] : '';
$username = isset($_GET['username']) ? $_GET['username'] : '';

// Process form submission to send a message
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['message'])) {
        $message = $_POST['message'];
        
        // Insert message into the messages table
        $insertMessageQuery = "INSERT INTO messages (chatcode, username, message) VALUES ('$chatcode', '$username', '$message')";
        if ($conn->query($insertMessageQuery) === TRUE) {
            // No need for redirection, let AJAX handle it
        } else {
            $error = "Error sending message: " . $conn->error;
        }
    }
}

// Get all messages for this chat
$getMessagesQuery = "SELECT * FROM messages WHERE chatcode = '$chatcode' ORDER BY timestamp ASC";
$messagesResult = $conn->query($getMessagesQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-4">
        <h1 class="text-2xl font-semibold mb-4">Chat Room: <?= htmlspecialchars($chatcode) ?></h1>
        
        <!-- Display Error Message -->
        <?php if (!empty($error)): ?>
            <div class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Messages Display -->
        <div id="messages" class="overflow-y-auto max-h-60 mb-4">
            <?php while ($row = $messagesResult->fetch_assoc()): ?>
                <div class="mb-2">
                    <strong><?= htmlspecialchars($row['username']) ?>:</strong> <?= htmlspecialchars($row['message']) ?>
                    <div class="text-xs text-gray-500"><?= $row['timestamp'] ?></div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Send Message Form -->
        <form method="POST" id="chatForm">
            <input type="text" name="message" id="message" class="w-full p-2 border rounded-lg mb-4" placeholder="Type a message..." required>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Send Message</button>
        </form>
    </div>

    <script>
        // Function to fetch new messages every 3 seconds
        function fetchMessages() {
            const chatcode = "<?= $chatcode ?>";  // Chat code from PHP
            const username = "<?= $username ?>";  // Username from PHP

            // Fetch messages from the get_messages.php file
            fetch('get_messages.php?chatcode=' + chatcode)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('messages').innerHTML = data;
                })
                .catch(error => console.error('Error fetching messages:', error));
        }

        // Refresh messages every 3 seconds
        setInterval(fetchMessages, 3000); // 3000ms = 3 seconds

        // Send message using AJAX without reloading the page
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();  // Prevent the form from submitting normally

            const message = document.getElementById('message').value;
            const chatcode = "<?= $chatcode ?>";
            const username = "<?= $username ?>";

            // Send message to the server using fetch (AJAX)
            fetch('chat.php?chatcode=' + chatcode + '&username=' + username, {
                method: 'POST',
                body: new URLSearchParams({
                    'message': message
                })
            })
            .then(response => response.text())
            .then(data => {
                // Clear the message input after sending
                document.getElementById('message').value = '';
            })
            .catch(error => console.error('Error sending message:', error));
        });
    </script>
</body>
</html>
