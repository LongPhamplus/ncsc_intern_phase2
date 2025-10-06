<?php
require_once __DIR__ . "/config/constants.php";
include 'includes/header.php';

?>


<!-------SEARCH BAR---------->
<section class="search_bar">
    <form class="container search_bar-container" action="<?= ROOT_URL ?>search.php" method="GET">
        <div>
            <i class="uil uil-search"></i>
            <input type="search" name="search" placeholder="Search">
        </div>
        <button type="submit" name="submit" class="btn">GO</button>
    </form>

</section>

<!-------SEARCH BAR ENDS---------->

<!-------POSTS STARTS HERE---------->

<section class="posts">
    <div class="container posts_container">
       
            <article class="post">
                <div class="post_thumbnail">
                </div>
                <div class="post_info">


                    <a href="" class="category_button"></a>
                    <h3 class="post_title"><a href="<?= ROOT_URL ?>post.php?id="></a></h3>
                    <p class="post_body">
                        awefawfasdf...
                    </p>
                    <div class="post_author">
                    
                        <div class="post_author-avatar">
                            <img src="./images/">
                        </div>
                        <div class="post_author-info">
                        <h5>By: sdafasdf</h5>
                            

                            <small>
                                asdfasdf.<br> asdfasdf
                            </small>

                        </div>
                    </div>
                </div>
            </article>
    </div>
</section>
<!-------POSTS ENDS---------->


<?php
include 'includes/footer.php';

?>