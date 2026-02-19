<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Handle team member deletion
if (isset($_GET['delete'])) {
    $memberId = intval($_GET['delete']);
    
    // Get photo path before deleting
    $stmt = $conn->prepare("SELECT photo FROM team_members WHERE id = ?");
    $stmt->bind_param("i", $memberId);
    $stmt->execute();
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    
    if ($member) {
        // Delete photo
        if (file_exists($member['photo'])) {
            unlink($member['photo']);
        }
        
        // Delete member
        $deleteStmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
        $deleteStmt->bind_param("i", $memberId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        header('Location: admin_team.php?success=deleted');
        exit();
    }
}

// Get all team members grouped by category
$teamCategories = ['Faculty', 'Organisers', 'Event Heads', 'Design Team', 'Website Team', 'Decor Team', 'Volunteers'];
$teamMembers = [];

foreach ($teamCategories as $category) {
    $stmt = $conn->prepare("SELECT * FROM team_members WHERE team_category = ? ORDER BY display_order ASC, name ASC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $teamMembers[$category] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team | InnovateX Admin</title>
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
                        yellow: {
                            300: "#edff66",
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
    <header class="bg-blue-200 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="font-zentry text-3xl">Team Management</h1>
                    <p class="font-robert-regular text-sm opacity-80">Manage team members</p>
                </div>
                <div class="flex gap-4">
                    <a href="about.php" target="_blank" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                        View About Page
                    </a>
                    <a href="admin_dashboard.php" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-robert-regular">
                <?php 
                    if ($_GET['success'] === 'added') echo 'Team member added successfully!';
                    elseif ($_GET['success'] === 'updated') echo 'Team member updated successfully!';
                    elseif ($_GET['success'] === 'deleted') echo 'Team member deleted successfully!';
                ?>
            </div>
        <?php endif; ?>

        <!-- Add New Member Button -->
        <div class="mb-6">
            <a href="admin_add_team_member.php" class="inline-block bg-blue-200 text-white font-robert-medium px-6 py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105">
                + Add New Team Member
            </a>
        </div>

        <!-- Team Category Tabs -->
        <div class="flex flex-wrap gap-4 mb-8">
            <?php foreach ($teamCategories as $index => $category): ?>
                <button 
                    onclick="showTeamCategory('<?php echo str_replace(' ', '_', $category); ?>')"
                    class="team-tab font-robert-medium px-6 py-3 rounded-lg transition-all duration-300 <?php echo $index === 0 ? 'bg-blue-200 text-white' : 'bg-white text-blue-200 border-2 border-blue-200/20 hover:bg-blue-200/10'; ?>"
                    data-category="<?php echo str_replace(' ', '_', $category); ?>"
                >
                    <?php echo $category; ?>
                    <span class="ml-2 text-sm opacity-75">(<?php echo count($teamMembers[$category]); ?>)</span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Team Members by Category -->
        <?php foreach ($teamCategories as $index => $category): ?>
            <div 
                id="team-<?php echo str_replace(' ', '_', $category); ?>" 
                class="team-content bg-white rounded-xl shadow-lg overflow-hidden <?php echo $index === 0 ? '' : 'hidden'; ?>"
            >
                <div class="p-6 bg-blue-200 border-b">
                    <h2 class="font-zentry text-2xl text-white"><?php echo $category; ?></h2>
                </div>
                
                <div class="p-6">
                    <?php if (!empty($teamMembers[$category])): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($teamMembers[$category] as $member): ?>
                                <div class="border-2 border-blue-200/10 rounded-lg p-4 hover:shadow-lg transition-shadow">
                                    <img src="<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                                    <h3 class="font-robert-medium text-blue-200 text-center text-lg"><?php echo htmlspecialchars($member['name']); ?></h3>
                                    <p class="font-robert-regular text-blue-200/70 text-center text-sm mb-4"><?php echo htmlspecialchars($member['designation']); ?></p>
                                    <div class="flex gap-2 justify-center">
                                        <a href="admin_edit_team_member.php?id=<?php echo $member['id']; ?>" class="bg-blue-300 text-white px-3 py-1 rounded font-robert-regular text-sm hover:bg-blue-300/90 transition-colors">
                                            Edit
                                        </a>
                                        <a href="?delete=<?php echo $member['id']; ?>" onclick="return confirm('Are you sure you want to delete this team member?');" class="bg-red-600 text-white px-3 py-1 rounded font-robert-regular text-sm hover:bg-red-700 transition-colors">
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-blue-200/50 font-robert-regular py-8">No team members in this category yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Team category switching
        function showTeamCategory(category) {
            // Hide all team content sections
            const allContent = document.querySelectorAll('.team-content');
            allContent.forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active state from all tabs
            const allTabs = document.querySelectorAll('.team-tab');
            allTabs.forEach(tab => {
                tab.classList.remove('bg-blue-200', 'text-white');
                tab.classList.add('bg-white', 'text-blue-200', 'border-2', 'border-blue-200/20');
            });

            // Show selected team content
            const selectedContent = document.getElementById('team-' + category);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }

            // Activate selected tab
            const selectedTab = document.querySelector(`[data-category="${category}"]`);
            if (selectedTab) {
                selectedTab.classList.remove('bg-white', 'text-blue-200', 'border-2', 'border-blue-200/20');
                selectedTab.classList.add('bg-blue-200', 'text-white');
            }
        }
    </script>
</body>
</html>
