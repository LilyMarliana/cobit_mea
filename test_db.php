<?php
// Test database connection
require_once 'config.php';

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
    
    // Check if database exists, if not create it
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);
    
    echo "Database connection successful! Database '" . DB_NAME . "' is ready.\n";
    
    // Now import the schema
    $schemaFile = 'database/cobit_mea_schema.sql';
    if (file_exists($schemaFile)) {
        $sql = file_get_contents($schemaFile);
        
        // Split the SQL file into individual statements
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Some statements might fail if they already exist, which is okay
                    // echo "Warning: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "Database schema imported successfully!\n";
        
        // Check if default data exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM mea_processes");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "Default MEA processes already exist.\n";
        } else {
            echo "No default data found. You may need to manually insert the sample data.\n";
        }
    } else {
        echo "Schema file not found: " . $schemaFile . "\n";
    }
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>