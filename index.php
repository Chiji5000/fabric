<?php
ob_start();
require "./navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$items_per_page = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR category LIKE ? LIMIT ?, ?");
    $searchTerm = "%$search%";
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $offset, $items_per_page);
} else {
    $stmt = $conn->prepare("SELECT * FROM products LIMIT ?, ?");
    $stmt->bind_param("ii", $offset, $items_per_page);
}

$stmt->execute();
$result = $stmt->get_result();

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
    :root {
        --color-red: rgb(237, 28, 36);
        --color-yellow: rgb(252, 176, 64);
        --color-green: rgb(57, 181, 74);
    }

    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f9;
        color: #333;
    }

    .container {
        padding: 40px 20px;
        text-align: center;
    }

    .welcome-message {
        font-size: 32px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    body.dark .products {
        background-color: #1e293b !important;
        color: #f0f0f0;
    }

    body.dark .category-section h3 {
        color: #f0f0f0 !important;
    }

    .products {
        padding: 20px 40px;
        background-color: #fff;
    }

    .products h2 {
        font-size: 28px;
        text-align: center;
        margin-bottom: 30px;
        color: #34495e;
        padding: 20px 0;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    body.dark .product-card {
        background-color: #113 !important;
        color: #fff;
        box-shadow: 2px 3px 5px grey;
    }

    .product-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(6, 6, 6, 0.06);
        padding: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .product-img {
        width: 100%;
        height: 200px;
        object-fit: contain;
        border-radius: 10px;
        margin-bottom: 15px;
    }


    body.dark .product-card h3,
    body.dark .product-card p {
        color: #fff;
    }

    body.dark .carousel-text {
        background-color: #111 !important;
        color: #fff;
    }

    .product-card h3 {
        font-size: 20px;
        margin: 10px 0 5px;
        color: #2c3e50;
    }

    .product-card p {
        font-size: 14px;
        color: #555;
        margin: 4px 0;
    }

    .product-card a {
        text-decoration: none;
    }

    .product-desc {
        font-size: 13px;
        color: #777;
        margin-top: 10px;
        line-height: 1.4;
    }

    .btn {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 20px;
        background-color: #3498db;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #2980b9;
    }

    @media (max-width: 600px) {
        .container {
            padding: 20px 10px;
        }

        .products {
            padding: 20px;
        }
    }

    .category-section h3 a {
        text-decoration: none;
    }

    body.dark .category-section h3 a {
        color: #f0f0f0 !important;
    }

    .carousel-container {
        display: flex;
        align-items: stretch;
        justify-content: center;
        width: 100%;
        max-width: 1000px;
        margin: 50px auto;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px grey;
        height: 600px;
    }

    .carousel-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        z-index: 0;
    }

    .carousel-img.active {
        opacity: 1;
        z-index: 1;
    }

    .carousel-image {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        position: relative;
    }

    .carousel-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: opacity 1s ease;
    }

    .carousel-text {
        flex: 1;
        padding: 40px;
        background-color: #fff;
        color: black;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: right;
    }

    .carousel-text h2 {
        font-size: 32px;
        margin-bottom: 20px;
    }

    .carousel-text p {
        font-size: 18px;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .carousel-container {
            flex-direction: column;
            width: 90% !important;
        }

        .carousel-image {
            height: 300px !important;
        }

        .carousel-img {
            height: 300px !important;
        }

        .carousel-text {
            padding: 20px;
            text-align: center;
        }

        .carousel-text h2 {
            font-size: 40px !important;
        }

        .carousel-text h3 {
            font-size: 30px !important;
            padding-bottom: 10px !important;
        }

        .carousel-text p {
            font-size: 5px !important;
        }

        .writings {
            text-align: center !important;
        }

        .writings h3 {
            text-align: center !important;
            padding: 20px 0;
        }

        #purchase_order {
            margin-top: 20px;
        }

        .gift-text h3 {
            text-align: center !important;
            padding-bottom: 20px;
        }

        .gift-text p {
            text-align: center !important;
        }
    }

    .carousel-text h2 {
        font-size: 3rem;
        font-weight: 700;
    }

    .carousel-text h2 span {
        color: var(--color-red) !important;
    }

    #mal {
        color: var(--color-yellow) !important;
    }

    .h3 span {
        color: var(--color-green);
    }

    .carousel-text h3 {
        font-size: 2rem;
        font-weight: 400;
    }

    .carousel-text p span {
        font-size: 1rem;
        font-weight: 400;
        font-style: italic;
        margin-top: 30px !important;
    }

    .welcome-message span {
        color: var(--color-green) !important;
    }

    :root {
        --color-red: #dc3545;
    }

    body.dark .gift {
        background-color: #1e293b !important;
        color: #fff;
    }

    .gift {
        background-color: white;
        margin-top: 5em;
        padding: 5em 2em;
    }

    .gift-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 2em;
        margin-bottom: 4em;
    }

    .reverse {
        flex-direction: row-reverse;
    }

    .gift-text {
        flex: 1 1 45%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .gift-text h5 span {
        font-style: italic !important;
        font-weight: 100 !important;
        opacity: 0.7;
    }

    .gift-image {
        flex: 1 1 45%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .gift-image img {
        max-width: 100%;
        height: auto;
        background-color: whitesmoke;
        padding: 2em;
    }

    .purchase {
        width: 50%;
        text-transform: uppercase;
        font-weight: 500;
        padding: 0.8em 1.2em;
        background-color: var(--color-red);
        color: white;
        border: none;
        cursor: pointer;
        margin-top: 1em;
        transition: background 0.3s;
    }

    .purchase:hover {
        background-color: #c82333;
    }

    .gift p {
        text-align: left;
        font-weight: 100 !important;
        font-style: italic;
        line-height: 1.9;
        opacity: 0.8;
    }

    .gift h3 {
        text-align: left;
        font-size: 2rem;
    }

    .gift h3 span {
        color: var(--color-red);
    }

    .shopping-updates {
        text-align: left;
        padding-top: 2em;
    }

    .shopping-updates h3 {
        font-size: 2rem;
    }

    .shopping-updates h5 {
        font-size: 1.2rem;
        line-height: 2.5rem;
        font-weight: 700;
    }

    .shopping-updates h5 span {
        font-weight: 300;
        font-style: italic;
    }

    #red {
        color: var(--color-red);
        font-style: normal;
        font-weight: 600;
    }

    .writings {
        padding: 0 2em;
    }

    /* Responsive styles */
    @media screen and (max-width: 900px) {
        .gift-row {
            flex-direction: column;
        }

        .gift-image img {
            margin-top: 2em;
            width: 100%;
            height: auto;
        }

        .purchase {
            margin: auto;
        }

        .writings {
            padding: 0 1.5em;
        }
    }

    .sponsors {
        background-color: #f8f9fa;
        padding: 4em 2em;
        text-align: center;
    }

    body.dark .sponsors {
        background-color: rgb(76, 76, 77) !important;
        color: #fff;
    }

    .sponsors h2 {
        font-size: 2rem;
        margin-bottom: 2em;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #333;
    }

    .sponsor-slider {
        overflow: hidden;
        position: relative;
        width: 100%;
    }

    .sponsor-track {
        display: flex;
        gap: 4em;
        animation: scroll 20s linear infinite;
        align-items: center;
    }

    .sponsor-track img {
        max-height: 60px;
        width: auto;
        object-fit: contain;
        filter: grayscale(100%);
        transition: filter 0.3s ease-in-out;
    }

    .sponsor-track img:hover {
        filter: grayscale(0%);
    }

    @keyframes scroll {
        0% {
            transform: translateX(0%);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    /* Responsive logo scaling */
    @media screen and (max-width: 600px) {
        .sponsor-track {
            gap: 2em;
        }

        .sponsor-track img {
            max-height: 40px;
        }
    }
</style>
</head>

<body>

    <div class="container">
        <h1 class="welcome-message">Hello, <span><?php echo htmlspecialchars($username); ?></span><span id="mal"> !</span></h1>
        <p>Welcome to your dashboard.</p>
    </div>

    <div class="carousel-container">
        <div class="carousel-image">
            <img class="carousel-img active" src="./images/img10.jpg" alt="Slide 1">
            <img class="carousel-img" src="./images/img16.jpg" alt="Slide 2">
            <img class="carousel-img" src="./images/img9.jpg" alt="Slide 3">
        </div>
        <div class="carousel-text">
            <h2>Meet <span>Mama</span> <span id="mal">Maloura</span> Herself</h2>
            <h3 class="h3">She have any <span>design</span> you want.</h3>
            <p><span>Discover premium African wrapper fabrics designed with elegance, tradition, and comfort in mind. Shop now and experience vibrant patterns and rich textures!</span></p>
        </div>
    </div>


    <section id="products" class="products">
        <h2>Our <span id="mal">Products</span></h2>

        <form method="GET" action="" style="text-align:center; margin-bottom: 30px;">
            <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; width: 300px; border-radius: 8px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 10px 20px; border: none; background-color: #3498db; color: white; border-radius: 8px; cursor: pointer;">Search</button>
        </form>

        <?php if ($search !== ''): ?>
            <section id="products" class="products">
                <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
                <div class="product-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="product-card">
                                <img class="product-img" src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
                                <p>Price: ₦<?php echo number_format($row['price']); ?></p>
                                <div class="product-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                                <a class="btn" href="product-details.php?id=<?php echo $row['id']; ?>">View Product</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align:center; font-size:18px;">No products found for "<?php echo htmlspecialchars($search); ?>"</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php
        // Get all distinct categories
        $category_sql = "SELECT DISTINCT category FROM products";
        $category_result = $conn->query($category_sql);

        while ($cat_row = $category_result->fetch_assoc()):
            $category = $cat_row['category'];
        ?>
            <div class="category-section">
                <h3 style="margin-bottom: 20px;">
                    <a href="category.php?name=<?= urlencode($category) ?>" style="margin-bottom: 50px; color: #2c3e50; font-size: 26px text-decoration: none !important;">
                        <?= htmlspecialchars(ucfirst($category)) ?>
                    </a>
                </h3>
                <div class="product-grid">
                    <?php
                    $product_sql = "SELECT * FROM products WHERE category = ?";
                    $stmt = $conn->prepare($product_sql);
                    $stmt->bind_param("s", $category);
                    $stmt->execute();
                    $product_result = $stmt->get_result();

                    while ($row = $product_result->fetch_assoc()):
                        $productId = $row['id'];
                    ?>
                        <div class="product-card">
                            <a href="product-details.php?id=<?= $productId ?>">
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>"
                                    alt="<?= htmlspecialchars($row['name']) ?>"
                                    class="product-img">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                                <p>Category: <?= htmlspecialchars($row['category']) ?></p>
                                <a href="product-details.php?id=<?= $productId ?>" class="btn">View Details</a>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <hr style="margin: 40px 0;">
        <?php endwhile; ?>

    </section>

    <section class="gift">
        <div class="thank-you">
            <div class="gift-row">
                <div class="gift-text">
                    <h3>
                        Send Someone Some '<span id="mal">Virtual</span>' <span>Love!</span>
                    </h3>
                    <p>Missed someone or just want to send a quick 'thank you' ? E-gift cards are the perfect gift!</p>
                    <p>Show your appreciation or send some love from afar.</p>
                    <p style="padding-bottom: 20px;">
                        Just enter contact info and select the date & occasion. All set!
                    </p>
                    <button class="purchase">Get Your E-Gift Card Here</button>
                </div>
                <div class="gift-image">
                    <img src="./images/Thank-You.jpg" alt="Thank You Card" />
                </div>
            </div>
        </div>

        <div class="shopping-updates">
            <div class="gift-row reverse">
                <div class="gift-image">
                    <img src="./images/Present.jpeg" alt="Present" />
                </div>
                <div class="gift-text writings">
                    <h3>Shipping Update</h3>
                    <h5>* <span id="red">PLEASE NOTE</span> * **************************</h5>
                    <h5>
                        Orders will ship within 2 business days from time of purchase -
                        <span>unless otherwise noted, i.e. pre-orders, wholesale, customized orders.</span>
                    </h5>
                    <h5>
                        <span>Tracking number will be provided upon shipping via text &/or email to the contact info provided at checkout.</span>
                    </h5>
                    <h5>
                        Please allow up to 4-7 business days from shipment notification
                        <span>to receive your order as USPS continues to experience delays.</span>
                    </h5>
                    <h5>
                        <span>Please feel free to contact us with any questions or concerns.</span>
                        We value your support and appreciate your understanding!
                    </h5>
                    <button class="purchase" id="purchase_order">Order Here</button>
                </div>
            </div>
        </div>
    </section>

    <section class="sponsors">
        <h2>Our Sponsors</h2>
        <div class="sponsor-slider">
            <div class="sponsor-track">
                <img src="./images/Apple_Pay_logo.svg.png" alt="Sponsor 1" />
                <img src="./images/bella-naija.png" alt="Sponsor 2" />
                <img src="./images/BLACK-ENTERPRISE.png" alt="Sponsor 3" />
                <img src="./images/linda-ikeji.png" alt="Sponsor 5" />
                <img src="./images/macys.png" alt="Sponsor 6" />
                <img src="./images/Meta-Logo.png" alt="Sponsor 7" />

                <!-- Repeat or duplicate to ensure smooth loop -->
                <img src="./images/Apple_Pay_logo.svg.png" alt="Sponsor 1" />
                <img src="./images/bella-naija.png" alt="Sponsor 2" />
                <img src="./images/BLACK-ENTERPRISE.png" alt="Sponsor 3" />
                <img src="./images/linda-ikeji.png" alt="Sponsor 5" />
                <img src="./images/macys.png" alt="Sponsor 6" />
                <img src="./images/Meta-Logo.png" alt="Sponsor 7" />
            </div>
        </div>
    </section>


    <?php include 'footer.php'; ?>

    <script>
        window.onload = function() {
            const images = document.querySelectorAll(".carousel-img");
            let current = 0;

            setInterval(() => {
                images[current].classList.remove("active");
                current = (current + 1) % images.length;
                images[current].classList.add("active");
            }, 3000);
        };
    </script>
</body>

</html>