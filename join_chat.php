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

// Process form submission to enter username
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
        
        // Check if user already exists in the chat
        $checkUserQuery = "SELECT * FROM members WHERE chatcode = '$chatcode' AND username = '$username'";
        $result = $conn->query($checkUserQuery);
        if ($result->num_rows == 0) {
            // Insert user into the members table
            $insertMemberQuery = "INSERT INTO members (chatcode, username) VALUES ('$chatcode', '$username')";
            if ($conn->query($insertMemberQuery) === TRUE) {
                // Redirect to chat room
                header("Location: chat.php?chatcode=$chatcode&username=$username");
                exit();
            } else {
                $error = "Error entering chat: " . $conn->error;
            }
        } else {
            $error = "Username already taken in this chat!";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Chat</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-4">
        <h1 class="text-xl font-semibold mb-4">Enter Your Name</h1>
        <!-- Display Error Message -->
        <?php if (!empty($error)): ?>
            <div class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Enter your name" class="w-full p-2 border rounded-lg mb-4" required>
            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Join Chat</button>
        </form>
    </div>
</body>
</html>
