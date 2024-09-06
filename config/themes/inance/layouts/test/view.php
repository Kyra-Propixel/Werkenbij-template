<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= phpb_e($page->get('title')) ?></title>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <!-- Include Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Include Owl Carousel Theme CSS (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="/phpagebuilder/config/themes/inance/public/css/bootstrap.css">
    <!-- Font Awesome style -->
    <link rel="stylesheet" type="text/css" href="/phpagebuilder/config/themes/inance/public/css/font-awesome.min.css">
    <!-- Custom styles for this template -->
    <link href="/phpagebuilder/config/themes/inance/public/css/style.css" rel="stylesheet">
    <!-- Responsive style -->
    <link href="/phpagebuilder/config/themes/inance/public/css/responsive.css" rel="stylesheet">
</head>
<body>
<div class="hero_area">
    <header class="header_section">
        <div class="header_top">
            <div class="container-fluid">
                <div class="contact_nav">
                    <a href="">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                        <span>
                            Call : +01 123455678990
                        </span>
                    </a>
                    <a href="">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <span>
                            Email : demo@gmail.com
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="header_bottom">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg custom_nav-container">
                    <a class="navbar-brand" href="index.html">
                        <span>
                            Inance
                        </span>
                    </a>
                    

                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class=""> </span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item active">
                                <a class="nav-link" href="index.html">Home <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="about.html">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="service.html">Services</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact.html">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <?= $body ?>
</div>

<!-- Include Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- Include Bootstrap JS -->
<script src="/phpagebuilder/config/themes/inance/public/js/bootstrap.js?=v1.0"></script>

<!-- Include custom JS -->
<script src="/phpagebuilder/config/themes/inance/public/js/custom.js"></script>

<!-- Run PHPageBuilder script.js files -->
<script type="text/javascript">
    document.querySelectorAll("script").forEach(function(scriptTag) {
        scriptTag.dispatchEvent(new Event('run-script'));
    });
</script>


</body>
</html>