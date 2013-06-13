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
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container-fluid">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="<?php bloginfo('url') ?>"><?php bloginfo('name'); ?></a>
                    <div class="nav-collapse collapse">
<!--                        <p class="navbar-text pull-right">-->
<!--                            Logged in as <a href="#" class="navbar-link">Username</a>-->
<!--                        </p>-->
                        <ul class="nav">
                            <li class="active"><a href="<?php bloginfo('url') ?>">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row-fluid">
                <div class="span3">
                    <div class="well sidebar-nav">
                        <ul class="nav nav-list">
                            <li class="nav-header">Pixelgrade Themes</li>
                            <li class="active"><a href="#">Senna</a></li>
                            <li><a href="#">Buy</a></li>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">Demo</a></li>
                            <li class="nav-header">Swipe</li>
                            <li><a href="#">Buy</a></li>
                            <li><a href="#">Documentation</a></li>
                            <li><a href="#">Demo</a></li>
                        </ul>
                    </div><!--/.well -->
                </div><!--/span-->