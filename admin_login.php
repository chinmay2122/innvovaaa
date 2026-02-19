<?php
require_once 'config.php';

$error = '';
$success = '';

// Check if session is locked
if (isLoginLocked()) {
    $error = 'Too many failed login attempts. Please try again in 15 minutes.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isLoginLocked()) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
    
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                // ========== SECURITY: Session Hardening ==========
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Set secure session data
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['login_time'] = time();
                $_SESSION['login_ip'] = $_SERVER['REMOTE_ADDR'];
                
                // Reset login attempts
                $client_ip = $_SERVER['REMOTE_ADDR'];
                unset($_SESSION['login_attempts_' . md5($client_ip)]);
                
                header('Location: admin_dashboard.php');
                exit();
            } else {
                recordLoginAttempt();
                $error = 'Invalid username or password';
            }
        } else {
            recordLoginAttempt();
            $error = 'Invalid username or password';
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Generate CSRF token if not exists
$csrf_token = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | InnovateX</title>
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
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full">
            <!-- Logo/Title -->
            <div class="text-center mb-8">
                <h1 class="font-zentry text-5xl md:text-6xl text-blue-200 mb-2">InnovateX</h1>
                <p class="font-robert-regular text-lg text-blue-200/70">Admin Portal</p>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border-2 border-blue-200/10">
                <h2 class="font-zentry text-3xl text-blue-200 mb-6 text-center">Login</h2>
                
                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 font-robert-regular">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="space-y-6">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div>
                        <label for="username" class="block text-blue-200 font-robert-medium mb-2">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="Enter your username"
                        >
                    </div>
                    
                    <div>
                        <label for="password" class="block text-blue-200 font-robert-medium mb-2">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border-2 border-blue-200/20 rounded-lg focus:outline-none focus:border-blue-300 transition-colors font-robert-regular"
                            placeholder="Enter your password"
                        >
                    </div>
                    
                    <button 
                        type="submit"
                        class="w-full bg-blue-200 text-white font-robert-medium py-3 rounded-lg hover:bg-blue-200/90 transition-all duration-300 transform hover:scale-105"
                    >
                        Login
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="index.php" class="text-blue-300 hover:text-blue-200 font-robert-regular text-sm transition-colors">
                        ← Back to Home
                    </a>
                </div>
            </div>
            

        </div>
    </div>
</body>
</html>
