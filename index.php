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
$successMessage = '';

// Handle form submission to join a chat
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['join_chatcode'])) {
        $chatcode = $_POST['join_chatcode'];
        $checkChatcodeQuery = "SELECT * FROM chatcode WHERE code = '$chatcode'";
        $result = $conn->query($checkChatcodeQuery);
        if ($result->num_rows > 0) {
            // Chat code exists, redirect to chat page
            header("Location: join_chat.php?chatcode=$chatcode");
            exit();
        } else {
            $error = "Invalid chat code!";
        }
    }

    // Handle form submission to start a new chat
    if (isset($_POST['start_chatcode'])) {
        $chatcode = generateChatCode();
        $insertChatcodeQuery = "INSERT INTO chatcode (id, code) VALUES ('$chatcode', '$chatcode')";
        if ($conn->query($insertChatcodeQuery) === TRUE) {
            $successMessage = "New chat created! Chat code: $chatcode";
        } else {
            $error = "Error creating chat: " . $conn->error;
        }
    }
}

function generateChatCode() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $chatcode = '';
    for ($i = 0; $i < 6; $i++) {
        $chatcode .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $chatcode;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join or Start a Chat</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col h-screen">
    <div class="flex flex-col items-center justify-center flex-1 p-4">
        <h1 class="text-3xl font-bold mb-2">Anonymous Chat</h1>
        <p class="text-gray-600 mb-8">Connect without revealing your identity.</p>

        <!-- Display Error or Success Message -->
        <?php if (!empty($error)): ?>
            <div class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($successMessage)): ?>
            <div class="text-green-500 mb-4"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- Start New Chat Form -->
        <form method="POST" class="w-full max-w-xs space-y-4">
            <button type="submit" name="start_chatcode" class="w-full bg-blue-500 text-white py-2 rounded-lg flex items-center justify-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Start a New Chat</span>
            </button>
        </form>

        <!-- Join Chat Form -->
        <form method="POST" class="w-full max-w-xs space-y-4">
            <input type="text" name="join_chatcode" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Room Code" required>
            <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg flex items-center justify-center space-x-2">
                <i class="fas fa-sign-in-alt"></i>
                <span>Join Chat</span>
            </button>
        </form>
    </div>
</body>
</html>

