<?php
session_start();
$user_id = $_SESSION['userid'];
header('Content-Type: application/json');
include("record.php");
set_time_limit(0);
// Initialize variables
$uploaded_file = "";
$preview = "";
$analysis_result = "";
$plot_image = "";

$default_fasta_file = '/localdisk/home/s2667265/public_html/uploads/glucose_6_phosphatase_Aves.fasta';
$default_mainreport = "/localdisk/home/s2667265/public_html/uploads/glucose_6_phosphatase_Aves.iqtree";

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

        $basename = basename($_FILES['fasta_file']['name']);

        // Run the Python analysis script
        $command = escapeshellcmd("python3 /localdisk/home/s2667265/public_html/python/phylogeny.py " . escapeshellarg($file_to_analyze) . " " . escapeshellarg($basename) . " " . escapeshellarg($user_id));
        //$command .= " > /uploads/phylogeny_output.log 2>&1 &"; // Run in the background
        $output = shell_exec($command . " 2>&1");
        //$data = implode("\n", $output);
        $data = json_decode($output, true);
        
        if ($data === null) {
            echo "JSON decoding error: " . json_last_error_msg();
            exit;
        }
        
        if (isset($data["tree_file"], $data["mainreport"], $data["zipfile"])) {
            $tree_path = $data["tree_file"];
            $report = $data["mainreport"];
            $zipfile = $data["zipfile"];
        } else {
            echo "Error: Missing expected keys in JSON output.";
            exit;
        }
        $action = "Phylogeny tree biult, based on " . basename($_FILES['fasta_file']['name']);
        record_user_history($zipfile, $action);
        
    } elseif (isset($_POST['use_default_file']) && $_POST['use_default_file'] == '1') {
        // No file uploaded, use the default FASTA file
        $file_to_analyze = $default_fasta_file;
        $preview = file_get_contents($file_to_analyze);
        $report = file_get_contents($default_mainreport);
        $tree_path = "https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/glucose_6_phosphatase_Aves.treefile";
        $zipfile = "https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/iqtree_results.zip";
    } else {
        echo "<p style='color: red;'>Error: " . $_FILES['fasta_file']['error'] . "</p>";
    }


    // Split the output by newlines into an array
    //$output_line = implode("\n", $output);
    $response = [
        "preview" => $preview,
        "report" => $report,
        "tree_path" => $tree_path,
        "zipfile" => $zipfile
    ];

    echo json_encode(value: $response);

    //if (!empty($plot_image)) {
    //    echo "<h2>Similarity Plot</h2>";
    //    echo "<img src='$web_accessible_path' alt='Similarity Plot'>";
    //} 
}
?>