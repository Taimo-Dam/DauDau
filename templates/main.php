<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    
    <div class="content-wrapper">
        <section class="hero">
            <style> 
            .hero {
                text-align: center;
                padding: 100px 20px;
                background-color: #121212;
                background-image: url(images/background.jpg);
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                min-height: 100vh;
                transition: all 0.3s ease;
            }

            /* Adjust text responsiveness */
            .hero h1 {
                font-size: clamp(40px, 5vw, 60px);
                text-align: left;
                line-height: 1.2;
                margin-bottom: 20px;
            }

            .hero p {
                font-size: clamp(16px, 2vw, 20px);
                margin: 30px 0;
                text-align: left;
                line-height: 1.6;
                max-width: 600px;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .hero {
                    padding: 60px 20px;
                }
                
                .hero h1 br,
                .hero p br {
                    display: none;
                }
            }
            </style>
            <h1>All the Best Song <br> in One Place</h1>
            <p>On our website, you can access an amazing collection of<br>
                popular and new songs. Stream your favorite tracks in high<br>
                quality and enjoy without interruptions. Whatever your taste in<br>
                music, we have it all for you!
            </p>
            <button class="button1"><a href="#discover">Discover now</a></button>
            <button class="button2">Create Playlist</button>
        </section>
    </div>


</body>
</html>