<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Priceless Pages</title>
    
<style>
/* General Reset */
*, *:before, *:after {
  box-sizing: border-box;
}

/* Set the body and html to fill the height of the viewport */
html, body {
  margin: 0;
  padding: 0;
  font-family: 'Arial', sans-serif;
  height: 100%;
  display: flex;
  flex-direction: column;
}

/* Ensure the main content area takes up the remaining space */
main {
  flex: 1;
}

/* Footer Styles */
.footer {
  background-color: #000; /* Dark background */
  color: #fff; /* White text */
  text-align: center;
  padding: 6px 0;
  width: 100%;
  position: relative;
  bottom: 0;
}

.footer-content {
  display: flex;
  flex-wrap: wrap;
  max-width: 1100px;
  margin: 0 auto;
  padding: 10px;
  justify-content: space-between;
  text-align: left;
}

.footer__addr {
  flex: 1 1 300px;
  margin-right: 1.25em;
}

.footer__logo {
  font-family: 'Pacifico', cursive;
  font-size: 1.5rem;
  margin-bottom: 0.5em;
  text-transform: lowercase;
}

.footer__addr address {
  font-style: normal;
  color: #999;
}

.footer__btn {
  display: inline-block;
  padding: 10px 20px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 50px;
  color: #fff;
  text-decoration: none;
  margin-top: 10px;
}

.footer__btn:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.footer__nav {
  display: flex;
  flex: 2 1 600px;
  justify-content: space-between;
  flex-wrap: wrap;
}

.nav__item {
  flex: 1 1 150px;
  margin-bottom: 1.5em;
}

.nav__title {
  font-weight: bold;
  font-size: 1.1rem;
  margin-bottom: 0.5em;
  text-transform: uppercase;
}

.nav__ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav__ul a {
  color: #999;
  text-decoration: none;
}

.nav__ul a:hover {
  text-decoration: underline;
}

.legal {
  border-top: 1px solid #333;
  margin-top: 20px;
  padding-top: 10px;
  text-align: center;
  color: #999;
}

@media (max-width: 768px) {
  .footer-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .footer_addr, .footernav, .nav_item {
    flex: 1 1 100%;
  }

  .footer__nav {
    margin-top: 20px;
  }

  .legal {
    margin-top: 20px;
  }
}

</style>
</head>
<body>
   
<!-- Footer Section -->
<footer class="footer">
    <div class="footer-content">
      <div class="footer__addr">
        <h1 class="footer__logo">Priceless Pages</h1>
        <address>
          Somewhere In. The World<br>
          <a class="footer__btn" href="mailto:example@gmail.com">Email Us</a>
        </address>
      </div>
      <ul class="footer__nav">
        <li class="nav__item">
          <h2 class="nav__title">Media</h2>
          <ul class="nav__ul">
            <li><a href="#">Online</a></li>
            <li><a href="#">Print</a></li>
            <li><a href="#">Alternative Ads</a></li>
          </ul>
        </li>
        
        <li class="nav__item">
          <h2 class="nav__title">Legal</h2>
          <ul class="nav__ul">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Use</a></li>
            <li><a href="#">Sitemap</a></li>
          </ul>
        </li>
      </ul>
    </div>
    <div class="legal">
      <p>&copy; 2024 Priceless Pages. All Rights Reserved.</p>
    </div>
  </footer>


<!-- External CSS and Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</body>
</html>
