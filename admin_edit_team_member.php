<?php
require_once 'config.php';
requireLogin();

$error = '';
$memberId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get member details
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM team_members WHERE id = ?");
$stmt->bind_param("i", $memberId);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();

if (!$member) {
    header('Location: admin_team.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $designation = sanitizeInput($_POST['designation']);
    $team_category = sanitizeInput($_POST['team_category']);
    $display_order = intval($_POST['display_order']);
    
    if (empty($name) || empty($designation) || empty($team_category)) {
        $error = 'Please fill in all required fields';
    } else {
        $photoPath = $member['photo'];
        
        // Handle new photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoUpload = handleFileUpload($_FILES['photo'], 'uploads/', ['jpg', 'jpeg', 'png', 'webp']);
            if ($photoUpload['success']) {
                // Delete old photo
                if (file_exists($member['photo'])) {
                    unlink($member['photo']);
                }
                $photoPath = 'uploads/' . $photoUpload['fileName'];
            }
        }
        
        // Update database
        $updateStmt = $conn->prepare("UPDATE team_members SET name=?, designation=?, team_category=?, photo=?, display_order=? WHERE id=?");
        $updateStmt->bind_param("ssssii", $name, $designation, $team_category, $photoPath, $display_order, $memberId);
        
        if ($updateStmt->execute()) {
            header('Location: admin_team.php?success=updated');
            exit();
        } else {
            $error = 'Failed to update team member: ' . $conn->error;
        }
        
        $updateStmt->close();
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
    <title>Edit Team Member | InnovateX Admin</title>
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
                <h1 class="font-zentry text-3xl">Edit Team Member</h1>
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
                        value="<?php echo htmlspecialchars($member['name']); ?>"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                </div>

                <div>
                    <label for="designation" class="block text-blue-200 font-robert-medium mb-2">Designation *</label>
                    <input 
                        type="text" 
                        id="designation" 
                        name="designation" 
                        required
                        value="<?php echo htmlspecialchars($member['designation']); ?>"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
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
                        <option value="Faculty" <?php echo $member['team_category'] === 'Faculty' ? 'selected' : ''; ?>>Faculty</option>
                        <option value="Organisers" <?php echo $member['team_category'] === 'Organisers' ? 'selected' : ''; ?>>Organisers</option>
                        <option value="Event Heads" <?php echo $member['team_category'] === 'Event Heads' ? 'selected' : ''; ?>>Event Heads</option>
                        <option value="Design Team" <?php echo $member['team_category'] === 'Design Team' ? 'selected' : ''; ?>>Design Team</option>
                        <option value="Website Team" <?php echo $member['team_category'] === 'Website Team' ? 'selected' : ''; ?>>Website Team</option>
                        <option value="Decor Team" <?php echo $member['team_category'] === 'Decor Team' ? 'selected' : ''; ?>>Decor Team</option>
                        <option value="Volunteers" <?php echo $member['team_category'] === 'Volunteers' ? 'selected' : ''; ?>>Volunteers</option>
                    </select>
                </div>

                <div>
                    <label class="block text-blue-200 font-robert-medium mb-2">Current Photo</label>
                    <img src="<?php echo htmlspecialchars($member['photo']); ?>" alt="Photo" class="w-32 h-32 rounded-full mx-auto mb-2 object-cover">
                    <label for="photo" class="block text-blue-200 font-robert-regular text-sm mb-2">Upload New Photo (Optional)</label>
                    <input 
                        type="file" 
                        id="photo" 
                        name="photo"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                </div>

                <div>
                    <label for="display_order" class="block text-blue-200 font-robert-medium mb-2">Display Order</label>
                    <input 
                        type="number" 
                        id="display_order" 
                        name="display_order"
                        value="<?php echo $member['display_order']; ?>"
                        min="0"
                        class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                    >
                </div>

                <div class="flex gap-4">
                    <button 
                        type="submit"
                        class="flex-1 bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105"
                    >
                        Update Team Member
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
