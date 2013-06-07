<!DOCTYPE html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>
    <meta charset="utf-8">
    <title><?php wp_title('|','true','right'); ?><?php bloginfo('name'); ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" />
    <meta name="HandheldFriendly" content="True">
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="MobileOptimized" content="320">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php
    /*
     * Wordpress Head. This is REQUIRED.Never remove this
     */
    wp_head(); ?>
</head>
<body <?php body_class(); ?> >
<div id="wrap">
    <div id="page">
        <header id="header" class="wrapper site-header-wrapper">
            <div class="container">
                <div class="row">
                    <div class="site-header">
                        <nav class="site-navigation desktop" role="navigation">
                            <?php //wpgrade_main_nav(); ?>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <div id="content">