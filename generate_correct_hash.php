<!DOCTYPE html>
<html>
<head>
    <title>Generate Password Hash</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .result { background: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Generate Password Hash for admin123</h1>
    
    <?php
    if (isset($_POST['generate'])) {
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        echo "<div class='result'>";
        echo "<strong>Password:</strong> $password<br>";
        echo "<strong>Hash:</strong> $hash<br>";
        echo "<strong>Verification:</strong> " . (password_verify($password, $hash) ? 'SUCCESS' : 'FAILED');
        echo "</div>";
        
        echo "<h3>Update your database schema with this hash:</h3>";
        echo "<textarea rows='10' cols='80'>-- Insert default admin user (password: admin123)
-- Password hash for 'admin123' using password_hash()
INSERT INTO users (username, email, password, first_name, last_name, organization, role_id, is_active) VALUES
('admin', 'admin@example.com', '$hash', 'Administrator', 'Account', 'COBIT MEA Organization', 1, 1),
('user', 'user@example.com', '$hash', 'Regular', 'User', 'COBIT MEA Organization', 2, 1),
('lily', 'lilymarliana392@gmail.com', '$hash', 'Lily', 'Marliana', 'COBIT MEA Organization', 2, 1);</textarea>";
    } else {
        echo "<form method='post'>";
        echo "<button type='submit' name='generate'>Generate Hash for 'admin123'</button>";
        echo "</form>";
    }
    ?>
</body>
</html>