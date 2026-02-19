<?php
require_once 'config.php';

$error = '';
$success = '';
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Get event details
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: events.php');
    exit();
}

// Generate CSRF token for form
$csrf_token = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    verifyCsrfToken();
    
    $team_name = sanitizeInput($_POST['team_name']);
    $leader_name = sanitizeInput($_POST['leader_name']);
    $leader_email = sanitizeInput($_POST['leader_email']);
    $leader_phone = sanitizeInput($_POST['leader_phone']);
    $team_size = intval($_POST['team_size']);
    $college_name = sanitizeInput($_POST['college_name']);
    
    if (empty($team_name) || empty($leader_name) || empty($leader_email) || empty($leader_phone) || empty($college_name)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($leader_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $insertStmt = $conn->prepare("INSERT INTO registrations (event_id, team_name, leader_name, leader_email, leader_phone, team_size, college_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("issssss", $eventId, $team_name, $leader_name, $leader_email, $leader_phone, $team_size, $college_name);
        
        if ($insertStmt->execute()) {
            $success = 'Registration successful! We will contact you soon.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
        
        $insertStmt->close();
    }
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for <?php echo htmlspecialchars($event['title']); ?> | InnovateX</title>
    <link rel="icon" type="image/svg+xml" href="public/favicon.svg" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        zentry: ["zentry", "sans-serif"],
                        general: ["general", "sans-serif"],
                        "robert-medium": ["robert-medium", "sans-serif"],
                        "robert-regular": ["robert-regular", "sans-serif"],
                    },
                    colors: {
                        blue: {
                            50: "#dfdff0",
                            75: "#dfdff2",
                            100: "#f0f2fa",
                            200: "#010101",
                            300: "#4fb7dd",
                        },
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-blue-50">
    <!-- Header -->
    <header class="bg-blue-200 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="font-zentry text-3xl">Event Registration</h1>
                <a href="events.php" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                    ← Back to Events
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Event Info -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="flex flex-col md:flex-row gap-6">
                    <img 
                        src="<?php echo htmlspecialchars($event['cover_image']); ?>" 
                        alt="<?php echo htmlspecialchars($event['title']); ?>"
                        class="w-full md:w-64 h-48 object-cover rounded-lg"
                    />
                    <div>
                        <h2 class="font-zentry text-4xl text-blue-200 mb-3"><?php echo htmlspecialchars($event['title']); ?></h2>
                        <p class="font-robert-regular text-blue-200/70 mb-4"><?php echo htmlspecialchars($event['description']); ?></p>
                        <div class="space-y-2">
                            <?php if (!empty($event['prize_money'])): ?>
                                <p class="font-robert-medium text-blue-300">Prize: <?php echo htmlspecialchars($event['prize_money']); ?></p>
                            <?php endif; ?>
                            <p class="font-robert-regular text-blue-200/70">Date: <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
                            <p class="font-robert-regular text-blue-200/70">Event Head: <?php echo htmlspecialchars($event['event_head_name']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="font-zentry text-3xl text-blue-200 mb-6">Registration Form</h3>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-robert-regular">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-robert-regular">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div>
                        <label for="team_name" class="block text-blue-200 font-robert-medium mb-2">Team Name *</label>
                        <input 
                            type="text" 
                            id="team_name" 
                            name="team_name" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="Enter your team name"
                        >
                    </div>

                    <div>
                        <label for="leader_name" class="block text-blue-200 font-robert-medium mb-2">Team Leader Name *</label>
                        <input 
                            type="text" 
                            id="leader_name" 
                            name="leader_name" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="Enter team leader's name"
                        >
                    </div>

                    <div>
                        <label for="leader_email" class="block text-blue-200 font-robert-medium mb-2">Email *</label>
                        <input 
                            type="email" 
                            id="leader_email" 
                            name="leader_email" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="your.email@example.com"
                        >
                    </div>

                    <div>
                        <label for="leader_phone" class="block text-blue-200 font-robert-medium mb-2">Phone Number *</label>
                        <input 
                            type="tel" 
                            id="leader_phone" 
                            name="leader_phone" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="+91 1234567890"
                        >
                    </div>

                    <div>
                        <label for="team_size" class="block text-blue-200 font-robert-medium mb-2">Team Size *</label>
                        <input 
                            type="number" 
                            id="team_size" 
                            name="team_size" 
                            required
                            min="1"
                            max="10"
                            value="1"
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        >
                    </div>

                    <div>
                        <label for="college_name" class="block text-blue-200 font-robert-medium mb-2">College/University Name *</label>
                        <input 
                            type="text" 
                            id="college_name" 
                            name="college_name" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="Enter your college name"
                        >
                    </div>

                    <div class="flex gap-4">
                        <button 
                            type="submit"
                            class="flex-1 bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105"
                        >
                            Submit Registration
                        </button>
                        <a 
                            href="events.php"
                            class="flex-1 bg-gray-500 text-white font-robert-medium py-3 rounded-lg hover:bg-gray-600 transition-all duration-300 text-center"
                        >
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
