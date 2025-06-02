<?php
header('Content-Type: image/jpeg');

// Check if the 'image' and 'filename' POST parameters are set
if (isset($_POST['image']) && isset($_POST['filename'])) {
    // Decode the base64-encoded image data
    $imagedata = base64_decode($_POST['image']);

    // Specify the destination directory for saving the image
    $uploadDir = 'images/';

    // Specify the filename based on the 'filename' parameter
    $filename = $uploadDir . $_POST['filename'];

    // Save the image in the destination directory
    $success = file_put_contents($filename, $imagedata);

    if ($success !== false) {
        // Image data saved successfully
        echo json_encode(['success' => true, 'message' => 'Image data received and saved successfully.']);
    } else {
        // Handle failure to save the image
        echo json_encode(['success' => false, 'message' => 'Failed to save the image.']);
    }
} else {
    // Handle cases where the expected POST parameters are not present
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
