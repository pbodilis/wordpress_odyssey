<?php
/*
Template Name: Archives
*/
get_header(); ?>


    <div id="archives_block">
        <ul class="accordion">
            <li class="general">
                <h3>General</h3>
                <div class="content">
                    <ul>
                        <li><a href="index.php?x=browse&amp;filter=all"             title="All (<SITE_PHOTONUMBER> Photos)"> All&nbsp;(<SITE_PHOTONUMBER>) </a></li>
                        <li><a href="index.php?x=browse&amp;filter=most_commented"  title="Most commented pictures">         Most Commented                </a></li>
                        <li><a href="index.php?x=browse&amp;filter=least_commented" title="Least commented pictures">        Least Commented               </a></li>
                        <li><a href="index.php?x=browse&amp;filter=top_rated"       title="Top rated pictures">              Top Rated                     </a></li>
                        <li><a href="index.php?x=browse&amp;filter=worst_rated"     title="Worst rated pictures">            Worst Rated                   </a></li>
                    </ul>
                </div>
            </li>
            <li class="category">
                <h3>By Category</h3>
                <div class="content">
                    <CATEGORY_LINKS_AS_LIST_PAGED>
                </div>
            </li>
            <li class="archivedate">
                <h3>By Date</h3>
                <div class="content">
                    <BROWSE_MONTHLY_ARCHIVE_AS_LINK_PAGED>
                </div>
            </li>
        </ul>
        <div id="copyright">All images &copy; Pierre Bodilis</div>
    </div>
    <div id="photolist_block">
        <ul><ODYSSEY_THUMBNAILS></ul>
        <div class="clr"></div>
    </div>
    <div class="clr"></div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>