<?php
require_once 'config.php';
requireLogin();

$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Get event details
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT title FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header('Location: admin_dashboard.php');
    exit();
}

// Get all registrations for this event
$regStmt = $conn->prepare("SELECT * FROM registrations WHERE event_id = ? ORDER BY registration_date DESC");
$regStmt->bind_param("i", $eventId);
$regStmt->execute();
$registrations = $regStmt->get_result();

$stmt->close();
$regStmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations | InnovateX Admin</title>
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
                <div>
                    <h1 class="font-zentry text-3xl">Registrations</h1>
                    <p class="font-robert-regular text-sm opacity-80"><?php echo htmlspecialchars($event['title']); ?></p>
                </div>
                <a href="admin_dashboard.php" class="font-robert-medium px-4 py-2 bg-blue-300 rounded-lg hover:bg-blue-300/90 transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 bg-blue-200 border-b">
                <h2 class="font-zentry text-2xl text-white">All Registrations (<?php echo $registrations->num_rows; ?>)</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Team Name</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Leader</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Email</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Phone</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Team Size</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">College</th>
                            <th class="px-6 py-4 text-left font-robert-medium text-blue-200 text-sm">Registration Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-50">
                        <?php if ($registrations->num_rows > 0): ?>
                            <?php while ($reg = $registrations->fetch_assoc()): ?>
                                <tr class="hover:bg-blue-50/50 transition-colors">
                                    <td class="px-6 py-4 font-robert-medium text-blue-200"><?php echo htmlspecialchars($reg['team_name']); ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo htmlspecialchars($reg['leader_name']); ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo htmlspecialchars($reg['leader_email']); ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo htmlspecialchars($reg['leader_phone']); ?></td>
                                    <td class="px-6 py-4 font-robert-medium text-blue-300"><?php echo $reg['team_size']; ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo htmlspecialchars($reg['college_name']); ?></td>
                                    <td class="px-6 py-4 font-robert-regular text-blue-200/70"><?php echo date('M d, Y H:i', strtotime($reg['registration_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-blue-200/50 font-robert-regular">
                                    No registrations yet for this event.
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
