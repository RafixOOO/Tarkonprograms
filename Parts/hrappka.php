<!DOCTYPE html>
<html lang="pl">
<head>
<title>Parts</title>
<meta charset ="utf-8" />
<link rel="stylesheet" href="../static/bootstrap.min.css"/>
<link rel="shortcut icon" href="../static/clipboard-data.svg">

    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .con {
            position: relative;
            height: 100vh; /* wysokość 100% okna przeglądarki */
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .fixed-button {
            position: fixed;
            top: 2.5%;
            left: 15%;
            z-index: 1000; /* zapewnia, że przycisk będzie na wierzchu */
        }
    </style>
</head>
<body>
    <div class="container-fluid con">
        <a href="main.php" class="btn btn-primary btn-lg fixed-button">PROJEKTY</a>
        <iframe src="http://10.100.100.42/work-time-register?widget_hash=488a3e4adca6545878db8ec4163c15fd" loading="lazy"></iframe>
    </div>
</body>
</html>