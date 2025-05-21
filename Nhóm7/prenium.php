<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Me and You Premium </title>
</head>
<style> 
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}
.main-header {
    background-color: #333;
    color: white;
    padding: 1rem;
    text-align: center;
}
nav ul {
    list-style: none;
    padding: 0;
}
nav ul li {
    display: inline;
    margin: 0 10px;
}
nav ul li a {
    color: white;
    text-decoration: none;
}
section {
    padding: 2rem;
}
form input, form textarea, form button {
    display: block;
    margin: 10px 0;
}

</style>
<body>
    <header class="main-header">
        <h1>Welcome to Me and You Premium Website</h1>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>
    <section id="home">
        <h2>Home</h2>
        <p>Discover the premium experience.</p>
    </section>
    <section id="about">
        <h2>About Us</h2>
        <p>Learn more about our journey.</p>
    </section>
    <section id="services">
        <h2>Our Services</h2>
        <p>Quality offerings tailored for you.</p>
    </section>
    <section id="contact">
        <h2>Contact Us</h2>
        <form>
            <input type="text" placeholder="Your Name" required>
            <input type="email" placeholder="Your Email" required>
            <textarea placeholder="Your Message" required></textarea>
            <button type="submit">Send</button>
        </form>
    </section>
</body>
</html>
