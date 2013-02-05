<?php
/**
 * Template Name: Header
 */

session_start();
// if (isset($_REQUEST['info']) && !empty($_REQUEST['info']))
//     $_SESSION['odyssey:info'] = $_REQUEST['info'];
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
    <meta name="title" content="<?php wp_title(); ?>" />

    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Comments RSS Feed" href="<?php bloginfo('comments_rss2_url'); ?>" />
    <link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />

    <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

    <!-- theme js -->
    <?php theCore()->embedJavascript(); ?>
    <?php theCore()->embedTemplates(); ?>

    <?php wp_head(); ?>

    <script type="text/javascript">
        var imageWidth = 683;
        var imageHeight = 1024;
        var dE = document.documentElement;
    </script>

</head>
<body>

<?php

$data = array(
    'blogName'    => get_bloginfo('name'),
    'blogHomeUri' => get_bloginfo('url'),
);

theCore()->render('photoblog_header', $data);
?>