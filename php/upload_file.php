<?php

function upload_file($user_id, $fasta_file){
    // Check if a file was uploaded and no error occurred
        // Fetch the uploaded file's details
    $uploaded_file = $_FILES['fasta_file']['tmp_name'];
    $upload_dir = "/localdisk/home/s2667265/public_html/uploads/";  // Directory where files will be saved
    // Get the original file name and extension
    $file_name = basename($_FILES['fasta_file']['name']);
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    // Generate a unique file name using the user_id, original file name, and a timestamp
    $new_file_name = $user_id . "_" . time() . "_" . $file_name;

    // Specify the full path to save the file in the uploads directory
    $file_to_analyze = $upload_dir . $new_file_name;

    // Move the uploaded file to the target directory with the new name
    if (move_uploaded_file($uploaded_file, $file_to_analyze)) {
        // Preview the FASTA file (only first 500 characters for display)
        $preview = file_get_contents($file_to_analyze);
    } else {
        echo "Error while uploading the file. Please try again.";
    }
}    
?>