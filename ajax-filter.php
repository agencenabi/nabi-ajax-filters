<?php
/*
Plugin Name: Nabi Ajax Filter
Plugin URI: https://www.agencenabi.com
Description: Filter custom posts with Ajax
Version: 1.0.1
Author: Marc-André Lavigne
Author URI: https://www.agencenabi.com
License: GPL2
*/

$installpath = pathinfo(__FILE__);

//include php files in lib folder
foreach (glob($installpath['dirname']."/lib/*.php") as $filename){
    include $filename;
}

/**
 *
 * The main class used by the plugin
 * @since 1.0
 *
 */
class NabiFilter extends NabiFilter_elements{

    /**
     *
     * The constructor
     * @since 1.0
     */
    function __construct(){
        add_shortcode('NabiFilter',array(&$this,'ajax_filter'));
        add_action('init',array(&$this,'enqueue_scripts'));

        /* the following methods are inherited from NabiFilter_elements */
        if(is_admin()){
                add_action( 'wp_ajax_nopriv_nabifilterposts',array(&$this,'create_filtered_section'));
                add_action( 'wp_ajax_nabifilterposts',array(&$this,'create_filtered_section'));
            }else{
                add_action( 'wp_ajax_nopriv_nabifilterposts',array(&$this,'create_filtered_section'));
            }
        }

    /**
     *
     * The is the method that is used by the shortcode
     * @param array $atts
     * @since 1.0
     * @return HTML
     */
    function ajax_filter($atts){ ?>
    <div id="nbfPostsTop">
    <?php
        if(!isset($atts['posttypes']))
            $posttypes = array('post');
        else
            $posttypes = explode(',',$atts['posttypes']);

        if(isset($atts['taxonomies']))
            $taxs = explode(",",$atts['taxonomies']);
        else
            $taxs = array('category');

        if(isset($atts['showcount']) && $atts['showcount']==1)
            $showCount = 1;
        else
            $showCount = 0;

        if(isset($atts['pagination']))
            $pagination = explode(",",$atts['pagination']);
        else
            $pagination = array("top","bottom");

        if(isset($atts['posts_per_page']))
            $postsPerPage = $atts['posts_per_page'];
        else
            $postsPerPage = 8;

        // TODO: Add Multiple filter selection

        if(isset($atts['filters']))
            $filters = explode(",",$atts['filters']);
        else
        	$filters = array();

        if(!isset($atts['shownav']) || $atts['shownav']=='1')
            $this->create_filter_nav($taxs,$posttypes,$showCount);
	        $this->add_inline_javascript($posttypes);
	        $this->create_loading();
	    ?>
	        <section id="ajax-filtered-section">
	            <?php $this->create_filtered_section($posttypes,$filters,$postsPerPage,$pagination);?>
	        </section>
        </div>
        <?php
    }

    /**
     *
     * method to include all required scripts
     * @since 1.0
     */
    function enqueue_scripts(){
    	wp_register_script('nbf-script', get_bloginfo('wpurl').'/wp-content/plugins/nabi-ajax-filters/js/nbf-script.js',array('jquery'),'1.0',true);
        wp_enqueue_script('nbf-script');
    }

}

// load up the class so it can be used in the theme or by other plugins
$NabiFilter = new NabiFilter();