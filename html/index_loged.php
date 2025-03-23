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
    <title>Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-image:url(https://bioinfmsc8.bio.ed.ac.uk/~s2667265/uploads/Picture4.png);
            background-size:cover;
        }
        .button-container {
            margin-top: 20px;
        }
        .button {
            padding: 15px 25px;
            font-size: 16px;
            margin: 10px;
            border: none;
            background-color:rgb(139, 172, 124);
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .button:hover {
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
    </style>
</head>
<body id="maincontent">
    <h1>Welcome to the Homepage</h1>

    <div class="button-container">
        <button class="button" onclick="window.location.href='retrieve_sequences.php';">Obtain Protein Sequences</button>
        <button class="button" onclick="window.location.href='msa.php';">Conservation Analysis</button>
        <button class="button" onclick="window.location.href='motif.php';">Motif Analysis</button>
        <button class="button" onclick="window.location.href='phylogeny.php';">Phylogeny Prediction</button>
    </div>

    <button class="toggle-btn" onclick="toggleSidebar()">More ></button>
    
    <div class="sidebar" id="sidebar">
        <a href="javascript:void(0);" class="close-sidebar" onclick="toggleSidebar()">&lt;</a>
        <a href="history.php">Analysis History</a>
        <a href="help.php">Help</a>
        <a href="about.php">About</a>
        <a href="credit.html">Credit</a>
        <a href="../php/logout.php">Log Out</a>
       
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
