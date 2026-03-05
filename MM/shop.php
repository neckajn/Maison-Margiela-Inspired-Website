<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no">

    <title>Maison Margiela</title>

    <link rel="stylesheet" href="mm1.css">
</head>
<body>
   
    <div class="mmlogo-container">
        <img src="images/mmlogo.png" alt="Logo" class="mmlogo">
    </div>

    
    <div class="background-container"></div>

    <div class="mmcalendar-container">
        <img src="images/mmcalendar.png" alt="mmcalendar">
    </div>

    <script>
        document.addEventListener('wheel', function(e) {
            if (e.ctrlKey) {
                e.preventDefault(); 
            }
        }, { passive: false });
    </script>

<a href="about.html" class="ABOUT" onclick="fadeOutPage(event)">ABOUT</a>
<a href="login.php" class="SHOP" onclick="fadeOutPage(event)">SHOP</a>
<script src="script.js" defer></script>

</body>
</html>
