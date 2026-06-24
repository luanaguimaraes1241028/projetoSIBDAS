<?php
define('APP_NAME', 'MedCore Inventory');
define('APP_VERSION', '1.0.0');
define('APP_COPYRIGHT', '© 2025 MedCore');

define('BASE_URL', '/sibdas/1241028/medcore');

define('MYSQL_HOST',     'vsgate-s1.dei.isep.ipp.pt');
define('MYSQL_PORT',     10464);
define('MYSQL_DATABASE', 'db1241028');
define('MYSQL_USERNAME', '1241028');
define('MYSQL_PASSWORD', 'guimarães_028');

// AES-256-CBC usado exclusivamente para ofuscar IDs em parâmetros GET (ex: ?id=abc...)
// as passwords e dados sensíveis são protegidos por bcrypt e PDO prepared statements
define('OPENSSL_METHOD', 'AES-256-CBC');
define('OPENSSL_KEY',    'H0SDRQzIGqclX2kbYBk9xspdn9U5f3Wa');
define('OPENSSL_IV',     'BzKAbjuREsHgnw56');
