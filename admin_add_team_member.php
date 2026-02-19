<?php
require_once 'config.php';
requireLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $designation = sanitizeInput($_POST['designation']);
    $team_category = sanitizeInput($_POST['team_category']);
    $display_order = intval($_POST['display_order']);
    
    if (empty($name) || empty($designation) || empty($team_category)) {
        $error = 'Please fill in all required fields';
    } else {
        // Handle photo upload
        $photoUpload = handleFileUpload($_FILES['photo'], 'uploads/', ['jpg', 'jpeg', 'png', 'webp']);
        
        if (!$photoUpload['success']) {
            $error = 'Photo upload failed: ' . $photoUpload['message'];
        } else {
            $photoPath = 'uploads/' . $photoUpload['fileName'];
            
            // Insert into database
            $conn = getDBConnection();
            $stmt = $conn->prepare("INSERT INTO team_members (name, designation, team_category, photo, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $designation, $team_category, $photoPath, $display_order);
            
            if ($stmt->execute()) {
                header('Location: admin_team.php?success=added');
                exit();
            } else {
                $error = 'Failed to add team member: ' . $conn->error;
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
    <title>Add Team Member | InnovateX Admin</title>
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
    <header class="bg-blue-200 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="font-zentry text-3xl">Add Team Member</h1>
                <a href="admin_team.php" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                    ← Back
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
                <div>
                    <label for="name" class="block text-blue-200 font-robert-medium mb-2">Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="e.g., Dr. John Doe"
                    >
                </div>

                <div>
                    <label for="designation" class="block text-blue-200 font-robert-medium mb-2">Designation *</label>
                    <input 
                        type="text" 
                        id="designation" 
                        name="designation" 
                        required
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                        placeholder="e.g., Event Coordinator"
                    >
                </div>

                <div>
                    <label for="team_category" class="block text-blue-200 font-robert-medium mb-2">Team Category *</label>
                    <select 
                        id="team_category" 
                        name="team_category" 
                        required
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                        <option value="">Select Category</option>
                        <option value="Faculty">Faculty</option>
                        <option value="Organisers">Organisers</option>
                        <option value="Event Heads">Event Heads</option>
                        <option value="Design Team">Design Team</option>
                        <option value="Website Team">Website Team</option>
                        <option value="Decor Team">Decor Team</option>
                        <option value="Volunteers">Volunteers</option>
                    </select>
                </div>

                <div>
                    <label for="photo" class="block text-blue-200 font-robert-medium mb-2">Photo * (JPG, PNG, WEBP)</label>
                    <input 
                        type="file" 
                        id="photo" 
                        name="photo" 
                        required
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                    <p class="text-sm text-blue-200/60 mt-1 font-robert-regular">Recommended: Square image (400x400px)</p>
                </div>

                <div>
                    <label for="display_order" class="block text-blue-200 font-robert-medium mb-2">Display Order</label>
                    <input 
                        type="number" 
                        id="display_order" 
                        name="display_order"
                        value="0"
                        min="0"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                    <p class="text-sm text-blue-200/60 mt-1 font-robert-regular">Lower numbers appear first (0 = default order)</p>
                </div>

                <div class="flex gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105"
                    >
                        Add Team Member
                    </button>
                    <a 
                        href="admin_team.php"
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
