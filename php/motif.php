<?php
session_start();
$user_id = $_SESSION['userid'];
header('Content-Type: application/json');
include("record.php");
// Initialize variables
$uploaded_file = "";
$preview = "";
$analysis_result = "";
$plot_image = "";

$default_fasta_file = '/localdisk/home/s2667265/public_html/uploads/glucose_6_phosphatase_Aves.fasta';
$default_report = "/localdisk/home/s2667265/public_html/uploads/report_motif.txt";

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['fasta_file']) && $_FILES['fasta_file']['error'] == 0) {
        // Fetch the uploaded file's details
        $uploaded_file = $_FILES['fasta_file']['tmp_name'];
        $upload_dir = "/localdisk/home/s2667265/public_html/userdata/" . $user_id . "/";   // Directory where files will be saved

        // Save the file to the "uploads" directory
        $file_to_analyze = $upload_dir . basename($_FILES['fasta_file']['name']);
        move_uploaded_file($uploaded_file, $file_to_analyze);

        // Preview the FASTA file (only first 500 characters for display)
        $preview = file_get_contents($file_to_analyze);

        // Run the Python analysis script
        $command = escapeshellcmd("python3 /localdisk/home/s2667265/public_html/python/motif.py " . escapeshellarg($file_to_analyze) . " " . escapeshellarg($user_id));
        $output = exec($command);
        $data = json_decode($output, true);
        if ($data){
            $motifs_path = $data["file_path"];
            $report = $data["report"];
        }
        $action = "Motif analysis on " . basename($_FILES['fasta_file']['name']);
        record_user_history($motifs_path, $action);
        
    } elseif (isset($_POST['use_default_file']) && $_POST['use_default_file'] == '1') {
        // No file uploaded, use the default FASTA file
        $file_to_analyze = $default_fasta_file;
        $preview = file_get_contents($file_to_analyze);
        $report = file_get_contents($default_report);
        $motifs_path = "https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/motifs";
    } else {
        echo "<p style='color: red;'>Error: " . $_FILES['fasta_file']['error'] . "</p>";
    }


    // Split the output by newlines into an array
    //$output_line = implode("\n", $output);
    $response = [
        "preview" => $preview,
        "report" => $report,
        "motifs_path" => $motifs_path
    ];

    echo json_encode(value: $response);

    //if (!empty($plot_image)) {
    //    echo "<h2>Similarity Plot</h2>";
    //    echo "<img src='$web_accessible_path' alt='Similarity Plot'>";
    //} 
}
?>
