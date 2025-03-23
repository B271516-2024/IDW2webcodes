<?php
// Initialize variables
$uploaded_file = "";
$preview = "";
$analysis_result = "";
$plot_image = "";

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['fasta_file']) && $_FILES['fasta_file']['error'] == 0) {
        // Fetch the uploaded file's details
        $uploaded_file = $_FILES['fasta_file']['tmp_name'];
        $upload_dir = "/localdisk/home/s2667265/public_html/uploads/";  // Directory where files will be saved

        // Save the file to the "uploads" directory
        $upload_file_path = $upload_dir . basename($_FILES['fasta_file']['name']);
        move_uploaded_file($uploaded_file, $upload_file_path);

        // Preview the FASTA file (only first 500 characters for display)
        $preview = file_get_contents($upload_file_path);

        // Run the Python analysis script
        $command = escapeshellcmd("python3 /localdisk/home/s2667265/public_html/python/w_msa.py " . escapeshellarg($upload_file_path));
        $output = shell_exec($command);
        echo $output;

        // Get the image path from the Python script's output
        if (file_exists(trim($output))) {
            $plot_image = trim($output);  // Trim in case of extra spaces
            $analysis_result = "";
        } else {
            $analysis_result = "Error generating plot.";
        }

        // Convert absolute path to a web-accessible URL
        $web_accessible_path = str_replace("/home/s2667265/public_html/", "/", $plot_image);

        // Return HTML output for the result

        if (!empty($plot_image)) {
            echo "<h2>Similarity Plot</h2>";
            echo "<img src='$web_accessible_path' alt='Similarity Plot'>";
        }
    } else {
        echo "<p style='color: red;'>Error: " . $_FILES['fasta_file']['error'] . "</p>";
    }
}
?>
