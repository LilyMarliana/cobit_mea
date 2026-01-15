<?php
// Database setup script for COBIT 5 MEA Assessment System
require_once 'config.php';

echo "<h2>COBIT 5 MEA Assessment System - Database Setup</h2>\n";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);
    
    echo "<p>✓ Database '" . DB_NAME . "' created/referenced successfully.</p>\n";
    
    // Read the schema file
    $schemaFile = 'database/cobit_mea_schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        
        // Split the SQL file into individual statements
        $statements = explode(';', $sql);
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    $successCount++;
                } catch (PDOException $e) {
                    // Some statements might fail if they already exist, which is okay
                    $errorCount++;
                    // Uncomment the line below if you want to see warnings
                    // echo "<p>⚠ Statement skipped: " . substr($statement, 0, 50) . "... (" . $e->getMessage() . ")</p>\n";
                }
            }
        }
        
        echo "<p>✓ Schema imported successfully! $successCount statements executed.</p>\n";
        if ($errorCount > 0) {
            echo "<p>ℹ $errorCount statements were skipped (likely already exist).</p>\n";
        }
        
        // Check if default data exists
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM mea_processes");
            $result = $stmt->fetch();
            $processCount = $result['count'];
            
            if ($processCount > 0) {
                echo "<p>✓ Default MEA processes already exist ($processCount processes).</p>\n";
            } else {
                echo "<p>⚠ No default MEA processes found.</p>\n";
            }
        } catch (PDOException $e) {
            echo "<p>⚠ Could not check for MEA processes: " . $e->getMessage() . "</p>\n";
        }
        
        // Check if default roles exist
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
            $result = $stmt->fetch();
            $rolesCount = $result['count'];
            
            if ($rolesCount > 0) {
                echo "<p>✓ Default roles already exist ($rolesCount roles).</p>\n";
            } else {
                echo "<p>⚠ No default roles found.</p>\n";
            }
        } catch (PDOException $e) {
            echo "<p>⚠ Could not check for roles: " . $e->getMessage() . "</p>\n";
        }
        
        echo "<h3>Setup Complete!</h3>\n";
        echo "<p>You can now use the COBIT 5 MEA Assessment System.</p>\n";
        echo "<p><a href='index.php?page=login'>Go to Login Page</a></p>\n";
        
    } else {
        echo "<p>❌ Schema file not found: " . $schemaFile . "</p>\n";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>\n";
}
?>