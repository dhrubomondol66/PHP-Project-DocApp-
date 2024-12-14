<!-- download.php -->
<?php
// You may want to add additional security checks before allowing the download

$filename = 'sample_recipe.pdf';
$filepath = 'path/to/your/recipe/files/' . $Payment; // Update with the actual path

// Check if the file exists
if (file_exists($filepath)) {
    // Set appropriate headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));

    // Read the file and output it to the browser
    readfile($filepath);
    exit;
} else {
    echo 'File not found.';
}
?>
