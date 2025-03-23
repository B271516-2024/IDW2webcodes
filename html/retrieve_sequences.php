<?php
include("../php/check.php")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <meta charset="UTF-8">
    <title>Protein Sequence Retrieval</title>
    <style>
        body { font-family: Arial, sans-serif;
            background-image:url(https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/Picture4.png);
            background-size:cover;
        }
        .container { width: 50%; margin: auto; text-align: center; }
        /* Add some simple styles for the loading sign */
        #loading {
            display: none;
            color: blue;
            font-size: 18px;
            font-weight: bold;
        }
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            font-size: 14px;
            border: none;
            background-color: rgb(139, 172, 124);
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: rgb(105, 155, 108);
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: -250px;
            top: 0;
            background: rgb(78, 105, 65);
            color: white;
            padding-top: 20px;
            transition: left 0.3s;
            text-align: center;
        }
        .sidebar a, .close-sidebar {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
        }
        .sidebar a:hover, .close-sidebar:hover {
            background: rgb(54, 78, 43);
        }
        .toggle-btn {
            position: absolute;
            left: 10px;
            top: 10px;
            background: rgb(78, 105, 65);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body id="maincontent">
    <button class="toggle-btn" onclick="toggleSidebar()">More ></button>
        
        <div class="sidebar" id="sidebar">
            <a href="javascript:void(0);" class="close-sidebar" onclick="toggleSidebar()">&lt;</a>
            <a href="msa.php">Conservation Analysis</a>
            <a href="motif.php">Motif Analysis</a>
            <a href="phylogeny.php">Phylogeny Prediction</a>
            <a href="history.php">Analysis History</a>
            <a href="help.php">Help</a>
            <a href="about.php">About</a>
            <a href="credit.html">Credit</a>
            <a href="../php/logout.php">Log Out</a>
        
        </div>
    <div class="container">
        <h1>Protein Sequence Retrieval</h1>
        <button class="back-button" onclick="window.location.href='index_loged.php';">Back to Homepage</button>

        <form id="taxonomic-form">
            <label for="taxon-id">Taxon ID:</label>
            <input type="text" id="taxon-id" placeholder="Enter Taxon ID" required><br>

            <label for="protein_name">Protein Name:</label>
            <input type="text" id="protein_name" placeholder="Enter protein name" required><br>

            <button type="submit">Get Protein Sequences</button>
        </form>

        <div id="protein-sequences"></div>

        <!-- Loading sign (hidden by default) -->
        <div id="loading">Loading...</div>

        <!-- Download button for protein sequences, hidden initially -->
        <button id="download-button" style="display:none;">Download Protein Sequence</button>

        <!-- Download button for another file -->
        <button id="DownloadButton" style="display:block;">Download glucose-6-phosphatase sequences in Aves</button>

    </div>
    

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var maincontent = document.getElementById("maincontent");

            if (sidebar.style.left === "" || sidebar.style.left === "-250px") {
                sidebar.classList.remove("animate__animated", "animate__fadeOutLeft");
                sidebar.classList.add("animate__animated", "animate__fadeInLeft");
                sidebar.style.left = "0";
                maincontent.style.marginLeft = "250px";
            } else {
                sidebar.classList.remove("animate__animated", "animate__fadeInLeft");
                sidebar.classList.add("animate__animated", "animate__fadeOutLeft");
                sidebar.style.left = "-250px";
                maincontent.style.marginLeft = "0";
            }
        }
        document.getElementById('taxonomic-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const protein_name = document.getElementById('protein_name').value;
            const taxonId = document.getElementById('taxon-id').value;

            // Show the loading sign
            document.getElementById('loading').style.display = 'block';
            document.getElementById('protein-sequences').innerHTML = "";  // Clear any previous results
            document.getElementById('download-button').style.display = 'none';  // Hide the download button while loading

            // Send AJAX request to backend to retrieve protein sequences
            fetch(`../php/retrieve_sequences.php?protein_name=${protein_name}&taxonId=${taxonId}`)
                .then(response => response.json())
                .then(data => {
                    // Hide the loading sign once the data is received
                    document.getElementById('loading').style.display = 'none';

                    if (data.error) {
                        // Handle error message
                        document.getElementById('protein-sequences').innerHTML = `<p style="color: red;">${data.error}</p>`;
                        document.getElementById('download-button').style.display = 'none';  // Hide download button in case of error
                    } else if (data.file_path) {
                        // If the file path is returned, show the download button for the protein sequence
                        const file_path = data.file_path;

                        // Show the download button
                        const downloadButton = document.getElementById('download-button');
                        downloadButton.style.display = 'block';

                        // Attach an event to download the protein sequence file when clicked
                        downloadButton.onclick = function() {
                            const link = document.createElement('a');
                            const base = "https://bioinfmsc8.bio.ed.ac.uk/~s2667265/"
                            link.href = file_path;  // Use the file path returned by PHP
                            link.download = 'protein_sequences.fasta';  // Set default file name for download
                            link.style.display = 'none';  // Hide the link element
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        };
                    } else {
                        // If no file path is returned, show an error
                        document.getElementById('protein-sequences').innerHTML = `<p style="color: red;">No file path returned from server.</p>`;
                        document.getElementById('download-button').style.display = 'none';  // Hide download button in case of error
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('protein-sequences').innerHTML = "<p style='color: red;'>An error occurred while fetching the data.</p>";
                    document.getElementById('loading').style.display = 'none';  // Hide loading sign if error occurs
                });
        });

        DownloadButton.onclick = function() {
            const FilePath = 'https://bioinfmsc8.bio.ed.ac.uk/~s2667265/protein_sequences/glucose-6-phosphatase_Aves.fasta';  // Replace with actual file path
            const link = document.createElement('a');
            link.href = FilePath;  // The file path returned by PHP
            link.download = 'glucose_6_phosphatase_Aves.fasta';  // Default file name for download
            link.style.display = 'none';  // Hide the link element
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };
    </script>
</body>
</html>
