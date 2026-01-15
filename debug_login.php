<?php
// File untuk debugging proses login
session_start();
require_once 'config.php';
require_once 'includes/db_connect.php';

// Fungsi untuk membersihkan input
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username']);
    $password = cleanInput($_POST['password']);
    
    echo "<h3>Mencoba login dengan:</h3>";
    echo "<p>Username/Email: " . $username . "</p>";
    
    // Cek apakah user ditemukan
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>✅ User ditemukan di database!</p>";
        echo "<p>ID: " . $user['id'] . "</p>";
        echo "<p>Username: " . $user['username'] . "</p>";
        echo "<p>Email: " . $user['email'] . "</p>";
        echo "<p>Nama: " . $user['first_name'] . " " . $user['last_name'] . "</p>";
        echo "<p>Role ID: " . $user['role_id'] . "</p>";
        echo "<p>Is Active: " . $user['is_active'] . "</p>";
        
        // Cek password
        $passwordMatch = password_verify($password, $user['password']);
        echo "<p>Password yang dimasukkan: " . $password . "</p>";
        echo "<p>Password hash di database: " . $user['password'] . "</p>";
        echo "<p>Password cocok: " . ($passwordMatch ? 'YA' : 'TIDAK') . "</p>";
        
        if ($passwordMatch) {
            if ($user['is_active'] == 1) {
                echo "<p>✅ Login berhasil! User aktif dan password benar.</p>";
            } else {
                echo "<p>❌ Login gagal! Akun tidak aktif.</p>";
            }
        } else {
            echo "<p>❌ Login gagal! Password salah.</p>";
        }
    } else {
        echo "<p>❌ User tidak ditemukan di database.</p>";
        echo "<p>Mungkin tabel users kosong atau username/email salah.</p>";
    }
} else {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login - COBIT 5 MEA Assessment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Debug Login</h1>
                <p class="text-gray-500 mt-2">Cek proses login secara manual</p>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username atau Email</label>
                    <input type="text" name="username" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#3291B6] focus:border-transparent outline-none transition"
                           placeholder="Masukkan username atau email">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#3291B6] focus:border-transparent outline-none transition"
                           placeholder="Masukkan password">
                </div>

                <button type="submit"
                        class="w-full bg-[#3291B6] hover:bg-[#2a7a99] text-white font-semibold py-3 px-6 rounded-xl transition-colors">
                    Debug Login
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Coba dengan:</p>
                <p>Username: <strong>admin</strong> atau <strong>user</strong></p>
                <p>Password: <strong>admin123</strong></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
?>