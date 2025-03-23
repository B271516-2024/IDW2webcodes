<?php

ob_start();

session_start();
$user_id = $_SESSION['userid'];

include("record.php");

// Get parameters from the request
$protein_name = $_GET['protein_name'];
$taxonId = $_GET['taxonId'];

$default_protein_name = "glucose-6-phosphatase";
$default_taxonid = "Aves";

// Validate input (simple example, you may want to improve it)
if (empty($protein_name) || empty($taxonId)) {
    echo json_encode(['error' => 'Taxon or protein name is missing.']);
    exit;
}

if (empty($user_id)) {
    echo json_encode(['error' => 'session failed']);
    exit;
}

// Call the Python script to retrieve sequences
$command = escapeshellcmd("python3 /home/s2667265/public_html/python/retrieve_sequences.py " . escapeshellarg($taxonId) . " " . escapeshellarg($user_id) . " " . escapeshellarg($protein_name));

exec($command, $output, $return_var);

// Check if the file was created
if ($return_var === 0) {
    // Retrieve the path of the generated file (from the Python response)
    $response = implode("\n", $output);

    if (isset($response)) {
        
        // Serve the file for download
        if (file_exists($response)) {

            $download_path = str_replace("/home/s2667265/public_html", "https://bioinfmsc8.bio.ed.ac.uk/~s2667265", $response);
            echo json_encode(['file_path' => $download_path]);
            $action = "retrieve protein sequences of " . $protein_name . " in " . $taxonId;
            record_user_history($download_path, $action);

            exit;
        } else {
            echo json_encode(['error' => 'File not found.']);
        }
    } else {
        echo json_encode(['error' => 'Error generating file.']);
    }
} else {
    echo json_encode(['error' => 'Failed to execute the Python script. Output: ' . implode("\n", $output)]);
}
ob_end_flush();
?>
