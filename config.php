<?php
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        throw new RuntimeException(".env file not found: {$filePath}");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Bỏ qua comment và dòng trống
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        // Xử lý giá trị có dấu = trong value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Xử lý quoted values ("" hoặc '')
            if (preg_match('/^"(.*)"$/s', $value, $matches) || preg_match("/^'(.*)'$/s", $value, $matches)) {
                $value = $matches[1];
            }

            // Xử lý biến môi trường lồng nhau (ví dụ: ${DB_HOST})
            $value = preg_replace_callback('/\${([a-zA-Z0-9_]+)}/', function ($match) {
                return getenv($match[1]) ?: $match[0];
            }, $value);

            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
