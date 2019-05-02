<?php
if (!class_exists('MSDNewsCPT')) {
	class MSDNewsCPT {
		//Properties
		var $cpt = 'msd_news';
		//Methods
		/**
		 * PHP 4 Compatible Constructor
		 */
		public function MSDNewsCPT(){$this->__construct();}

		/**
		 * PHP 5 Constructor
		 */
		function __construct(){
			global $current_screen;
			//Actions
			add_action( 'init', array(&$this,'register_taxonomies') );
			add_action( 'init', array(&$this,'register_cpt') );
			add_action( 'init', array(&$this,'register_metaboxes') );
			//add_action('admin_head', array(&$this,'plugin_header'));
			add_action('admin_print_scripts', array(&$this,'add_admin_scripts') );
			add_action('admin_print_styles', array(&$this,'add_admin_styles') );
			add_action('admin_footer',array(&$this,'info_footer_hook') );
			// important: note the priority of 99, the js needs to be placed after tinymce loads
			add_action('admin_print_footer_scripts',array(&$this,'print_footer_scripts'),99);
			//add_action( 'template_redirect', array(&$this,'hide_single_news') );


			//Filters
			//add_filter( 'pre_get_posts', array(&$this,'custom_query') );
			add_filter( 'enter_title_here', array(&$this,'change_default_title') );
			//add_filter('template_include', array(&$this,'my_theme_redirect'),99);
			add_filter( 'genesis_attr_news', array(&$this,'custom_add_news_attr') );


			//Shortcodes
			add_shortcode('news',array(&$this,'shortcode_handler'));

			//add cols to manage panel
			add_filter( 'manage_edit-'.$this->cpt.'_columns', array(&$this,'my_edit_columns' ));
			add_action( 'manage_'.$this->cpt.'_posts_custom_column', array(&$this,'my_manage_columns'), 10, 2 );
		}


		function register_taxonomies(){

			$labels = array(
				'name' => _x( 'News categories', 'news-category' ),
				'singular_name' => _x( 'News category', 'news-category' ),
				'search_items' => _x( 'Search news categories', 'news-category' ),
				'popular_items' => _x( 'Popular news categories', 'news-category' ),
				'all_items' => _x( 'All news categories', 'news-category' ),
				'parent_item' => _x( 'Parent news category', 'news-category' ),
				'parent_item_colon' => _x( 'Parent news category:', 'news-category' ),
				'edit_item' => _x( 'Edit news category', 'news-category' ),
				'update_item' => _x( 'Update news category', 'news-category' ),
				'add_new_item' => _x( 'Add new news category', 'news-category' ),
				'new_item_name' => _x( 'New news category name', 'news-category' ),
				'separate_items_with_commas' => _x( 'Separate news categories with commas', 'news-category' ),
				'add_or_remove_items' => _x( 'Add or remove news categories', 'news-category' ),
				'choose_from_most_used' => _x( 'Choose from the most used news categories', 'news-category' ),
				'menu_name' => _x( 'News categories', 'news-category' ),
			);

			$args = array(
				'labels' => $labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_tagcloud' => false,
				'hierarchical' => true, //we want a "category" style taxonomy, but may have to restrict selection via a dropdown or something.

				'rewrite' => array('slug'=>'news-category','with_front'=>false),
				'query_var' => true,

				'capabilities' => array(
					'manage_terms' => 'manage_news_categories',
					'edit_terms' => 'manage_news_categories',
					'delete_terms' => 'manage_news_categories',
					'assign_terms' => 'edit_page',
				),
			);

			register_taxonomy( 'news_category', array($this->cpt), $args );


			$labels = array(
				'name' => _x( 'News tags', 'news-tag' ),
				'singular_name' => _x( 'News tag', 'news-tag' ),
				'search_items' => _x( 'Search news tags', 'news-tag' ),
				'popular_items' => _x( 'Popular news tags', 'news-tag' ),
				'all_items' => _x( 'All news tags', 'news-tag' ),
				'parent_item' => _x( 'Parent news tag', 'news-tag' ),
				'parent_item_colon' => _x( 'Parent news tag:', 'news-tag' ),
				'edit_item' => _x( 'Edit news tag', 'news-tag' ),
				'update_item' => _x( 'Update news tag', 'news-tag' ),
				'add_new_item' => _x( 'Add new news tag', 'news-tag' ),
				'new_item_name' => _x( 'New news tag name', 'news-tag' ),
				'separate_items_with_commas' => _x( 'Separate news tags with commas', 'news-tag' ),
				'add_or_remove_items' => _x( 'Add or remove news tags', 'news-tag' ),
				'choose_from_most_used' => _x( 'Choose from the most used news tags', 'news-tag' ),
				'menu_name' => _x( 'News tags', 'news-tag' ),
			);

			$args = array(
				'labels' => $labels,
				'public' => true,
				'show_in_nav_menus' => false,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => false,

				'rewrite' => array('slug'=>'news-tag','with_front'=>false),
				'query_var' => true,

				'capabilities' => array(
					'manage_terms' => 'manage_news_categories',
					'edit_terms' => 'manage_news_categories',
					'delete_terms' => 'manage_news_categories',
					'assign_terms' => 'edit_page',
				),
			);

			register_taxonomy( 'news_tag', array($this->cpt), $args );
		}

		function register_cpt() {

			$labels = array(
				'name' => _x( 'News', 'news' ),
				'singular_name' => _x( 'News', 'news' ),
				'add_new' => _x( 'Add New', 'news' ),
				'add_new_item' => _x( 'Add New News', 'news' ),
				'edit_item' => _x( 'Edit News', 'news' ),
				'new_item' => _x( 'New News', 'news' ),
				'view_item' => _x( 'View News', 'news' ),
				'search_items' => _x( 'Search News', 'news' ),
				'not_found' => _x( 'No news found', 'news' ),
				'not_found_in_trash' => _x( 'No news found in Trash', 'news' ),
				'parent_item_colon' => _x( 'Parent News:', 'news' ),
				'menu_name' => _x( 'News', 'news' ),
			);

			$args = array(
				'labels' => $labels,
				'hierarchical' => false,
				'description' => 'News',
				'supports' => array( 'title','editor', 'excerpt', 'author', 'thumbnail' ),
				'taxonomies' => array( 'news_category', 'news_tag' ),
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_position' => 20,

				'show_in_nav_menus' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => true,
				'has_archive' => true,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => array('slug'=>'news','with_front'=>false),
				'capability_type' => 'page',
				'menu_icon' => 'dashicons-megaphone',
			);

			register_post_type( $this->cpt, $args );
		}


		function register_metaboxes(){
			global $news_info,$multimedia_info;
			$news_info = new WPAlchemy_MetaBox(array
			(
				'id' => '_news_information',
				'title' => 'News Info',
				'types' => array($this->cpt),
				'context' => 'normal',
				'priority' => 'high',
				'template' => plugin_dir_path(dirname(__FILE__)).'/template/metabox-news.php',
				'autosave' => TRUE,
				'mode' => WPALCHEMY_MODE_EXTRACT, // defaults to WPALCHEMY_MODE_ARRAY
				'prefix' => '_news_' // defaults to NULL
			));
		}


		function add_admin_scripts() {
			global $current_screen;
			if($current_screen->post_type == $this->cpt){
			}
		}

		function add_admin_styles() {
			global $current_screen;
			if($current_screen->post_type == $this->cpt){
				wp_enqueue_style('custom_meta_css',plugin_dir_url(dirname(__FILE__)).'/css/meta.css');
			}
		}

		function print_footer_scripts()
		{
			global $current_screen;
			if($current_screen->post_type == $this->cpt){
				print '<script type="text/javascript">/* <![CDATA[ */
					jQuery(function($)
					{
						var i=1;
						$(\'.customEditor textarea\').each(function(e)
						{
							var id = $(this).attr(\'id\');
			 
							if (!id)
							{
								id = \'customEditor-\' + i++;
								$(this).attr(\'id\',id);
							}
			 
							tinyMCE.execCommand(\'mceAddControl\', false, id);
			 
						});
					});
				/* ]]> */</script>';
			}
		}

		function info_footer_hook()
		{
			global $current_screen;
			if($current_screen->post_type == $this->cpt){
				?><script type="text/javascript">
                    jQuery('#postdivrich').before(jQuery('#_news_info_metabox'));
				</script><?php
			}
		}


		function my_theme_redirect($return_template) {
			global $wp;
			if(!is_cpt($this->cpt)){
				return $return_template;
			}
			//A Specific Custom Post Type
			if(is_single() && $wp->query_vars["post_type"] == $this->cpt){
				$templatefilename = 'single-'.$this->cpt.'.php';
			} elseif (isset($wp->query_vars["news_category"])) {
				$templatefilename = 'taxonomy-news_category.php';
			} elseif (is_archive() && $wp->query_vars["post_type"] == $this->cpt) {
				$templatefilename = 'archive-'.$this->cpt.'.php';
				//A Custom Taxonomy Page
			}
			if($templatefilename) {
				if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
					$return_template = STYLESHEETPATH . '/' . $templatefilename;
				} else {
					$return_template = plugin_dir_path(dirname(__FILE__)) . 'template/' . $templatefilename;
				}
			}

			return $return_template;
		}

		function custom_query( $query ) {
			if(!is_admin()){
				if(is_page()){
					return $query;
				}
				if($query->is_main_query()) {
					$post_types = $query->get('post_type');             // Get the currnet post types in the query

					if(!is_array($post_types) && !empty($post_types))   // Check that the current posts types are stored as an array
						$post_types = explode(',', $post_types);

					if(empty($post_types))
						$post_types = array('post'); // If there are no post types defined, be sure to include posts so that they are not ignored

					if ($query->is_search) {
						$searchterm = $query->query_vars['s'];
						// we have to remove the "s" parameter from the query, because it will prevent the posts from being found
						$query->query_vars['s'] = "";

						if ($searchterm != "") {
							$query->set('meta_value', $searchterm);
							$query->set('meta_compare', 'LIKE');
						};
						$post_types[] = $this->cpt;                         // Add your custom post type

					} elseif ($query->is_archive) {
						$post_types[] = $this->cpt;                         // Add your custom post type
					}

					$post_types = array_map('trim', $post_types);       // Trim every element, just in case
					$post_types = array_filter($post_types);            // Remove any empty elements, just in case

					$query->set('post_type', $post_types);              // Add the updated list of post types to your query
				}
			}
		}


		function my_edit_columns( $columns ) {
			$mycolumns = array(
				'cb' => '<input type="checkbox" />',
				'title' => __( 'Title' ),
				$this->cpt.'_category' => __( 'Categories' ),
				$this->cpt.'_tag' => __( 'Tags' ),
			);
			$columns = array_merge($mycolumns,$columns);

			return $columns;
		}


		function my_manage_columns( $column, $post_id ) {
			global $post;

			switch( $column ) {
				/* If displaying the 'logo' column. */
				case $this->cpt.'_category' :
				case $this->cpt.'_tag' :
					$taxonomy = $column;
					if ( $taxonomy ) {
						$taxonomy_object = get_taxonomy( $taxonomy );
						$terms = get_the_terms( $post->ID, $taxonomy );
						if ( is_array( $terms ) ) {
							$out = array();
							foreach ( $terms as $t ) {
								$posts_in_term_qv = array();
								if ( 'post' != $post->post_type ) {
									$posts_in_term_qv['post_type'] = $post->post_type;
								}
								if ( $taxonomy_object->query_var ) {
									$posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
								} else {
									$posts_in_term_qv['taxonomy'] = $taxonomy;
									$posts_in_term_qv['term'] = $t->slug;
								}

								$label = esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) );
								$out[] = $this->get_edit_link( $posts_in_term_qv, $label );
							}
							/* translators: used between list items, there is a space after the comma */
							echo join( __( ', ' ), $out );
						} else {
							echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . $taxonomy_object->labels->no_terms . '</span>';
						}
					}
					break;
				default :
					break;
			}
		}

		function get_edit_link( $args, $label, $class = '' ) {
			$url = add_query_arg( $args, 'edit.php' );

			$class_html = '';
			if ( ! empty( $class ) ) {
				$class_html = sprintf(
					' class="%s"',
					esc_attr( $class )
				);
			}

			return sprintf(
				'<a href="%s"%s>%s</a>',
				esc_url( $url ),
				$class_html,
				$label
			);
		}


		function change_default_title( $title ){
			global $current_screen;
			if  ( $current_screen->post_type == $this->cpt ) {
				return __('News Name','news');
			} else {
				return $title;
			}
		}

		function cpt_display(){
			global $post;
			if(is_cpt($this->cpt)) {
				if (is_single()){
					//display content here
				} else {
					//display for aggregate here
				}
			}
		}

		function shortcode_handler($atts){
			extract(shortcode_atts( array(
				'count' => 3,
			), $atts ));
			$args = array(
				'post-type' => $this->cpt,
				'posts_per_page' => $count,
			);
			$more = '<div class="more"><a class="button" href="/news">Read More</a></div>';
			return $this->msdlab_news_special($args).$more;
		}


		function msdlab_news_special($args){
			global $post;
			$origpost = $post;
			$defaults = array(
				'posts_per_page' => 3,
				'post_type' => $this->cpt,
			);
			$args = array_merge($defaults,$args);
			//set up result array

			$results = array();
			$results = get_posts($args);
			//format result
			$i = 0;
			foreach($results AS $result){
				$post = $result;
				$i++;

				$articles = get_post_meta($post->ID,'_news_articles',1);
				$url = $articles[0]['newsurl'];
				if($url == ''){
				    $url = get_post_permalink($post->ID);
                }

				$ret[] = genesis_markup( array(
					'html5'   => '<article %s>',
					'xhtml'   => '<div class="news status-publish has-post-thumbnail entry">',
					'context' => 'news',
					'echo' => false,
				) );
				$ret[] = genesis_markup( array(
					'html5' => '<div class="wrap">',
					'xhtml' => '<div class="wrap">',
					'echo' => false,
				) );

				$ret[] = genesis_markup( array(
					'html5' => '<main>',
					'xhtml' => '<div class="main">',
					'echo' => false,
				) );

				$ret[] = genesis_markup( array(
					'html5' => '<header>',
					'xhtml' => '<div class="header">',
					'echo' => false,
				) );
				$ret[] = get_the_post_thumbnail($result->ID,'bizcard',array('itemprop'=>'image'));

				$ret[] = genesis_markup( array(
					'html5' => '</header>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$ret[] = genesis_markup( array(
					'html5' => '<content>',
					'xhtml' => '<div class="content">',
					'echo' => false,
				) );

				$ret[] = '<h3 class="entry-title" itemprop="name">'.apply_filters('the_title',$post->post_title).'</h3>';
				$ret[] = '<p class="date">'.get_the_date('F j, Y',$post->ID).'</p>';
				$ret[] = genesis_markup( array(
					'html5' => '</content>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$ret[] = genesis_markup( array(
					'html5' => '<footer>',
					'xhtml' => '<div class="footer">',
					'echo' => false,
				) );
				$ret[] = '
                           <a href="'.$url.'" class="full-cover-button"><span class="screen-reader-text">Read More</span></a>';  //add some ajax here

				$ret[] = genesis_markup( array(
					'html5' => '</footer>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$ret[] = genesis_markup( array(
					'html5' => '</main>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$ret[] = genesis_markup( array(
					'html5' => '</div>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$ret[] = genesis_markup( array(
					'html5' => '</article>',
					'xhtml' => '</div>',
					'context' => 'news',
					'echo' => false,
				) );
			}
			//return
			$post = $origpost;
			return implode("\n",$ret);
		}


		function custom_loop(){
			global $wp_query;
			$recents = $wp_query;

			if($recents->have_posts()) {
				global $post, $news_info;
				print self::custom_loop_content($recents);
				do_action('genesis_after_endwhile');
			}
		}

		function custom_loop_content($recents = false){
			if(!is_object($recents)){
				global $wp_query;
				$recents = $wp_query;
			}
			global $post,$news_info;
			$ret[] = '<section class="filtered '.$class.'">
<div class="wrap">';
//start loop
			ob_start();
			while($recents->have_posts()) {
				$recents->the_post();
				$news_info->the_meta($post->ID);
				$item = array();
				$item[] = genesis_markup( array(
					'html5'   => '<article %s>',
					'xhtml'   => '<div class="news status-publish has-post-thumbnail entry">',
					'context' => 'news',
					'echo' => false,
				) );
				$item[] = genesis_markup( array(
					'html5' => '<div class="wrap">',
					'xhtml' => '<div class="wrap">',
					'echo' => false,
				) );

				$item[] = genesis_markup( array(
					'html5' => '<main>',
					'xhtml' => '<div class="main">',
					'echo' => false,
				) );

				$item[] = genesis_markup( array(
					'html5' => '<header>',
					'xhtml' => '<div class="header">',
					'echo' => false,
				) );
				$item[] = get_the_post_thumbnail($post->ID,'bizcard',array('itemprop'=>'image'));

				$item[] = genesis_markup( array(
					'html5' => '</header>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$item[] = genesis_markup( array(
					'html5' => '<content>',
					'xhtml' => '<div class="content">',
					'echo' => false,
				) );

				$item[] = '<h3 class="entry-title" itemprop="name">'.apply_filters('the_title',$post->post_title).'</h3>';
				//$item[] = '<p class="date">'.get_the_date('F j, Y',$post->ID).'</p>';
				$item[] = genesis_markup( array(
					'html5' => '</content>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$item[] = genesis_markup( array(
					'html5' => '<footer>',
					'xhtml' => '<div class="footer">',
					'echo' => false,
				) );
				$articles = get_post_meta($post->ID,'_news_articles',1);
				$url = $articles[0]['newsurl'];
				if($url != ''){
					$item[] = '
                           <a href="' . $url . '" class="full-cover-button" target="_blank"><span class="screen-reader-text">Read More</span></a>';  //add some ajax here

				} else {
					$item[] = '
                           <a href="' . get_permalink( $post->ID ) . '" class="full-cover-button"><span class="screen-reader-text">Read More</span></a>';  //add some ajax here
				}
				$item[] = genesis_markup( array(
					'html5' => '</footer>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$item[] = genesis_markup( array(
					'html5' => '</main>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$item[] = genesis_markup( array(
					'html5' => '</div>',
					'xhtml' => '</div>',
					'echo' => false,
				) );
				$item[] = genesis_markup( array(
					'html5' => '</article>',
					'xhtml' => '</div>',
					'context' => 'news',
					'echo' => false,
				) );
				print implode("\n",$item);
			} //end loop
			$ret[] = ob_get_contents();
			ob_end_clean();
			$ret[] = '</div></section>';
			return implode( "\n", $ret );
		}


		function hide_single_news(){
			if(!is_single())
				return;
			if(get_query_var('post_type') == $this->cpt){
				global $wp_query;
				wp_redirect(get_post_meta($wp_query->post->ID,'_news_newsurl',true));
				return;
			} else {
				return;
			}
		}


		/**
		 * Callback for dynamic Genesis 'genesis_attr_$context' filter.
		 *
		 * Add custom attributes for the custom filter.
		 *
		 * @param array $attributes The element attributes
		 * @return array $attributes The element attributes
		 */
		function custom_add_news_attr( $attributes ){
			$attributes['class'] .= ' equalize col-xs-12 col-sm-6 col-md-4';
			$attributes['itemtype']  = 'http://schema.org/NewsArticle';
			// return the attributes
			return $attributes;
		}
	} //End Class
} //End if class exists statement