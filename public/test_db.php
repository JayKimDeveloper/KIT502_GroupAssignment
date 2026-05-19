<?php
$path = "/home/ykim13/public_html/phpliteadmin/db/database.sqlite";
echo "<pre>";
echo "Path: $path\n";
echo "Exists: " . (file_exists($path) ? "YES" : "NO") . "\n";
echo "Readable: " . (is_readable($path) ? "YES" : "NO") . "\n";
echo "Writable: " . (is_writable($path) ? "YES" : "NO") . "\n";

if (file_exists($path)) {
    echo "Permissions: " . substr(sprintf("%o", fileperms($path)), -4) . "\n";
    echo "Size: " . filesize($path) . " bytes\n";
    $owner = posix_getpwuid(fileowner($path));
    echo "Owner: " . $owner["name"] . "\n";
    $group = posix_getgrgid(filegroup($path));
    echo "Group: " . $group["name"] . "\n";
}

echo "\n--- PHP-FPM running as ---\n";
echo "User: " . posix_getpwuid(posix_geteuid())["name"] . "\n";
echo "Group: " . posix_getgrgid(posix_getegid())["name"] . "\n";

echo "\n--- Path traverse test ---\n";
$paths = [
    "/home",
    "/home/ykim13",
    "/home/ykim13/public_html",
    "/home/ykim13/public_html/phpliteadmin",
    "/home/ykim13/public_html/phpliteadmin/db",
];
foreach ($paths as $p) {
    echo "$p: ";
    if (!file_exists($p)) {
        echo "DOES NOT EXIST\n";
    } elseif (!is_readable($p)) {
        echo "NOT READABLE\n";
    } else {
        echo "OK\n";
    }
}
echo "</pre>";
