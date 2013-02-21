<?php
/**
 * Template Name: Header
 */

session_start();
// if (isset($_REQUEST['info']) && !empty($_REQUEST['info']))
//     $_SESSION['odyssey:info'] = $_REQUEST['info'];


$blog = theCore()->getBlog();
theCore()->render('photoblog_header', $blog);


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

    <title><?php echo $blog['name']; ?> <?php echo $blog['title']; ?></title>

    <!-- theme js -->
    <?php wp_head(); ?>
</head>
<body <?php body_class();?> >

<?php


//var_dump(get_pages());
get_sidebar();

?>