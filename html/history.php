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
    <title>Analysis History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-image:url(https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/Picture4.png);
            background-size:cover;
        }
        .container { width: 95%; margin: auto; text-align: left; }
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
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }

        th, td {
            border: 1px solid black; /* Adds a border around each cell */
            padding: 10px;
            text-align: center;
        }

        th {
            background-color:rgb(184, 230, 136);
        }

        td {
            white-space: nowrap; /* Prevents wrapping */
        }

        tr {
            background-color:rgb(237, 255, 224);
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
    <h1>Welcome to Your Analysis History</h1>
    <button class="back-button" onclick="window.location.href='index_loged.php';">Back to Homepage</button>
    <button class="toggle-btn" onclick="toggleSidebar()">More ></button>
    
    <div class="sidebar" id="sidebar">
        <a href="javascript:void(0);" class="close-sidebar" onclick="toggleSidebar()">&lt;</a>
        <a href="retrieve_sequences.php">Retrieve Sqeuences</a>
        <a href="msa.php">Conservation Analysis</a>        
        <a href="motif.php">Motif Analysis</a>
        <a href="phylogeny.php">Phylogeny Prediction</a>
        <a href="help.php">Help</a>
        <a href="about.php">About</a>
        <a href="history.php">Analysis History</a>
        <a href="../php/logout.php">Log Out</a>
       
    </div>

    <script>
        // Function to fetch and display data
        async function fetchData() {
            try {
                let response = await fetch('../php/history.php');
                let data = await response.json();

                let tableBody = document.getElementById('history-table-body');
                tableBody.innerHTML = '';

                if (data.error) {
                    tableBody.innerHTML = `<tr><td colspan="3">${data.error}</td></tr>`;
                    return;
                }

                data.forEach(row => {
                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${row.action}</td>
                        <td><a href=${row.file_path} download = "File or Image generated">Download</a></td>                        
                        <td>${row.timestamp}</td>
                    `;
                    tableBody.appendChild(tr);
                });

            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // Fetch data when the page loads
        window.onload = fetchData;
    </script>
    <div class="container">
        <table boarder = "2">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>File or Image generated</th>                
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody id="history-table-body">
                <tr><td colspan="3">Loading...</td></tr>
            </tbody>
        </table>
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
    </script>
</body>
</html>
