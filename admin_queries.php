<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM contact_queries WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = 'Query deleted successfully';
    } else {
        $error = 'Failed to delete query';
    }
    
    $stmt->close();
    $conn->close();
}

// Handle mark as read action
if (isset($_GET['action']) && $_GET['action'] === 'mark_read' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE contact_queries SET status = 'read' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success = 'Query marked as read';
    } else {
        $error = 'Failed to update query';
    }
    
    $stmt->close();
    $conn->close();
}

// Fetch all queries
$conn = getDBConnection();
$result = $conn->query("SELECT * FROM contact_queries ORDER BY created_at DESC");
$queries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $queries[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Queries | InnovateX Admin</title>
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
                        violet: {
                            300: "#5724ff",
                        },
                        yellow: {
                            100: "#8e983f",
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
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-200 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="font-zentry text-2xl mb-8">InnovateX Admin</h1>
                <nav class="space-y-2">
                    <a href="admin_dashboard.php" class="block px-4 py-2 rounded font-robert-regular hover:bg-blue-300 transition-colors">
                        Dashboard
                    </a>
                    <a href="admin_add_event.php" class="block px-4 py-2 rounded font-robert-regular hover:bg-blue-300 transition-colors">
                        Add Event
                    </a>
                    <a href="admin_team.php" class="block px-4 py-2 rounded font-robert-regular hover:bg-blue-300 transition-colors">
                        Manage Team
                    </a>
                    <a href="admin_queries.php" class="block px-4 py-2 rounded font-robert-regular bg-blue-300">
                        Contact Queries
                    </a>
                    <a href="logout.php" class="block px-4 py-2 rounded font-robert-regular hover:bg-red-500 transition-colors">
                        Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden">
            <div class="p-8">
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="font-zentry text-5xl text-blue-200 mb-2">Contact Queries</h2>
                    <p class="font-robert-regular text-blue-200/70">Manage and respond to contact form submissions</p>
                </div>

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

                <!-- Queries Table -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-blue-200 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left font-robert-medium">Name</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Email</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Phone</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Subject</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Message</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Date</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Status</th>
                                    <th class="px-6 py-4 text-left font-robert-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if (empty($queries)): ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-blue-200/50 font-robert-regular">
                                            No queries found
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($queries as $query): ?>
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-6 py-4 font-robert-regular text-blue-200">
                                                <?php echo htmlspecialchars($query['name']); ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200">
                                                <a href="mailto:<?php echo htmlspecialchars($query['email']); ?>" 
                                                   class="text-blue-300 hover:underline">
                                                    <?php echo htmlspecialchars($query['email']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200">
                                                <?php echo htmlspecialchars($query['phone']) ?: 'N/A'; ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200">
                                                <?php echo htmlspecialchars($query['subject']); ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200">
                                                <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($query['message']); ?>">
                                                    <?php echo htmlspecialchars(substr($query['message'], 0, 50)); ?>
                                                    <?php if (strlen($query['message']) > 50) echo '...'; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200 whitespace-nowrap">
                                                <?php echo date('M d, Y', strtotime($query['created_at'])); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if ($query['status'] === 'unread'): ?>
                                                    <span class="px-3 py-1 text-xs font-robert-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                        Unread
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-3 py-1 text-xs font-robert-medium bg-green-100 text-green-800 rounded-full">
                                                        Read
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex gap-2">
                                                    <?php if ($query['status'] === 'unread'): ?>
                                                        <a href="?action=mark_read&id=<?php echo $query['id']; ?>" 
                                                           class="text-blue-300 hover:text-blue-400 font-robert-regular text-sm"
                                                           onclick="return confirm('Mark this query as read?')">
                                                            Mark Read
                                                        </a>
                                                    <?php endif; ?>
                                                    <button 
                                                        onclick="viewQuery(<?php echo htmlspecialchars(json_encode($query)); ?>)"
                                                        class="text-green-600 hover:text-green-700 font-robert-regular text-sm">
                                                        View
                                                    </button>
                                                    <a href="?action=delete&id=<?php echo $query['id']; ?>" 
                                                       class="text-red-600 hover:text-red-700 font-robert-regular text-sm"
                                                       onclick="return confirm('Are you sure you want to delete this query?')">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing full query -->
    <div id="queryModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="font-zentry text-3xl text-blue-200">Query Details</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Name:</label>
                        <p id="modalName" class="font-robert-regular text-blue-200/80"></p>
                    </div>
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Email:</label>
                        <p id="modalEmail" class="font-robert-regular text-blue-200/80"></p>
                    </div>
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Phone:</label>
                        <p id="modalPhone" class="font-robert-regular text-blue-200/80"></p>
                    </div>
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Subject:</label>
                        <p id="modalSubject" class="font-robert-regular text-blue-200/80"></p>
                    </div>
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Message:</label>
                        <p id="modalMessage" class="font-robert-regular text-blue-200/80 whitespace-pre-wrap"></p>
                    </div>
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Date:</label>
                        <p id="modalDate" class="font-robert-regular text-blue-200/80"></p>
                    </div>
                    <div>
                        <label class="font-robert-medium text-blue-200 block mb-1">Status:</label>
                        <p id="modalStatus" class="font-robert-regular text-blue-200/80"></p>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button 
                        onclick="closeModal()" 
                        class="px-6 py-2 bg-blue-200 text-white rounded font-robert-medium hover:bg-blue-200/90 transition-colors">
                        Close
                    </button>
                    <a id="modalReplyBtn" 
                       href="" 
                       class="px-6 py-2 bg-blue-300 text-white rounded font-robert-medium hover:bg-blue-400 transition-colors">
                        Reply via Email
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewQuery(query) {
            document.getElementById('modalName').textContent = query.name;
            document.getElementById('modalEmail').textContent = query.email;
            document.getElementById('modalPhone').textContent = query.phone || 'N/A';
            document.getElementById('modalSubject').textContent = query.subject;
            document.getElementById('modalMessage').textContent = query.message;
            document.getElementById('modalDate').textContent = new Date(query.created_at).toLocaleString();
            document.getElementById('modalStatus').textContent = query.status.charAt(0).toUpperCase() + query.status.slice(1);
            document.getElementById('modalReplyBtn').href = 'mailto:' + query.email + '?subject=Re: ' + encodeURIComponent(query.subject);
            document.getElementById('queryModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('queryModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('queryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>
