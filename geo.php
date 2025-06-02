<!DOCTYPE html>
<html>
<head>
  <title>Camera App</title>
  <!-- Add Google Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }

    #cameraContainer {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 400px;
      width: 100%;
      border: 1px solid #ccc;
    }

    #captureButton {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin: 10px;
    }

    #captureButton:hover {
      background-color: #0056b3;
    }
  </style>
</head>

<body>
<h1>Camera App</h1>

<!-- Camera container -->
<div id="cameraContainer">
  <video id="videoElement" width="100%" height="100%" autoplay></video>
</div>

<!-- Capture button -->
<button id="captureButton">Capture</button>

  <!-- Your other scripts and content here -->
  <script>
    // Function to handle opening the camera
function openCamera() {
  const constraints = {
    video: { width: 1280, height: 720 }
  };

  // Access the camera and display the video stream
  navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
      const videoElement = document.getElementById('videoElement');
      videoElement.srcObject = stream;
    })
    .catch((error) => {
      console.error('Error accessing the camera:', error);
    });
}

// Attach the click event handler to the "Capture" button
const captureButton = document.getElementById('captureButton');
captureButton.addEventListener('click', captureImage);

// Call openCamera to open the camera when the page loads
openCamera();

// Function to capture the image
function captureImage() {
  // Get the video element
  const videoElement = document.getElementById('videoElement');

  // Create a canvas element and set its dimensions to match the video element
  const canvas = document.createElement('canvas');
  canvas.width = videoElement.videoWidth;
  canvas.height = videoElement.videoHeight;

  // Draw the current video frame onto the canvas
  const ctx = canvas.getContext('2d');
  ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

  // Convert the canvas image to data URL
  const dataURL = canvas.toDataURL('image/jpeg');

  // You can use the 'dataURL' to display the captured image on the webpage or upload it to the server.
}

  </script>
</body>
</html>
