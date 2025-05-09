<?php
// Ensure temp directory exists
if (!file_exists('../temp')) {
    mkdir('../temp', 0777, true);
}

// Get the image data from POST
$imageData = $_POST['image'];
$name = $_POST['name'];

// Remove the "data:image/png;base64," part
$imageData = str_replace('data:image/png;base64,', '', $imageData);
$imageData = str_replace(' ', '+', $imageData);

// Decode the base64 data
$imageData = base64_decode($imageData);

// Save the image
$file = '../temp/' . $name . '.png';
file_put_contents($file, $imageData);

// Return success
echo json_encode(['success' => true]);
?> 