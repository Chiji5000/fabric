<?php
$dir = 'uploads/';

if (!file_exists($dir)) {
    echo "❌ The 'uploads/' directory does NOT exist.";
} elseif (!is_writable($dir)) {
    echo "⚠️ The 'uploads/' directory exists but is NOT writable.";
} else {
    echo "✅ The 'uploads/' directory exists and is writable!";
}
