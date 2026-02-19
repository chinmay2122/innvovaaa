<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Handle event deletion
if (isset($_GET['delete'])) {
    $eventId = intval($_GET['delete']);
    
    // Get file paths before deleting
    $stmt = $conn->prepare("SELECT cover_image, rule_book FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    
    if ($event) {
        // Delete files
        if (file_exists($event['cover_image'])) {
            unlink($event['cover_image']);
        }
        if (!empty($event['rule_book']) && file_exists($event['rule_book'])) {
            unlink($event['rule_book']);
        }
        
        // Delete event
        $deleteStmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $deleteStmt->bind_param("i", $eventId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        header('Location: admin_dashboard.php?success=deleted');
        exit();
    }
}

// Get all events
$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");

// Get statistics using prepared statements for security
function getStat($conn, $table, $where = '') {
    $query = "SELECT COUNT(*) as count FROM $table";
    if (!empty($where)) {
        $query .= " WHERE " . $where;
    }
    $result = $conn->query($query);
    return $result ? $result->fetch_assoc()['count'] : 0;
}

$totalEvents = getStat($conn, 'events');
$totalRegistrations = getStat($conn, 'registrations');
$upcomingEvents = getStat($conn, 'events', "event_date >= CURDATE()");
$totalTeamMembers = getStat($conn, 'team_members');
$unreadQueries = getStat($conn, 'contact_queries', "status = 'unread'");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | InnovateX</title>
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
                    <h1 class="font-zentry text-3xl">InnovateX Admin</h1>
                    <p class="font-robert-regular text-sm opacity-80">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                </div>
                <div class="flex gap-4">
                    <a href="events.php" target="_blank" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                        View Events Page
                    </a>
                    <a href="about.php" target="_blank" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                        View About Page
                    </a>
                    <a href="logout.php" class="font-robert-medium px-4 py-2 bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-blue-200/10">
                <h3 class="font-robert-medium text-blue-200/60 text-sm mb-2">Total Events</h3>
                <p class="font-zentry text-4xl text-blue-200"><?php echo $totalEvents; ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-blue-200/10">
                <h3 class="font-robert-medium text-blue-200/60 text-sm mb-2">Upcoming Events</h3>
                <p class="font-zentry text-4xl text-blue-200"><?php echo $upcomingEvents; ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-blue-200/10">
                <h3 class="font-robert-medium text-blue-200/60 text-sm mb-2">Total Registrations</h3>
                <p class="font-zentry text-4xl text-blue-200"><?php echo $totalRegistrations; ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-blue-200/10">
                <h3 class="font-robert-medium text-blue-200/60 text-sm mb-2">Team Members</h3>
                <p class="font-zentry text-4xl text-blue-200"><?php echo $totalTeamMembers; ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-blue-200/10">
                <h3 class="font-robert-medium text-blue-200/60 text-sm mb-2">Unread Queries</h3>
                <p class="font-zentry text-4xl text-blue-200"><?php echo $unreadQueries; ?></p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <a href="admin_add_event.php" class="group bg-white border-2 border-blue-100 hover:border-blue-400 hover:bg-blue-50 p-4 rounded-lg transition-all duration-200">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="p-2 bg-blue-100 group-hover:bg-blue-200 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="font-zentry text-base font-semibold text-gray-900">Add Event</h3>
                </div>
            </a>
            <a href="admin_team.php" class="group bg-white border-2 border-purple-100 hover:border-purple-400 hover:bg-purple-50 p-4 rounded-lg transition-all duration-200">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="p-2 bg-purple-100 group-hover:bg-purple-200 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                        </svg>
                    </div>
                    <h3 class="font-zentry text-base font-semibold text-gray-900">Manage Team</h3>
                </div>
            </a>
            <a href="admin_data_management.php" class="group bg-white border-2 border-green-100 hover:border-green-400 hover:bg-green-50 p-4 rounded-lg transition-all duration-200">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="p-2 bg-green-100 group-hover:bg-green-200 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8-4m0 0l8 4m0 0v10l-8 4-8-4V7m8 4v10m-8-10l8 4m0 0l8-4"></path>
                        </svg>
                    </div>
                    <h3 class="font-zentry text-base font-semibold text-gray-900">Data Management</h3>
                </div>
            </a>
            <a href="admin_queries.php" class="group bg-white border-2 border-amber-100 hover:border-amber-400 hover:bg-amber-50 p-4 rounded-lg transition-all duration-200">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="p-2 bg-amber-100 group-hover:bg-amber-200 rounded-lg transition-colors">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-zentry text-base font-semibold text-gray-900">Contact Queries</h3>
                </div>
            </a>
        </div>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-robert-regular">
                <?php 
                    if ($_GET['success'] === 'created') echo 'Event created successfully!';
                    elseif ($_GET['success'] === 'updated') echo 'Event updated successfully!';
                    elseif ($_GET['success'] === 'deleted') echo 'Event deleted successfully!';
                    elseif ($_GET['success'] === 'team_added') echo 'Team member added successfully!';
                    elseif ($_GET['success'] === 'team_updated') echo 'Team member updated successfully!';
                    elseif ($_GET['success'] === 'team_deleted') echo 'Team member deleted successfully!';
                ?>
            </div>
        <?php endif; ?>

        <!-- Add New Event Button -->
        <div class="mb-6">
            <a href="admin_add_event.php" class="inline-block bg-blue-200 text-white font-robert-medium px-6 py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105">
                + Add New Event
            </a>
        </div>

        <!-- Events List -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 bg-blue-200 border-b">
                <h2 class="font-zentry text-2xl text-white">Manage Events</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Cover</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Title</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Date</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Prize Money</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Event Head</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Registrations</th>
                            <th class="px-6 py-4 text-center font-robert-medium text-blue-200 text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-50">
                        <?php if ($events->num_rows > 0): ?>
                            <?php while ($event = $events->fetch_assoc()): ?>
                                <?php
                                    $eventId = $event['id'];
                                    $regCount = $conn = getDBConnection();
                                    $regCountResult = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE event_id = $eventId");
                                    $registrations = $regCountResult->fetch_assoc()['count'];
                                    $conn->close();
                                ?>
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <img src="<?php echo htmlspecialchars($event['cover_image']); ?>" alt="Cover" class="w-16 h-16 object-cover rounded-lg">
                                    </td>
                                    <td class="px-6 py-4 font-robert-medium text-blue-200"><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td class="px-6 py-4 font-robert-medium text-blue-300"><?php echo htmlspecialchars($event['prize_money']); ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo htmlspecialchars($event['event_head_name']); ?></td>
                                    <td class="px-6 py-4 font-robert-medium text-blue-200 text-center"><?php echo $registrations; ?></td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 justify-center">
                                            <a href="admin_edit_event.php?id=<?php echo $event['id']; ?>" class="bg-blue-300 text-white px-3 py-1 rounded font-robert-regular text-sm hover:bg-blue-300/90 transition-colors">
                                                Edit
                                            </a>
                                            <a href="admin_view_registrations.php?event_id=<?php echo $event['id']; ?>" class="bg-green-600 text-white px-3 py-1 rounded font-robert-regular text-sm hover:bg-green-700 transition-colors">
                                                View
                                            </a>
                                            <a href="?delete=<?php echo $event['id']; ?>" onclick="return confirm('Are you sure you want to delete this event? This will also delete all registrations.');" class="bg-red-600 text-white px-3 py-1 rounded font-robert-regular text-sm hover:bg-red-700 transition-colors">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-blue-200/50 font-robert-regular">
                                    No events found. Add your first event!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
