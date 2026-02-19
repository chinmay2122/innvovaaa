<?php
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

// Generate CSRF token for form
$csrf_token = generateCsrfToken();

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrfToken();
    
    $action = sanitizeInput($_POST['action']);
    $file_id = isset($_POST['file_id']) ? sanitizeInput($_POST['file_id']) : '';
    
    // Delete file
    if ($action === 'delete_file' && !empty($file_id)) {
        $file_path = 'uploads/' . $file_id;
        
        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                $success = 'File deleted successfully!';
            } else {
                $error = 'Failed to delete file. Please try again.';
            }
        } else {
            $error = 'File not found.';
        }
    }
    
    // Delete registration/user login
    if ($action === 'delete_registration' && !empty($file_id)) {
        $registration_id = intval($file_id);
        $conn = getDBConnection();
        
        $stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
        $stmt->bind_param("i", $registration_id);
        
        if ($stmt->execute()) {
            $success = 'Registration deleted successfully!';
        } else {
            $error = 'Failed to delete registration. Please try again.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Get all files from uploads directory
$files = [];
$uploads_dir = 'uploads/';
if (is_dir($uploads_dir)) {
    $dir_files = scandir($uploads_dir);
    foreach ($dir_files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.htaccess') {
            $file_path = $uploads_dir . $file;
            $files[] = [
                'name' => $file,
                'size' => filesize($file_path),
                'type' => strtolower(pathinfo($file, PATHINFO_EXTENSION)),
                'modified' => filemtime($file_path)
            ];
        }
    }
}

// Get all registrations from database
$conn = getDBConnection();
$registrations = $conn->query("
    SELECT r.*, e.title as event_title 
    FROM registrations r 
    JOIN events e ON r.event_id = e.id 
    ORDER BY r.registration_date DESC
");

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Management | InnovateX Admin</title>
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
    <style>
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-btn.active {
            background-color: #4fb7dd;
            color: white;
        }
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .file-card {
            background: white;
            border: 2px solid #4fb7dd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .file-preview {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal.active {
            display: flex;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
        }
        .modal-content h3 {
            font-size: 1.5rem;
            color: #010101;
            margin-bottom: 20px;
        }
        .modal-content p {
            color: #666;
            margin-bottom: 20px;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .modal-btn-confirm {
            background-color: #e74c3c;
            color: white;
        }
        .modal-btn-cancel {
            background-color: #ccc;
            color: #333;
        }
        .table-container {
            overflow-x: auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border: 2px solid #4fb7dd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #4fb7dd;
        }
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body class="bg-blue-50">
    <!-- Navigation Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <h1 class="font-zentry text-4xl text-blue-200">Data Management</h1>
                <a href="admin_dashboard.php" class="bg-blue-200 text-white px-6 py-2 rounded-lg hover:bg-blue-200/90 font-robert-medium transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Success/Error Messages -->
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 font-robert-regular">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-robert-regular">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($files); ?></div>
                <div class="stat-label font-robert-medium">Files Stored</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $registrations->num_rows; ?></div>
                <div class="stat-label font-robert-medium">User Registrations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $images = array_filter($files, function($f) { 
                        return in_array($f['type'], ['jpg', 'jpeg', 'png', 'webp']); 
                    }); 
                    echo count($images); 
                    ?>
                </div>
                <div class="stat-label font-robert-medium">Images</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $pdfs = array_filter($files, function($f) { 
                        return $f['type'] === 'pdf'; 
                    }); 
                    echo count($pdfs); 
                    ?>
                </div>
                <div class="stat-label font-robert-medium">PDFs</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Tab Buttons -->
            <div class="flex border-b-2 border-blue-200/20">
                <button class="tab-btn active flex-1 px-6 py-4 font-robert-medium text-blue-200 border-b-2 border-blue-200" onclick="switchTab('images')">
                    🖼️ Images
                </button>
                <button class="tab-btn flex-1 px-6 py-4 font-robert-medium text-blue-200 hover:bg-blue-50" onclick="switchTab('pdfs')">
                    📄 PDFs
                </button>
                <button class="tab-btn flex-1 px-6 py-4 font-robert-medium text-blue-200 hover:bg-blue-50" onclick="switchTab('registrations')">
                    👥 User Logins
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Images Tab -->
                <div id="images" class="tab-content active">
                    <h2 class="font-zentry text-2xl text-blue-200 mb-4">Images</h2>
                    
                    <?php 
                    $images = array_filter($files, function($f) { 
                        return in_array($f['type'], ['jpg', 'jpeg', 'png', 'webp']); 
                    }); 
                    ?>
                    
                    <?php if (count($images) > 0): ?>
                        <div class="file-grid">
                            <?php foreach ($images as $image): ?>
                                <div class="file-card">
                                    <img src="<?php echo htmlspecialchars('uploads/' . $image['name']); ?>" 
                                         alt="<?php echo htmlspecialchars($image['name']); ?>" 
                                         class="file-preview">
                                    <p class="font-robert-medium text-sm text-blue-200 mb-2 truncate">
                                        <?php echo htmlspecialchars($image['name']); ?>
                                    </p>
                                    <p class="font-robert-regular text-xs text-blue-200/70 mb-3">
                                        <?php echo number_format($image['size'] / 1024, 2); ?> KB
                                    </p>
                                    <p class="font-robert-regular text-xs text-blue-200/70 mb-3">
                                        <?php echo date('M d, Y', $image['modified']); ?>
                                    </p>
                                    <button onclick="deleteItem('file', '<?php echo htmlspecialchars($image['name']); ?>')" 
                                            class="w-full bg-red-600 text-white py-2 rounded font-robert-medium text-sm hover:bg-red-700 transition-colors">
                                        Delete
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-blue-200/70 font-robert-regular py-8">
                            No images found.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- PDFs Tab -->
                <div id="pdfs" class="tab-content">
                    <h2 class="font-zentry text-2xl text-blue-200 mb-4">PDFs</h2>
                    
                    <?php 
                    $pdfs = array_filter($files, function($f) { 
                        return $f['type'] === 'pdf'; 
                    }); 
                    ?>
                    
                    <?php if (count($pdfs) > 0): ?>
                        <div class="table-container">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-blue-200/10">
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">File Name</th>
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Size</th>
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Date Modified</th>
                                        <th class="px-6 py-4 text-center font-robert-medium text-blue-200">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pdfs as $pdf): ?>
                                        <tr class="border-b border-blue-200/20 hover:bg-blue-50">
                                            <td class="px-6 py-4 font-robert-regular text-blue-200">
                                                📄 <?php echo htmlspecialchars($pdf['name']); ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200/70">
                                                <?php echo number_format($pdf['size'] / 1024, 2); ?> KB
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200/70">
                                                <?php echo date('M d, Y H:i', $pdf['modified']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <button onclick="deleteItem('file', '<?php echo htmlspecialchars($pdf['name']); ?>')" 
                                                        class="bg-red-600 text-white px-4 py-2 rounded font-robert-medium text-sm hover:bg-red-700 transition-colors">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-blue-200/70 font-robert-regular py-8">
                            No PDFs found.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Registrations Tab -->
                <div id="registrations" class="tab-content">
                    <h2 class="font-zentry text-2xl text-blue-200 mb-4">User Logins & Registrations</h2>
                    
                    <?php if ($registrations->num_rows > 0): ?>
                        <div class="table-container">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-blue-200/10">
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Team Name</th>
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Leader</th>
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Email</th>
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Event</th>
                                        <th class="px-6 py-4 text-left font-robert-medium text-blue-200">Date</th>
                                        <th class="px-6 py-4 text-center font-robert-medium text-blue-200">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($reg = $registrations->fetch_assoc()): ?>
                                        <tr class="border-b border-blue-200/20 hover:bg-blue-50">
                                            <td class="px-6 py-4 font-robert-medium text-blue-200">
                                                <?php echo htmlspecialchars($reg['team_name']); ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200/70">
                                                <?php echo htmlspecialchars($reg['leader_name']); ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200/70">
                                                <a href="mailto:<?php echo htmlspecialchars($reg['leader_email']); ?>" 
                                                   class="text-blue-300 hover:text-blue-200">
                                                    <?php echo htmlspecialchars($reg['leader_email']); ?>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200/70">
                                                <?php echo htmlspecialchars($reg['event_title']); ?>
                                            </td>
                                            <td class="px-6 py-4 font-robert-regular text-blue-200/70">
                                                <?php echo date('M d, Y', strtotime($reg['registration_date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <button onclick="deleteItem('registration', '<?php echo htmlspecialchars($reg['id']); ?>', '<?php echo htmlspecialchars($reg['team_name']); ?>')" 
                                                        class="bg-red-600 text-white px-4 py-2 rounded font-robert-medium text-sm hover:bg-red-700 transition-colors">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-blue-200/70 font-robert-regular py-8">
                            No registrations found.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p id="modalMessage">Are you sure you want to delete this item? This action cannot be undone.</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button class="modal-btn modal-btn-confirm" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Deletion -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" id="deleteAction">
        <input type="hidden" name="file_id" id="deleteFileId">
    </form>

    <script>
        let deleteData = {
            type: '',
            id: '',
            name: ''
        };

        function switchTab(tabName) {
            // Hide all tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Remove active class from buttons
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => btn.classList.remove('active'));

            // Show selected tab
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        function deleteItem(type, id, name = '') {
            deleteData.type = type;
            deleteData.id = id;
            deleteData.name = name;

            const modal = document.getElementById('deleteModal');
            const message = document.getElementById('modalMessage');

            if (type === 'file') {
                message.textContent = `Are you sure you want to delete the file "${id}"? This action cannot be undone.`;
            } else if (type === 'registration') {
                message.textContent = `Are you sure you want to delete the registration for "${name}"? This action cannot be undone.`;
            }

            modal.classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
            deleteData = { type: '', id: '', name: '' };
        }

        function confirmDelete() {
            const form = document.getElementById('deleteForm');
            const action = deleteData.type === 'file' ? 'delete_file' : 'delete_registration';

            document.getElementById('deleteAction').value = action;
            document.getElementById('deleteFileId').value = deleteData.id;

            closeDeleteModal();
            form.submit();
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
