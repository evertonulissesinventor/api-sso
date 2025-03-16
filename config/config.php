<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_sso');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3306');

// JWT configuration
define('JWT_SECRET', 'your-256-bit-secret'); // Change this to a secure secret key in production
define('JWT_EXPIRATION', 3600); // Token expiration time in seconds (1 hour)

// API configuration
define('ALLOW_ORIGIN', '*'); // Configure CORS as needed
define('API_VERSION', 'v1');