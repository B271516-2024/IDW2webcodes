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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phylogeny Prediction</title>
    <style>
        body { font-family: Arial, sans-serif;
            background-image:url(https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/Picture4.png);
            background-size:cover;
        }
        .container { width: 50%; margin: auto; text-align: center; }
        #loading {
            display: none;
            color: blue;
            font-size: 18px;
            font-weight: bold;
        }
        pre {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            max-height: 300px;  /* Set maximum height */
            overflow-y: auto;  /* Enable vertical scrolling */
            white-space: pre-wrap; /* Ensure the text wraps within the box */
            word-wrap: break-word; /* Break long words if necessary */
        }
        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            font-size: 14px;
            border: none;
            background-color:rgb(139, 172, 124);
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color:rgb(105, 155, 108);
        }
        .link-button {
            position: absolute;
            top: 80px;
            right: 20px;
            padding: 10px 15px;
            font-size: 14px;
            border: none;
            background-color:rgb(139, 172, 124);
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .link-button:hover {
            background-color:rgb(105, 155, 108);
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
            background:rgb(54, 78, 43);
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
        .reportSection {
            text-align: left;
        }
    </style>    
</head>
<body id="maincontent">
    <button class="toggle-btn" onclick="toggleSidebar()">More ></button>
        
        <div class="sidebar" id="sidebar">
            <a href="javascript:void(0);" class="close-sidebar" onclick="toggleSidebar()">&lt;</a>
            <a href="retrieve_sequences.php">Retrieve Sqeuences</a>
            <a href="msa.php">Conservation Analysis</a>
            <a href="motif.php">Motif Analysis</a>
            <a href="history.php">Analysis History</a>
            <a href="help.php">Help</a>
            <a href="about.php">About</a>
            <a href="credit.html">Credit</a>

            <a href="../php/logout.php">Log Out</a>
        
        </div>
    <div class="container">
        <h1>Phylogeny Prediction</h1>
        <button class="back-button" onclick="window.location.href='index_loged.php';">Back to Homepage</button>
        <button class="link-button" onclick='window.location.href="https://github.com/rambaut/figtree/releases";'>Tree editing software</button>

        <!-- File upload form -->
        <form id="analysisForm" enctype="multipart/form-data">
            <label for="fasta_file">Upload FASTA File:</label>
            <input type="file" name="fasta_file" id="fasta_file" accept=".fasta,.fa" required>
            <br><br>

            <!-- Button to set default file -->
            <button type="submit">Analyze</button>
            <button type="button" id="set-default-file">Use Default FASTA File</button>

        </form>
        
        <hr>

        <!-- Preview section -->
        <h3>FASTA File Preview:</h3>
        <pre id="fasta-preview"></pre>

        <!-- Loading sign (hidden by default) -->
        <div id="loading">Loading...</div>

        <!-- Report section -->
        <h3>Analysis Report:</h3>
        <div class="reportSection" id="reportSection" style="white-space: pre;">
            <p>No report available yet. Please upload a FASTA file and click "Analyze" or click "Use Default FASTA File".</p>
        </div>

        <!-- Download button section -->
        <div id="downloadSection" style="display:none;">
            <button id="download-report">Download the phylogeny analysis files in zip</button>
            <button id="download-tree">Download the phylogenetic tree file</button>
        </div>

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
        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOM fully loaded");

            // Handle form submission
            document.getElementById("analysisForm").addEventListener("submit", function(event) {
                event.preventDefault();                 
                
                let formData = new FormData(this);
                let fileInput = document.getElementById('fasta_file');
                let reader = new FileReader();

                reader.onload = function(event) {
                    document.getElementById("fasta-preview").textContent = event.target.result; 
                };
                reader.readAsText(fileInput.files[0]);

                document.getElementById('loading').style.display = 'block';
                
                fetch("../php/phylogeny.php", {  
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())  
                .then(data => {
                    document.getElementById('loading').style.display = 'none';

                    let reportSection = document.getElementById("reportSection");
                    let downloadSection = document.getElementById("downloadSection");
                    
                    reportSection.innerHTML = data.report;
                    downloadSection.style.display = "block";

                    document.getElementById('download-report').addEventListener('click', function() {
                        const FilePath = data.zipfile;
                        const link = document.createElement('a');
                        link.href = FilePath;
                        link.download = 'phylogeny.zip';
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });

                    document.getElementById('download-tree').addEventListener('click', function() {
                        const FilePath = data.tree_path;
                        const link = document.createElement('a');
                        link.href = FilePath;
                        link.download = 'phylogenetic_tree.treefile';
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById("analysis-result").innerHTML = 
                        "<p style='color: red;'>An error occurred.</p>";
                });
            });

            // Handle default file usage
            document.getElementById("set-default-file").addEventListener("click", function(event) {
                event.preventDefault(); 

                let formData = new FormData();
                formData.append('use_default_file', '1');

                fetch("../php/phylogeny.php", {  
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())  
                .then(data => {
                    console.log(data);
                    let reportSection = document.getElementById("reportSection");
                    let downloadSection = document.getElementById("downloadSection");

                    reportSection.innerHTML = data.report;
                    downloadSection.style.display = "block";

                    document.getElementById('download-report').addEventListener('click', function() {
                        const FilePath = data.zipfile;
                        const link = document.createElement('a');
                        link.href = FilePath;
                        link.download = 'phylogeny.zip';
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });

                    document.getElementById('download-tree').addEventListener('click', function() {
                        const FilePath = data.tree_path;
                        const link = document.createElement('a');
                        link.href = FilePath;
                        link.download = 'phylogenetic_tree.treefile';
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });

                    if (data.preview) {
                        document.getElementById("fasta-preview").textContent = data.preview;
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("analysis-result").innerHTML = 
                        "<p style='color: red;'>An error occurred.</p>";
                });
            });
        });

    </script>
</body>
</html>
