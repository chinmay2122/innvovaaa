<?php
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

// Generate CSRF token for form
$csrf_token = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    verifyCsrfToken();
    
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $prize_money = sanitizeInput($_POST['prize_money']);
    $event_date = sanitizeInput($_POST['event_date']);
    $event_head_name = sanitizeInput($_POST['event_head_name']);
    $registration_link = sanitizeInput($_POST['registration_link']);
    
    // Validate inputs
    if (empty($title) || empty($description) || empty($event_date) || empty($event_head_name)) {
        $error = 'Please fill in all required fields';
    } else {
        // Handle cover image upload
        $coverUpload = handleFileUpload($_FILES['cover_image'], 'uploads/', ['jpg', 'jpeg', 'png', 'webp']);
        
        if (!$coverUpload['success']) {
            $error = 'Cover image upload failed: ' . $coverUpload['message'];
        } else {
            $coverImagePath = 'uploads/' . $coverUpload['fileName'];
            
            // Handle rule book upload (optional)
            $ruleBookPath = null;
            if (isset($_FILES['rule_book']) && $_FILES['rule_book']['error'] === UPLOAD_ERR_OK) {
                $ruleBookUpload = handleFileUpload($_FILES['rule_book'], 'uploads/', ['pdf']);
                if ($ruleBookUpload['success']) {
                    $ruleBookPath = 'uploads/' . $ruleBookUpload['fileName'];
                }
            }
            
            // Insert into database
            $conn = getDBConnection();
            $stmt = $conn->prepare("INSERT INTO events (title, description, cover_image, prize_money, event_date, event_head_name, rule_book, registration_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $title, $description, $coverImagePath, $prize_money, $event_date, $event_head_name, $ruleBookPath, $registration_link);
            
            if ($stmt->execute()) {
                header('Location: admin_dashboard.php?success=created');
                exit();
            } else {
                $error = 'Failed to create event: ' . $conn->error;
            }
            
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Event | InnovateX Admin</title>
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
                <h1 class="font-zentry text-3xl">Add New Event</h1>
                <a href="admin_dashboard.php" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-robert-regular">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                
                <!-- Title -->
                <div>
                    <label for="title" class="block text-blue-200 font-robert-medium mb-2">Event Title *</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        required
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="e.g., Code Sprint 2026"
                    >
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-blue-200 font-robert-medium mb-2">Description *</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        required
                        rows="4"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="Brief description of the event..."
                    ></textarea>
                </div>

                <!-- Cover Image -->
                <div>
                    <label for="cover_image" class="block text-blue-200 font-robert-medium mb-2">Cover Image * (JPG, PNG, WEBP)</label>
                    <input 
                        type="file" 
                        id="cover_image" 
                        name="cover_image" 
                        required
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                </div>

                <!-- Prize Money -->
                <div>
                    <label for="prize_money" class="block text-blue-200 font-robert-medium mb-2">Prize Money</label>
                    <input 
                        type="text" 
                        id="prize_money" 
                        name="prize_money"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="e.g., ₹50,000"
                    >
                </div>

                <!-- Event Date -->
                <div>
                    <label for="event_date" class="block text-blue-200 font-robert-medium mb-2">Event Date *</label>
                    <input 
                        type="date" 
                        id="event_date" 
                        name="event_date" 
                        required
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                </div>

                <!-- Event Head Name -->
                <div>
                    <label for="event_head_name" class="block text-blue-200 font-robert-medium mb-2">Event Head Name *</label>
                    <input 
                        type="text" 
                        id="event_head_name" 
                        name="event_head_name" 
                        required
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="e.g., Dr. Rajesh Kumar"
                    >
                </div>

                <!-- Rule Book -->
                <div>
                    <label for="rule_book" class="block text-blue-200 font-robert-medium mb-2">Rule Book PDF (Optional)</label>
                    <input 
                        type="file" 
                        id="rule_book" 
                        name="rule_book"
                        accept=".pdf"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                </div>

                <!-- Registration Link -->
                <div>
                    <label for="registration_link" class="block text-blue-200 font-robert-medium mb-2">Registration Link</label>
                    <input 
                        type="url" 
                        id="registration_link" 
                        name="registration_link"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="https://forms.google.com/..."
                    >
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105"
                    >
                        Create Event
                    </button>
                    <a 
                        href="admin_dashboard.php"
                        class="flex-1 bg-gray-500 text-white font-robert-medium py-3 rounded-lg hover:bg-gray-600 transition-all duration-300 text-center"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
