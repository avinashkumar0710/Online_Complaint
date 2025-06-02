<?php

// Validate the compNo parameter
if (isset($_GET['compNo'])) {
    $compNo = $_GET['compNo'];

    //echo 'compNO: ' . $compNo;

    // Construct the path to the image on server 100.9
    $imagePath = "images/{$compNo}.jpg";
    //echo 'image: ' . $imagePath;

    // Check if the image file exists
    if (file_exists($imagePath)) {
        // Read the image data
        $imageData = file_get_contents($imagePath);

        //echo 'image: ' . $imageData;

        // Convert the image data to base64
        $base64ImageData = base64_encode($imageData);

        // Send the base64-encoded image data as the response
        header('Content-Type: text/plain');
        echo $base64ImageData;
    } else {
        // If the image file does not exist, handle accordingly (e.g., return an error response)
        header('HTTP/1.0 404 Not Found');
        // echo 'Image not found';
    }
} else {
    // Handle the case where compNo is not provided (e.g., return an error response)
    header('HTTP/1.0 400 Bad Request');
    echo 'Missing compNo parameter';
}

?>
