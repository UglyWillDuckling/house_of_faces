<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="margin: auto;text-align: center;font-size: 1.3em;">     
      
        <h2 style="font-family: fantasy;font-size: 1.5em;">Welcome to the HOUSE!</h2>
        <div class="imgContainer" style="margin: auto;">
            <img src="<?php echo $cid; ?>" alt="friends">
        </div>
        <p style="font-size: 0.93em;padding: 11px;background: #0A42E1;color: aliceblue;margin: 1% 20%;text-align: center;border-radius: 5px;font-family: sans-serif;">
            You're just one step away from becoming our member.
            Click <a href="127.0.0.1/house_of_faces/auth/activateAccount&amp;code=<?php echo $code; ?>&amp;email=<?php 
            echo $email; ?>">on this link
            </a> and your account will be immeadetly activated. 
        </p>       
    </div>    
</body>
</html>