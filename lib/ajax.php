<?php
/*
 * ajax.php
 *
 */

/**
 *
 * the core class of the plugin
 * @author Marc-André Lavigne
 *
 *
 */
class NabiFilter_elements{

    function add_inline_javascript($posttypes = array('post')){
        $qo = get_queried_object();

        //get the page's current taxonomy to filter
        if(isset($qo->term_id))
           $qoString = $qo->taxonomy."##".$qo->term_id;
        else
           $qoString = "nbf_na";
        ?>
        <script type="text/javascript">
            var NABI_CONFIG = {
            	ajaxurl: '<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php',
                posttypes: '<?php echo implode(',',$posttypes); ?>',
                qo: '<?php echo $qoString; ?>',
                thisPage: 1,
                nonce: '<?php echo esc_js(wp_create_nonce('filternonce')); ?>'
            };
        </script>
        <?php
    }

    function create_filtered_section($pt = array("post"), $filters = array(), $postPerPage=8, $paginationDisplay = array('top','bottom'), $useQO = true){
    	//$useQO refers to using the Queried Object. This is to preset certain filters if in a taxonomy page.
        if($_POST){
            check_ajax_referer('filternonce');
            $pt = explode(',',$_POST['posttypes']);
        	$f = explode('&',$_POST['filters']);
        }

        $c=0;

        if(isset($f) && $f[0] != ""){ //check that the array isn't blank
            //this while loop put the filters in a usable array
            while($c < count($f)){
                    $string = explode('=',$f[$c]);
                if(!is_array($filters[$string[0]]))
                    $filters[$string[0]] = array();
                array_push($filters[$string[0]],$string[1]);
                $c++;
            }

        }

        $args = array(
            "post_type" => $pt,
            "posts_per_page" => $postPerPage,
            "tax_query" => array(),
            "orderby" => "title",
            "order" => "ASC",
        	"post_status" => "publish"
        );

        if(!$_POST){
            $qo = get_queried_object();

            //get the page's current taxonomy to filter
            if(isset($qo->term_id) && $useQO==true){
                array_push($args['tax_query'],
                    array(
                        'taxonomy' => $qo->taxonomy,
                        'field' => 'id',
                        'terms' => $qo->term_id
                    )
                );
            }
        }else{
            if($_POST['qo'] != 'nbf_na'){
                    $qo = explode('##',$_POST['qo']);
                array_push($args['tax_query'],
                    array(
                        'taxonomy' => $qo[0],
                        'field' => 'id',
                        'terms' => $qo[1]
                    )
                );
            }
            if(isset($_POST['paged']))
                $args['paged'] = $_POST['paged'];

        }

        if(isset($_POST['paged']))
            $args['paged'] = $_POST['paged'];
        else
            $args['paged'] = 1;

        if(isset($filters)){
            //add all the filters to tax_query
            foreach($filters as $tax => $ids){
                foreach($ids as $id){
                    array_push($args['tax_query'],
                        array(
                            'taxonomy' => $tax,
                            'field' => 'id',
                            'terms' => $id
                        )
                    );
                }
            }
        }

        //inserts a relation if more than one array in the tax_query
        if(count($args['tax_query'])>1)
            $args['tax_query']['relation'] = 'AND';


            if(file_exists(get_stylesheet_directory()."/ajax-loop.php")){
                include get_stylesheet_directory()."/ajax-loop.php";
            } else {
                $i =0;
                $ajaxPostfilter = new WP_Query();
                $ajaxPostfilter->query($args);
                if(in_array("top",$paginationDisplay))
                    $this->pagination($ajaxPostfilter->found_posts, $postPerPage);
                if($ajaxPostfilter->have_posts()): while ($ajaxPostfilter->have_posts()) : $ajaxPostfilter->the_post(); ?>
                    <?php $i++; ?>

					<div class="nbf-post__brick">
						<div class="nbf-post__img">
							<a href="<?php the_permalink(); ?>" class="pagelink" title="<?php the_title(); ?>">
								<?php the_post_thumbnail(); ?>
							</a>
						</div>
						<div class="nbf-post__content">
							<p class="nbf-post__date">
								<?php echo get_the_date(); ?>
							</p>
							<h3 class="nbf-post__title">
								<?php the_title(); ?>
							</h3>
							<div class="nbf-post__excerpt">
								<?php the_excerpt(); ?>
							</div>
							<p class="nbf-post__more">
								<a href="<?php the_permalink(); ?>" class="btn btn--black pagelink" title="<?php the_title(); ?>">
									<?php _e('En savoir plus', 'nabi'); ?>
								</a>
							</p>
						</div>
					</div>

                <?php endwhile; else:
	                echo '<div class="nbf-post__brick">';
                    echo _e('Aucun résultat', 'nabi');
                    echo '</div>';
                endif;
            }

        if(in_array("bottom",$paginationDisplay))
        $this->pagination($ajaxPostfilter->found_posts, $postPerPage);
        echo "<p>Total : {$ajaxPostfilter->found_posts}</p>";

        if($_POST)
            die();
    }

