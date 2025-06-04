<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ M&U</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/css.css">
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="icon" type="image/jpg" sizes="16x16" href="../images/logo.jpg">
</head>
<body>
    <?php include('templates/header.php'); ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="main-content" id="mainContent">
        <?php include('modun/sidebar.php'); ?>
        <div class="content-wrapper">
            <?php include('templates/main.php'); ?>
            <?php include('discover.php'); ?>
        </div>
    </div>

    <?php include('templates/footer.php'); ?>

    <script src="assets/js/sidebar.js"></script>
    <script src="assets/js/discover.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initSidebar();
            initDiscover();
        });
    </script>
</body>
</html>