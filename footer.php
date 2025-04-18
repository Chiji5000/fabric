<style>
    .dark .custom-footer {
        background-color: #1e1e1e;
        color: #ccc;
    }

    .custom-footer {
        background-color: white;
        padding: 3em 1em;
    }

    .footer-container {
        max-width: 1200px;
        margin: auto;
    }

    .footer-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 2em;
        justify-content: space-between;
        padding: 30px 0;
    }

    .footer-column {
        flex: 1 1 200px;
        padding: 30px 0 !important;
    }

    .footer-column h5 {
        margin-bottom: 1em;
        font-size: 1.1rem
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
    }

    .footer-column li {
        margin-bottom: 0.5em;
        line-height: 1.5 !important;
        font-weight: 100;
    }

    .footer-column a {
        text-decoration: none;
        color: #888;
        transition: font-weight 0.2s;
    }

    .footer-column a:hover {
        font-weight: 600;
    }

    .footer-subscribe {
        flex: 1 1 300px;
        margin-top: 24px;
    }

    .footer-subscribe h5 {
        font-size: 25px;
    }

    .footer-subscribe p {
        font-size: 15px;
    }

    .subscribe-form {
        display: flex;
        gap: 1em;
        margin: 1em 0;
    }

    .subscribe-form input {
        flex: 1;
        padding: 0.6em;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .subscribe-form button {
        padding: 0.6em 1em;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
    }

    .subscribe-form button:hover {
        background-color: #c82333;
    }

    .social-icons {
        display: flex;
        gap: 1em;
        font-size: 1.8rem;
    }

    .dark .social-icons i {
        background-color: #1e1e1e;
        color: #ccc;
    }

    .social-icons i {
        color: #333;
        transition: color 0.3s;
    }

    .dark .fa-facebook:hover {
        color: #3b5998;
    }

    .fa-facebook:hover {
        color: #3b5998;
    }

    .fa-x-twitter:hover {
        color: #1DA1F2;
    }

    .dark .fa-x-twitter:hover {
        color: #1DA1F2;
    }

    .fa-square-instagram:hover {
        color: #fccc63;
    }

    .dark .fa-square-instagram:hover {
        color: #fccc63;
    }

    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
        padding: 30px 0;
    }

    .footer-logos {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1em;
    }

    .footer-logos img {
        height: 30px;
        object-fit: contain;
    }


    @media screen and (max-width: 768px) {
        .footer-grid {
            flex-direction: column;
            text-align: center;
        }

        .subscribe-form {
            flex-direction: column;
        }

        .subscribe-form input,
        .subscribe-form button {
            width: 100%;
        }

        .footer-bottom {
            text-align: center;
            display: flex;
            flex-direction: column !important;
        }

        .footer-bottom p{
            padding: 20px 0;
        }
    }
</style>

<div class="custom-footer">
    <footer class="footer-container">
        <div class="footer-grid">
            <div class="footer-column">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="#">Search The Site</a></li>
                    <li><a href="#">More About After Pay</a></li>
                    <li><a href="#">Shipping & Handling</a></li>
                    <li><a href="#">Follow Mama Maloura</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h5>Our Conditions</h5>
                <ul>
                    <li><a href="#">Refund Policy</a></li>
                    <li><a href="#">Team Of Service</a></li>
                    <li><a href="#">Private Policy</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h5>Sections</h5>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-subscribe">
                <h5>Subscribe to our newsletter</h5>
                <p>Monthly digest of what's new and exciting from us.</p>
                <div class="subscribe-form">
                    <input type="text" placeholder="Email address" />
                    <button type="button">Subscribe</button>
                </div>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-square-instagram"></i></a>
                </div>
            </div>
        </div>

        <hr>

        <div class="footer-bottom">
            <p>&copy; 2025 Malaura okos favrics, Inc. All rights reserved.</p>
            <div class="footer-logos">
                <img src="./images/American_Express_logo_(2018).svg.png" alt="" />
                <img src="./images/Apple_Pay_logo.svg.png" alt="" />
                <img src="./images/discover.jpg" alt="" />
                <img src="./images/mastercard-icon.png" alt="" />
                <img src="./images/visa.jpg" alt="" />
                <img src="./images/Google-Pay-logo.png" alt="" />
                <img src="./images/Meta-Logo.png" alt="" />
                <img src="./images/flutterwave-big.webp" alt="" />
                <img src="./images/shop.jpg" alt="" />
            </div>
        </div>
    </footer>
</div>

</body>

</html>