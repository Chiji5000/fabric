<?php
require "./navbar.php";

$timeout_duration = 180;

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // If last activity is set and current time exceeds it by timeout, logout
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=true");
        exit();
    }
    // Update last activity time
    $_SESSION['LAST_ACTIVITY'] = time();
} else {
    // If not logged in at all
    header("Location: login.php");
    exit();
}
?>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f3f4f6;
        color: #1f2937;
    }

    .dark {
        background-color: #121212;
        color: #f0f0f0;
    }

    body.dark .contact-us {
        background-color: #121212;
        color: #f0f0f0;
    }

    body.dark .contact-item {
        background-color: #1f2937;
        color: #f0f0f0;
    }

    body.dark .contact-container {
        background-color: #1f2937;
        color: #f0f0f0;
    }

    body.dark .contact-form-container {
        background-color: #121212 !important;
        color: #f0f0f0;
    }

    .contact-container {
        display: flex;
        width: 95% !important;
        min-width: 95% !important;
    }

    .contact-left,
    .contact-right {
        flex: 1 1 45%;
        min-width: 300px;
    }


    .contact-info {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        width: 90%;
        margin-inline: auto;
        padding-top: 30px;
    }

    .contact-item {
        width: 30%;
        height: 150px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        padding: 20px;
    }

    .icon-wrapper {
        width: 55px;
        height: 55px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: white;
        border-radius: 50%;
        margin-right: 20px;
    }

    .contact-details {
        display: flex;
        flex-direction: column;
    }

    .label {
        margin-bottom: 8px;
        font-size: 14px;
    }

    .info {
        margin: 0;
        font-size: 18px;
        font-weight: bold;
    }

    .fa {
        font-size: 24px;
        color: #007bff;
        /* Use primary color for the icons */
    }

    .map-container {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 20px;
        height: 100%;
    }

    .map-wrapper {
        width: 100%;
        height: 100%;
        position: relative;
        border-radius: 10px;
        overflow: hidden;
    }

    .map-wrapper iframe {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 10px;
    }

    .hero-contact {
        background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d');
        /* Replace with your preferred image */
        background-size: cover;
        background-position: center;
        height: 60vh;
        position: relative;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-overlay {
        background-color: rgba(0, 0, 0, 0.6);
        /* Dark overlay */
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .hero-content {
        text-align: center;
        max-width: 700px;
    }

    .hero-content h1 {
        font-size: 3rem;
        margin-bottom: 20px;
        color: white;
    }

    .hero-content p {
        font-size: 1.2rem;
        line-height: 1.5;
    }

    @media (max-width: 700px) {
        .contact-info {
            display: flex;
            flex-direction: column;
        }

        .contact-item {
            width: 100%;
            height: 150px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 20px;
        }


        .contact-left .contact-right {
            width: 100% !important;
            min-width: 100% !important;
        }

        .contact-form-container .map-container {
            width: 100% !important;
            min-width: 100% !important;
        }

        .contact-form-container {
            margin-left: -15px !important;
        }

        .map-wrapper {
            width: 100% !important;
        }

        .contact-container {
            height: auto;
        }
    }
</style>

<section class="contact-us">
    <section class="hero-contact">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1>Contact Us</h1>
                <p>We'd love to hear from you. Reach out to us with your questions, feedback, or just to say hello!</p>
            </div>
        </div>
    </section>

    <div class="contact-info">
        <div class="contact-item">
            <div class="icon-wrapper">
                <i class="fa fa-map-marker-alt"></i>
            </div>
            <div class="contact-details">
                <p class="label">Address</p>
                <h5 class="info">123 Street, Warri, Delta State</h5>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon-wrapper">
                <i class="fa fa-phone-alt"></i>
            </div>
            <div class="contact-details">
                <p class="label">Call Us Now</p>
                <h5 class="info">+012 345 6789</h5>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon-wrapper">
                <i class="fa fa-envelope-open"></i>
            </div>
            <div class="contact-details">
                <p class="label">Mail Us Now</p>
                <h5 class="info">info@example.com</h5>
            </div>
        </div>
    </div>

    </div>
    <div class="contact-container">
        <div class="contact-left">
            <div class="contact-form-container" style="padding: 20px; background-color: #f8f9fa; border-radius: 8px;">
                <p style="display: inline-block; border: 1px solid rgb(21, 36, 53); border-radius: 50px; padding: 5px 20px; color:rgb(82, 83, 83); margin-top: 30px;">Contact Us</p>
                <h1 style="margin-bottom: 20px; padding-top: 20px; font-size: 40px;">Have Any Query? Please Contact Us!</h1>
                <p style="margin-bottom: 40px; font-weight: 100; opacity: 0.7;">The contact form is for enquires. Don't be shy to contact us today, we are always active to pick you calls or reply you're emails today.</p>
                <form action="" method="">
                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                        <div style="flex: 1 1 45%;">
                            <div style="position: relative; margin-bottom: 15px;">
                                <input type="text" name="name" id="name" placeholder="Your Name" style="width: 100%; padding: 10px 15px; border-radius: 5px; border: 1px solid #ced4da;">
                                <label for="name" style="position: absolute; top: -15px; left: 2px; font-size: 14px; color: #6c757d;">Your Name</label>
                            </div>
                        </div>
                        <div style="flex: 1 1 45%;">
                            <div style="position: relative; margin-bottom: 15px;">
                                <input type="email" name="email" id="email" placeholder="Your Email" style="width: 100%; padding: 10px 15px; border-radius: 5px; border: 1px solid #ced4da;">
                                <label for="email" style="position: absolute; top: -15px; left: 2px; font-size: 14px; color: #6c757d;">Your Email</label>
                            </div>
                        </div>
                        <div style="flex: 1 1 100%;">
                            <div style="position: relative; margin-bottom: 15px;">
                                <input type="text" name="subject" id="subject" placeholder="Subject" style="width: 100%; padding: 10px 15px; border-radius: 5px; border: 1px solid #ced4da;">
                                <label for="subject" style="position: absolute; top: -15px; left: 2px; font-size: 14px; color: #6c757d;">Subject</label>
                            </div>
                        </div>
                        <div style="flex: 1 1 100%;">
                            <div style="position: relative; margin-bottom: 15px;">
                                <textarea name="message" id="message" placeholder="Leave a message here" style="width: 100%; padding: 10px 15px; height: 100px; border-radius: 5px; border: 1px solid #ced4da; resize: none;"></textarea>
                                <label for="message" style="position: absolute; top: -15px; left: 2px; font-size: 14px; color: #6c757d;">Message</label>
                            </div>
                        </div>
                        <div style="flex: 1 1 100%;">
                            <button type="submit" style="width: 100%; padding: 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px;">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="contact-right">
            <div class="map-container">
                <div class="map-wrapper">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3971.354924218529!2d5.741448475026578!3d5.514225034068285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1041b2b8a8996a0b%3A0xb637ee4a02b49310!2sMain%20Market%20Warri!5e0!3m2!1sen!2sng!4v1727254693612!5m2!1sen!2sng" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();

        if (!name || !email || !subject || !message) {
            alert("All fields are required!");
            e.preventDefault();
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            e.preventDefault();
        }
    });
</script>
<?php include 'footer.php'; ?>