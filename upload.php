<?php
// Function to generate an auto-incremented ID
function generateID() {
  // Your logic to generate the ID from the last ID in the folder or database
  // For example, you can read the last ID from a file or database and increment it by 1.
  return 1; // Replace with your auto-generated ID
}

// Main code
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if the image data, latitude, and longitude are received in the request
  if (isset($_POST['imageData']) && isset($_POST['latitude']) && isset($_POST['longitude'])) {
    // Get the image data, latitude, and longitude from the request
    $imageData = $_POST['imageData'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Generate the auto-incremented ID
    $id = generateID();

    // Define the folder where you want to save the images
    $uploadFolder = 'uploads/';

    // Create the directory if it does not exist
    if (!file_exists($uploadFolder)) {
      mkdir($uploadFolder, 0777, true);
    }

    // Decode the image data from base64 and save it to the file
    $imageDataDecoded = base64_decode($imageData);
    $imageName = 'image_' . $id . '.jpg';
    $imagePath = $uploadFolder . $imageName;
    file_put_contents($imagePath, $imageDataDecoded);

    // Save the latitude and longitude to a text file (optional)
    $locationData = "ID: $id\nLatitude: $latitude\nLongitude: $longitude\n";
    $locationFile = $uploadFolder . 'location_data.txt';
    file_put_contents($locationFile, $locationData, FILE_APPEND);

    // Respond with the success message and the generated ID
    echo json_encode(array('success' => true, 'id' => $id));
  } else {
    // Respond with an error message if required data is missing
    echo json_encode(array('success' => false, 'message' => 'Missing data in the request.'));
  }
}
?>
