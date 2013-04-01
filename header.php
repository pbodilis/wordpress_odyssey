<?php
/**
 * Template Name: Header
 */

session_start();

$blog = the_core()->get_blog();
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="<?php echo $blog['html_type']; ?>; charset=<?php echo $blog['charset']; ?>" />
    <meta name="generator" content="WordPress <?php echo $blog['version']; ?>" /> <!-- leave this for stats -->
    <meta name="title" content="<?php echo $blog['title']; ?>" />

    <link rel="stylesheet" href="<?php echo $blog['stylesheet_url']; ?>" type="text/css" media="screen" />
    <link rel="alternate" type="application/rss+xml" title="<?php echo $blog['name']; ?> RSS Feed" href="<?php echo $blog['rss2_url']; ?>" />
    <link rel="alternate" type="application/rss+xml" title="<?php echo $blog['name']; ?> Comments RSS Feed" href="<?php echo $blog['comments_rss2_url']; ?>" />
    <link rel="alternate" type="application/atom+xml" title="<?php echo $blog['name']; ?> Atom Feed" href="<?php echo $blog['atom_url']; ?>" />

    <title><?php echo $blog['name'] . $blog['title']; ?></title>

    <!-- theme js -->
    <?php wp_head(); ?>
</head>
<body <?php body_class();?> >

<?php

echo the_core()->get_rendered_header_bar();

?>