    function pagination($totalPosts,$postPerPage){?>
        <nav class="pagination">
            <?php if($_POST && $_POST['paged']>1){
            	$pageNumber = $_POST['paged']; ?>
                <div class="prevPage"><a class="paginationNav" rel="prev" href="#"><?php _e('précédente', 'nabi'); ?></a></div>
            <?php } else {
				$pageNumber = 1; ?>
				<div class="prevPageDisabled"><a class="paginationDisabled"><?php _e('précédente', 'nabi'); ?></a></div>
            <?php }?>
            <div class="af-pages">
                <?php
                $p = 1;
                while($p<=ceil($totalPosts/$postPerPage)){
                	echo '<a class="nb-paginatelink-'.$p.' nb-paginatelink';
                    if($p == $pageNumber || (!$_POST && $p == 1))
                    	echo "current";
                    echo '" rel="'.$p.'">'.$p.'</a>';
                    if($p <= ceil($totalPosts/$postPerPage-1))
                        echo "";
                    $p++;
                }
                ?>
            </div>
            <?php if($postPerPage*$pageNumber<$totalPosts && $postPerPage<$totalPosts){ ?>
                <div class="nextPage"><a class="paginationNav" rel="next" href="#"><?php _e('suivante', 'nabi'); ?></a></div>
            <?php } else { ?>
                <div class="nextPageDisabled"><a class="paginationDisabled" ><?php _e('suivante', 'nabi'); ?></a></div>
            <?php }?>
        </nav>
    <?php }

    function create_filter_nav($taxs = array('category'), $posttypes= array('post'), $showCount = 1, $showTitles = 1){?>
        <nav id="ajax-filters" class="nbf-post__filters">
            <?php
            $qo = get_queried_object();

            foreach($taxs as $tax){

                $terms = get_terms( $tax, array(
                        'orderby'    => 'date',
                        'hide_empty' => 1
                    )
                );

                if($showTitles == 1){
                    $the_tax = get_taxonomy( $terms[0]->taxonomy );
                    echo "<h2>";
                    echo _e('Filtres', 'nabi');
                    echo "</h2>";
                }?>

                <ul>
	                <li class="NabiFilterItem all nbf-type-all filter-selected" data-tax=""><a href="#" class="ajax-filter-label"><span class="checkbox"></span><?php _e('Tous', 'nabi'); ?></a></li>
	                <?php
                    foreach($terms as $term){
                    	echo "<li class=\"NabiFilterItem {$term->slug} af-$tax-{$term->term_id}";
                    	if($term->term_id == $qo->term_id)
                    		echo " filter-selected";
                    	echo "\" data-tax=\"$tax={$term->term_id}\"><a href=\"#\" class=\"ajax-filter-label\"><span class=\"checkbox\"></span>{$term->name}</a></label>";
                        if($showCount==1){
                        	echo " ({$term->count})";
                        }
                        echo "</li>";
                    } ?>
                </ul>
            <?php } ?>
        </nav>
        <?php
    }

     function create_loading(){ ?>
    	<div id='ajax-loader' style="display: none">
            <!-- TODO: Add a loading animation here -->
        </div>
    <?php }

}