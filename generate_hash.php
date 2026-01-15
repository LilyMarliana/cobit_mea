<?php
// File untuk menghasilkan password hash yang benar
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hash\n";

// Cek apakah hash ini cocok dengan password
$isValid = password_verify($password, $hash);
echo "Password verification: " . ($isValid ? 'SUCCESS' : 'FAILED') . "\n";
?>