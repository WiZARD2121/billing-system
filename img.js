document.getElementById('imageInput').addEventListener('change', function () {
    previewImage();
});

function previewImage() {
    var input = document.getElementById('imageInput');
    var container = document.getElementById('imageContainer');

    // Check if a file is selected
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            // Display the uploaded image
            container.innerHTML = '<img src="' + e.target.result + '" alt="Uploaded Image">';
        };

        // Read the image file as a data URL
        reader.readAsDataURL(input.files[0]);
    } else {
        alert('Please select an image file.');
    }
}
