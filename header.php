<!DOCTYPE html>
<html>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri() ?>/favicon.ico">
    <meta name="theme-color" content="#000000" />
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#000000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#000000">
    <title><?php the_title(); ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div id="top-link"></div>
    <header id="top-head">
        <a href="#top">
            <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/logo.png" alt="<?php the_title(); ?>">
            <ul class="top-menu">
                <li><a class="active" href="#top-link">HOME</a></li>
                <li><a href="#about">ABOUT</a></li>
                <li><a href="#collections">COLLECTIONS</a></li>
                <li><a href="#flymap">FLYMAP</a></li>
                <li><a href="#team">TEAM</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>
            <div class="hamburger"></div>
            <div class="mobile-pop">
                <ul class="pop-menu">
                    <li><a class="active" href="#top-link">HOME</a></li>
                    <li><a href="#about">ABOUT</a></li>
                    <li><a href="#collections">COLLECTIONS</a></li>
                    <li><a href="#flymap">FLYMAP</a></li>
                    <li><a href="#team">TEAM</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </div>
    </header>