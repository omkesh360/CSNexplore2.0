<?php
// fix_encoding.php
$dirs = ['listing-detail', 'blogs'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'html') {
            $content = file_get_contents($file->getPathname());
            
            // Fix encoding issue character
            $newContent = str_replace(['ÔÇö', 'â€”'], '—', $content);
            $newContent = str_replace('localhost:8000', 'localhost', $newContent);
            
            if ($newContent !== $content) {
                file_put_contents($file->getPathname(), $newContent);
                echo "Fixed " . $file->getPathname() . "\n";
            }
        }
    }
}
echo "Encoding fix done.\n";
