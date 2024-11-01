<?php
/*
Plugin Name: Smart Tab Widget
Plugin URI: http://wpmagtheme.com/plugins/smart-tab-widget/
Description: Shows a tabbed widget for most popular, most commented, latest posts and tags using AJAX loading 
Author: WpMagTheme
Version: 1.0.0
Author URI: http://wpmagtheme.com/
*/
if(!class_exists('wmt_tab_widget')) {
    class wmt_tab_widget extends WP_Widget {
        function __construct() { 
        	// add image sizes and load language file
    		add_action( 'init', array(&$this, 'wmt_init') );

			// ajax functions
			add_action('wp_ajax_wmt_ajax_widget_content', array(&$this, 'wmt_ajax_widget_content'));
			add_action('wp_ajax_nopriv_wmt_ajax_widget_content', array(&$this, 'wmt_ajax_widget_content'));

			// css & js
    		add_action('wp_enqueue_scripts', array(&$this, 'wmt_register_scripts'));

    		//admin js
    		add_action('admin_enqueue_scripts', array(&$this, 'wmt_admin_scripts'));

    		$widget_ops = array('classname' => 'wmt_tab_widget', 'description' => __('Display popular posts, recent posts, comments, and tags in tabbed format.', 'wmt-tab-widget'));
			$control_ops = array('width' => 300, 'height' => 350);
			parent::__construct('wmt_tab_widget', esc_html__('Smart Tab Widget', 'wmt-tab-widget'), $widget_ops, $control_ops);

        }

        function wmt_init() {
	        load_plugin_textdomain('wmt-tab-widget', false, dirname(plugin_basename(__FILE__)) . '/languages/' ); 
	        add_image_size( 'wmt_small', 100, 75, true ); // small thumb
	        add_image_size( 'wmt_medium', 218, 150, true ); // medium thumb
	        add_image_size( 'wmt_large', 324, 235, true ); // large thumb
	    }

	    function wmt_register_scripts() { 
			// JS    
			wp_register_script('wmt_widget', plugins_url('js/smart-tab-widget.js', __FILE__), array('jquery'), false, false);     
			wp_localize_script( 'wmt_widget', 'wmt_tabs_widget',         
				array( 'ajax_url' => admin_url( 'admin-ajax.php' )) 
			);        
			// CSS     
			wp_register_style('wmt_widget', plugins_url('css/smart-tab-widget.css', __FILE__), true);
			wp_register_style('wmt_themify_icon', plugins_url('inc/themify/themify-icons.css', __FILE__), true);
	    }  
	    function wmt_admin_scripts($hook) {
	        if ($hook != 'widgets.php')
	            return;
	        wp_register_script('wmt_widget_admin', plugins_url('js/smart-tab-widget-admin.js', __FILE__), array('jquery'));  
	        wp_enqueue_script('wmt_widget_admin');

	        wp_register_style('wmt_widget_admin', plugins_url('css/smart-tab-widget-admin.css', __FILE__), true);
	        wp_enqueue_style('wmt_widget_admin');
	    }

	    function form( $instance ) { 
			$instance = wp_parse_args( (array) $instance, array( 
				'tabs' => array('recent' => 1, 'popular' => 1, 'comments' => 1, 'tags' => 0),   
				'tab_order' => array('popular' => 1, 'recent' => 2, 'comments' => 3, 'tags' => 4), 
				'pagination' => 1, 
				'pagination_type' => 'default',  
				'post_num' => '5',  
				'title_length' => '14',  
				'show_thumb' => 1, 
				'thumb_size' => 'small', 
				'show_date' => 1, 
				'show_comment_count' => 1, 
				'show_author' => 1, 
				'show_icon_meta' => 0, 
				'show_excerpt' => 0, 
				'excerpt_length' => '14', 
				'comment_num' => '5',  
				'show_avatar' => 1,  
				'post_duration' => 'all' ,
				
			) );
			
			extract($instance);

			?>
	        <div class="wmt_options_wrapper">
	        
	        <h4 class="wmt_tab_select_header wmt_tab_header"><a href="#"><?php esc_html_e('Select tabs to show', 'wmt-tab-widget'); ?></a></h4>
	        
			<div class="wmt_select_tabs" style="display: none;">
				<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px" for="<?php echo esc_html($this->get_field_id("tabs")); ?>_popular">
					<input type="checkbox" class="checkbox wmt_enable_popular" id="<?php echo esc_html($this->get_field_id("tabs")); ?>_popular" name="<?php echo esc_html($this->get_field_name("tabs")); ?>[popular]" value="1" <?php if (isset($tabs['popular'])) { checked( 1, $tabs['popular'], true ); } ?> />
					<?php esc_html_e( 'Popular Tab', 'wmt-tab-widget'); ?>
				</label>
				<label class="alignleft" style="display: block; width: 50%; margin-bottom: 5px;" for="<?php echo esc_html($this->get_field_id("tabs")); ?>_recent">
					<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("tabs")); ?>_recent" name="<?php echo esc_html($this->get_field_name("tabs")); ?>[recent]" value="1" <?php if (isset($tabs['recent'])) { checked( 1, $tabs['recent'], true ); } ?> />		
					<?php esc_html_e( 'Recent Tab', 'wmt-tab-widget'); ?>
				</label>
				<label class="alignleft" style="display: block; width: 50%;" for="<?php echo esc_html($this->get_field_id("tabs")); ?>_comments">
					<input type="checkbox" class="checkbox wmt_enable_comments" id="<?php echo esc_html($this->get_field_id("tabs")); ?>_comments" name="<?php echo esc_html($this->get_field_name("tabs")); ?>[comments]" value="1" <?php if (isset($tabs['comments'])) { checked( 1, $tabs['comments'], true ); } ?> />
					<?php esc_html_e( 'Comments Tab', 'wmt-tab-widget'); ?>
				</label>
				<label class="alignleft" style="display: block; width: 50%;" for="<?php echo esc_html($this->get_field_id("tabs")); ?>_tags">
					<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("tabs")); ?>_tags" name="<?php echo esc_html($this->get_field_name("tabs")); ?>[tags]" value="1" <?php if (isset($tabs['tags'])) { checked( 1, $tabs['tags'], true ); } ?> />
					<?php esc_html_e( 'Tags Tab', 'wmt-tab-widget'); ?>
				</label>
			</div>
	        <div class="clear"></div>
	        
	        <h4 class="wmt_tab_order_header wmt_tab_header"><a href="#"><?php esc_html_e('Order Options', 'wmt-tab-widget'); ?></a></h4>
	        
	        <div class="wmt_tab_order" style="display: none;">
	            
	            <label class="alignleft" for="<?php echo esc_html($this->get_field_id('tab_order')); ?>_popular" style="width: 50%;">
					<input id="<?php echo esc_html($this->get_field_id('tab_order')); ?>_popular" name="<?php echo esc_html($this->get_field_name('tab_order')); ?>[popular]" type="number" min="1" step="1" value="<?php echo esc_html($tab_order['popular']); ?>" style="width: 48px;" />
	                <?php esc_html_e('Popular', 'wmt-tab-widget'); ?>
	            </label>
	            <label class="alignleft" for="<?php echo esc_html($this->get_field_id('tab_order')); ?>_recent" style="width: 50%;">
					<input id="<?php echo esc_html($this->get_field_id('tab_order')); ?>_recent" name="<?php echo esc_html($this->get_field_name('tab_order')); ?>[recent]" type="number" min="1" step="1" value="<?php echo esc_html($tab_order['recent']); ?>" style="width: 48px;" />
	                <?php esc_html_e('Recent', 'wmt-tab-widget'); ?>
	            </label>
	            <label class="alignleft" for="<?php echo esc_html($this->get_field_id('tab_order')); ?>_comments" style="width: 50%;">
					<input id="<?php echo esc_html($this->get_field_id('tab_order')); ?>_comments" name="<?php echo esc_html($this->get_field_name('tab_order')); ?>[comments]" type="number" min="1" step="1" value="<?php echo esc_html($tab_order['comments']); ?>" style="width: 48px;" />
				    <?php esc_html_e('Comments', 'wmt-tab-widget'); ?>
	            </label>
	            <label class="alignleft" for="<?php echo esc_html($this->get_field_id('tab_order')); ?>_tags" style="width: 50%;">
					<input id="<?php echo esc_html($this->get_field_id('tab_order')); ?>_tags" name="<?php echo esc_html($this->get_field_name('tab_order')); ?>[tags]" type="number" min="1" step="1" value="<?php echo esc_html($tab_order['tags']); ?>" style="width: 48px;" />
				    <?php esc_html_e('Tags', 'wmt-tab-widget'); ?>
	            </label>
	        </div>
			<div class="clear"></div>
	        
	        <h4 class="wmt_post_options_header wmt_tab_header"><a href="#"><?php esc_html_e('Post Options', 'wmt-tab-widget'); ?></a></h4>
	        
	        <div class="wmt_post_options" style="display: none;">
	        <p>
				<label for="<?php echo esc_html($this->get_field_id("pagination")); ?>">				
					<input type="checkbox" class="checkbox wmt_enable_pagination" id="<?php echo esc_html($this->get_field_id("pagination")); ?>" name="<?php echo esc_html($this->get_field_name("pagination")); ?>" value="1" <?php if (isset($pagination)) { checked( 1, $pagination, true ); } ?> />
					<?php esc_html_e( 'Enable pagination', 'wmt-tab-widget'); ?>
				</label>
			</p>
			<p class="wmt_pagination_type"<?php echo (empty($pagination) ? ' style="display: none;"' : ''); ?>> 
				<label for="<?php echo esc_html($this->get_field_id('pagination_type')); ?>"><?php esc_html_e('Pagination type:', 'wmt-tab-widget'); ?></label> 
				<select id="<?php echo esc_html($this->get_field_id('pagination_type')); ?>" name="<?php echo esc_attr($this->get_field_name('pagination_type')); ?>" class="widefat pagination_type" style="width:100%;">
					<option value='default' <?php if ('default' == $instance['pagination_type']) echo 'selected="selected"'; ?>><?php esc_html_e('Default ( Next/Prev )', 'wmt-tab-widget'); ?></option>
					<option value='infinite' <?php if ('infinite' == $instance['pagination_type']) echo 'selected="selected"'; ?>><?php esc_html_e('Infinite ( Load More )', 'wmt-tab-widget'); ?></option>
				</select>
			</p>
			 
	        <p>
				<label for="<?php echo esc_html($this->get_field_id('post_num')); ?>"><?php esc_html_e('Number of posts to show:', 'wmt-tab-widget'); ?>
					<input id="<?php echo esc_html($this->get_field_id('post_num')); ?>" name="<?php echo esc_html($this->get_field_name('post_num')); ?>" type="number" min="1" step="1" value="<?php echo esc_html($post_num); ?>"   style="width:50px; margin-left: 15px"/>
				</label>
			</p> 
			<p>
				<label for="<?php echo esc_html($this->get_field_id('title_length')); ?>"><?php esc_html_e('Title length (words):', 'wmt-tab-widget'); ?>
					<input id="<?php echo esc_html($this->get_field_id('title_length')); ?>" name="<?php echo esc_html($this->get_field_name('title_length')); ?>" type="number" min="1" step="1" value="<?php echo esc_html($title_length); ?>" style="width:50px; margin-left: 15px"/>
				</label>
			</p>
			
			<p>
				<label for="<?php echo esc_html($this->get_field_id("show_thumb")); ?>">
					<input type="checkbox" class="checkbox wmt_show_thumbnails" id="<?php echo esc_html($this->get_field_id("show_thumb")); ?>" name="<?php echo esc_html($this->get_field_name("show_thumb")); ?>" value="1" <?php if (isset($show_thumb)) { checked( 1, $show_thumb, true ); } ?> />
					<?php esc_html_e( 'Show post thumbnails', 'wmt-tab-widget'); ?>
				</label>
			</p>   
			 
			<p class="wmt_thumbnail_size"<?php echo (empty($show_thumb) ? ' style="display: none;"' : ''); ?>> 
				<label for="<?php echo esc_html($this->get_field_id('thumb_size')); ?>"><?php esc_html_e('Thumbnail size:', 'wmt-tab-widget'); ?></label> 
				<select id="<?php echo esc_html($this->get_field_id('thumb_size')); ?>" name="<?php echo esc_html($this->get_field_name('thumb_size')); ?>" style="width:100px; margin-left: 15px;">
					<option value="small" <?php selected($thumb_size, 'small', true); ?>><?php esc_html_e('Small', 'wmt-tab-widget'); ?></option>
					<option value="large" <?php selected($thumb_size, 'medium', true); ?>><?php esc_html_e('Medium', 'wmt-tab-widget'); ?></option>    
					<option value="large" <?php selected($thumb_size, 'large', true); ?>><?php esc_html_e('Large', 'wmt-tab-widget'); ?></option>    
				</select>       
			</p>	
			
			<p>			
				<label for="<?php echo esc_html($this->get_field_id("show_date")); ?>">	
					<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("show_date")); ?>" name="<?php echo esc_html($this->get_field_name("show_date")); ?>" value="1" <?php if (isset($show_date)) { checked( 1, $show_date, true ); } ?> />	
					<?php esc_html_e( 'Show datetime', 'wmt-tab-widget'); ?>	
				</label>	
			</p>
	        
			<p>		
				<label for="<?php echo esc_html($this->get_field_id("show_comment_count")); ?>">		
					<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("show_comment_count")); ?>" name="<?php echo esc_html($this->get_field_name("show_comment_count")); ?>" value="1" <?php if (isset($show_comment_count)) { checked( 1, $show_comment_count, true ); } ?> />	
					<?php esc_html_e( 'Show comments count', 'wmt-tab-widget'); ?>		
				</label>	
			</p> 

			<p>		
				<label for="<?php echo esc_html($this->get_field_id("show_author")); ?>">		
					<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("show_author")); ?>" name="<?php echo esc_html($this->get_field_name("show_author")); ?>" value="1" <?php if (isset($show_author)) { checked( 1, $show_author, true ); } ?> />	
					<?php esc_html_e( 'Show Author', 'wmt-tab-widget'); ?>		
				</label>	
			</p>

			<p>		
				<label for="<?php echo esc_html($this->get_field_id("show_excerpt")); ?>">		
					<input type="checkbox" class="checkbox wmt_show_excerpt" id="<?php echo esc_html($this->get_field_id("show_excerpt")); ?>" name="<?php echo esc_html($this->get_field_name("show_excerpt")); ?>" value="1" <?php if (isset($show_excerpt)) { checked( 1, $show_excerpt, true ); } ?> />	
					<?php esc_html_e( 'Show excerpt', 'wmt-tab-widget'); ?>		
				</label>	
			</p> 
			<p class="wmt_excerpt_length"<?php echo (empty($show_excerpt) ? ' style="display: none;"' : ''); ?>> 
				<label for="<?php echo esc_html($this->get_field_id('excerpt_length')); ?>">
					<?php esc_html_e('Excerpt length (words):', 'wmt-tab-widget'); ?>   
					<input type="number" min="1" step="1" id="<?php echo esc_html($this->get_field_id('excerpt_length')); ?>" name="<?php echo esc_html($this->get_field_name('excerpt_length')); ?>" value="<?php echo esc_html($excerpt_length); ?>" style="width:50px; margin-left: 15px"/>
				</label>
			</p>	

			<p>		
				<label for="<?php echo esc_html($this->get_field_id("show_icon_meta")); ?>">		
					<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("show_icon_meta")); ?>" name="<?php echo esc_html($this->get_field_name("show_icon_meta")); ?>" value="1" <?php if (isset($show_icon_meta)) { checked( 1, $show_icon_meta, true ); } ?> />	
					<?php esc_html_e( 'Show icon post meta', 'wmt-tab-widget'); ?>		
				</label>	
			</p>    
			  
	        <div class="clear"></div> 
			</div>
			
			<div class="wmt_comment_options"<?php echo (empty($tabs['comments']) ? ' style="display: none;"' : ''); ?>> 
				<h4 class="wmt_comments_options_header wmt_tab_header"><a href="#"><?php esc_html_e('Comment Tab Options', 'wmt-tab-widget'); ?></a></h4>
				<div class="wmt_comments_option_wraper" style="display: none">
			        <p>
						<label for="<?php echo esc_html($this->get_field_id('comment_num')); ?>">
							<?php esc_html_e('Number of comments to show:', 'wmt-tab-widget'); ?> 
							<input type="number" min="1" step="1" id="<?php echo esc_html($this->get_field_id('comment_num')); ?>" name="<?php echo esc_html($this->get_field_name('comment_num')); ?>" value="<?php echo esc_html($comment_num); ?>" style="width:50px; margin-left: 15px"/>
						</label>			
					</p>      
					<p>			
						<label for="<?php echo esc_html($this->get_field_id("show_avatar")); ?>">			
							<input type="checkbox" class="checkbox" id="<?php echo esc_html($this->get_field_id("show_avatar")); ?>" name="<?php echo esc_html($this->get_field_name("show_avatar")); ?>" value="1" <?php if (isset($show_avatar)) { checked( 1, $show_avatar, true ); } ?> />
							<?php esc_html_e( 'Show avatars', 'wmt-tab-widget'); ?>	
						</label>	
					</p>
				</div>
			</div>  

			<div class="wmt_popular_options"<?php echo (empty($tabs['popular']) ? ' style="display: none;"' : ''); ?>> 
				<h4 class="wmt_popular_options_header wmt_tab_header"><a href="#"><?php esc_html_e('Popular Tab Options', 'wmt-tab-widget'); ?></a></h4>
				<div class="wmt_popular_option_wraper" style="display: none">
			        
			        <label for="<?php echo esc_html($this->get_field_id('post_duration')); ?>"><?php esc_html_e('Post Duration:', 'wmt-tab-widget'); ?></label> 
					<select id="<?php echo esc_html($this->get_field_id('post_duration')); ?>" name="<?php echo esc_html($this->get_field_name('post_duration')); ?>" style="width:100px; margin-left: 15px;">
						<option value="all" <?php selected($post_duration, 'all', true); ?>><?php esc_html_e('All time', 'wmt-tab-widget'); ?></option>
						<option value="last30" <?php selected($post_duration, 'last30', true); ?>><?php esc_html_e('Last 30 days', 'wmt-tab-widget'); ?></option>    
						<option value="last7" <?php selected($post_duration, 'last7', true); ?>><?php esc_html_e('Last 7 days', 'wmt-tab-widget'); ?></option>    
					</select> 
				</div>
			</div> 
			 
			</div> 
			<?php 
		}

		function update( $new_instance, $old_instance ) {	
			$instance = $old_instance;    
			$instance['tabs'] = $new_instance['tabs'];  
	        $instance['tab_order'] = $new_instance['tab_order'];
	        $instance['pagination'] = $new_instance['pagination'];	
	        $instance['pagination_type'] = $new_instance['pagination_type'];	
			$instance['post_num'] = $new_instance['post_num'];	
			$instance['title_length'] = $new_instance['title_length'];  
			$instance['show_thumb'] = $new_instance['show_thumb'];  
			$instance['show_icon_meta'] = $new_instance['show_icon_meta'];  
			$instance['thumb_size'] = $new_instance['thumb_size'];  
			$instance['show_date'] = $new_instance['show_date'];  
			$instance['show_comment_count'] = $new_instance['show_comment_count'];  
			$instance['show_author'] = $new_instance['show_author'];  
			$instance['show_excerpt'] = $new_instance['show_excerpt'];  
			$instance['excerpt_length'] = $new_instance['excerpt_length'];  
			$instance['comment_num'] = $new_instance['comment_num'];  
			$instance['show_avatar'] = $new_instance['show_avatar'];  
			$instance['post_duration'] = $new_instance['post_duration'];  
			return $instance;	
		}	

		function widget( $args, $instance ) {	
			extract($args);     
			extract($instance);    
			$pagination = $instance['pagination'];
			$pagination_type = $instance['pagination_type'];
			$post_num = $instance['post_num'];
			$comment_num = $instance['comment_num'];
			wp_enqueue_script('wmt_widget'); 
			wp_enqueue_style('wmt_widget');
			wp_enqueue_style('wmt_themify_icon');
			if (empty($tabs)) $tabs = array('recent' => 1, 'popular' => 1);    
			$tabs_count = count($tabs);     
			if ($tabs_count <= 1) {       
				$tabs_count = 1;       
			} elseif($tabs_count > 3) {   
				$tabs_count = 4;      
			}
			$enable_tabs = array('popular' => esc_html__('Popular', 'wmt'), 
            'recent' => esc_html__('Recent', 'wmt'), 
            'comments' => esc_html__('Comments', 'wmt'), 
            'tags' => esc_html__('Tags', 'wmt'));
            array_multisort($tab_order, $enable_tabs);
			echo wp_kses($before_widget, array( 'aside' => array( 'id' => array(), 'class' => array()))); 
			// Get lastpage popular
			if($post_duration=='last30') {
				$timequery = array( 'column'  => 'post_date', 'after'   => '- 30 days' );
			} elseif ($post_duration=='last7') {
				$timequery = array( 'column'  => 'post_date', 'after'   => '- 7 days' );
			} else { 
				$timequery = '';
			}
			$popular_query = new WP_Query( array('ignore_sticky_posts' => 1, 'posts_per_page' => $post_num, 'post_status' => 'publish', 'orderby' => 'comment_count', 'order' => 'desc', 'date_query'  => $timequery, 'paged' => 1));         
			$lastpage_popular = $popular_query->max_num_pages;  
			//Get lastpage recent
			$recent_query = new WP_Query( array('ignore_sticky_posts' => 1, 'posts_per_page' => $post_num, 'post_status' => 'publish', 'orderby' => 'post_date', 'order' => 'desc', 'paged' => 1));         
			$lastpage_recent = $recent_query->max_num_pages; 
			//Get lastpage comment
			$comment_args = apply_filters( 'wmt_comments_args', array( 'type' => 'comments', 'status' => 'approve' ));     
			$comments_total = new WP_Comment_Query();
			$comments_total_number = $comments_total->query( array_merge( array('count' => 1 ), $comment_args ) );
			$lastpage_comment = (int) ceil($comments_total_number / $comment_num);

			?>
			<div class="wmt-smart-tabs" id="<?php echo esc_html($widget_id); ?>_content" data-widget-number="<?php echo esc_attr( $this->number ); ?>">	
				<ul class="wmt-tabs-header <?php echo "wmt-col-$tabs_count"; ?> clearfix" data-lastpage-popular="<?php echo esc_html($lastpage_popular); ?>" data-lastpage-recent="<?php echo esc_html($lastpage_recent); ?>" data-lastpage-comment="<?php echo esc_html($lastpage_comment); ?>">
					<?php foreach ($enable_tabs as $tab => $label) { ?>
	                    <?php if (!empty($tabs[$tab])): ?>
	                        <li><a href="#" id="<?php echo esc_html($tab); ?>-tab"><?php echo esc_html($label); ?></a></li>	
	                    <?php endif; ?>
	                <?php } ?>   
				</ul>
				<div class="clear"></div>  
				<div class="wmt-tabs-content">
						<div class="wmt-tab-content">				
						</div> 
				
				<?php if( $pagination == 1) : ?>
			     	<?php if( $pagination_type == 'default') : ?>
			     		<div class="wmt-pagination">  
		     			 	<a href="#" class="previous"><?php esc_html_e('Prev', 'wmt'); ?></a>    
							<a href="#" class="next"><?php esc_html_e('Next', 'wmt'); ?></a>         
			     		</div> 
			     	<?php else : ?>  
					     <div class="wmt-loadmore">  
				            <a class="load-infinite" href="#"><?php esc_html_e('Load more', 'wmt'); ?></a> 
				            <div class="wmt-loading"><div class="loader"></div></div>
				        </div>  
				    <?php endif ?> 
		        <?php endif ?> 
		        <div class="wmt-tab-loading">
		        <div class="wmt-loading">
					<div class="loader"></div>
				</div>
				</div>
		        </div>
			</div>  
			<?php  
			unset($instance['tabs'], $instance['tab_order']);
			$wmt_data_args  = '
			    jQuery("#'.$widget_id.'_content").data("args", '.json_encode($instance).'); 
		  	'; 
			wp_add_inline_script( 'wmt_widget', $wmt_data_args ); 
			echo wp_kses($after_widget, array( 'aside' => array( 'id' => array(), 'class' => array())));  
		}  

		/**
		 * Get Data Ajax  
		 */
		function wmt_ajax_widget_content() { 
			$tab = sanitize_text_field($_POST['tab']);     
			$page = intval($_POST['page']);      
			if ($page < 1)        
				$page = 1;
			$post_num = intval($_POST['args']['post_num']); 
			$comment_num = intval($_POST['args']['comment_num']); 
			$post_duration = sanitize_text_field($_POST['args']['post_duration']); 
			$show_thumb = sanitize_text_field($_POST['args']['show_thumb']); 
			$thumb_size = sanitize_text_field($_POST['args']['thumb_size']); 
			$show_date = sanitize_text_field($_POST['args']['show_date']);     
			$show_comment_count = sanitize_text_field($_POST['args']['show_comment_count']);     
			$show_author = sanitize_text_field($_POST['args']['show_author']);     
			$show_excerpt = intval($_POST['args']['show_excerpt']);  
			$excerpt_length = intval($_POST['args']['excerpt_length']);
			$pagination = sanitize_text_field($_POST['args']['pagination']);
			$title_length = intval($_POST['args']['title_length']);
			$show_icon_meta = sanitize_text_field($_POST['args']['show_icon_meta']);
			$show_avatar = sanitize_text_field($_POST['args']['show_avatar']);
			// Display tabs content 
	 			switch ($tab) { 
	 				// Poppular post
	 				case "popular":  
						if($post_duration=='last30') {
							$timequery = array( 'column'  => 'post_date', 'after'   => '- 30 days' );
						} elseif ($post_duration=='last7') {
							$timequery = array( 'column'  => 'post_date', 'after'   => '- 7 days' );
						} else { 
							$timequery = '';
						}
						$popular = new WP_Query( array('ignore_sticky_posts' => 1, 'posts_per_page' => $post_num, 'post_status' => 'publish', 'orderby' => 'comment_count', 'order' => 'desc', 'date_query'  => $timequery, 'paged' => $page));         
						$last_page = $popular->max_num_pages;  
						?>
						<ul class="wmt-list-content" data-page="<?php echo esc_html($page); ?>">
						<?php     
							while ($popular->have_posts()) : $popular->the_post(); ?>
	 							<li class="clearfix">
	 								<?php if ( $show_thumb == 1 && has_post_thumbnail()) : ?>	
   	 								<div class="wmt-thumb">
   	 									<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>">	 
											<?php the_post_thumbnail('wmt_'.$thumb_size); ?> 
	                                    </a>
   	 								</div>
   	 							<?php endif; ?>	
	 								<div class="wmt-widget-title"><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo wp_trim_words(the_title(), $title_length,'...'); ?></a></div>
	 								<div class="wmt-widget-meta clearfix">
	 									<?php if( $show_date == 1) : ?>
						    			<span class="wmt-datetime-meta"><?php if( $show_icon_meta == 1) : ?><i class="ti-time"></i><?php endif ?><?php the_time( get_option('date_format') ); ?></span>
					   				<?php endif ?>
					   				<?php if( $show_comment_count == 1) : ?>
						    			<?php if ( ! post_password_required() && comments_open() ) { ?>				
											<span class="wmt-comment-meta"><?php if( $show_icon_meta == 1) : ?><i class="ti-comment"></i><?php endif ?><?php comments_popup_link( esc_html__('No Comment','wmt-tab-widget'),  esc_html__('1 Comment','wmt-tab-widget'), '% '.__('Comments','wmt-tab-widget')); ?></span>					
										<?php } ?>	
					   				<?php endif ?>
					   				<?php if( $show_author == 1) : ?>
						    			<span class="wmt-author-meta"><?php if( $show_icon_meta == 1) : ?><i class="ti-user"></i><?php endif ?><?php the_author_posts_link() ?></span>
					   				<?php endif ?> 
					   			</div>
					   			<?php if ( $show_excerpt == 1 ) : ?>	
	                                <div class="wmt_excerpt">
	                                    <p><?php echo wp_trim_words(get_the_excerpt(), $excerpt_length, '...'); ?></p>
	                                </div>
	                            <?php endif; ?>	
	 							</li>
	 						<?php endwhile; wp_reset_query(); ?>	
							</ul> 
  						<?php
  					break;
  					// Recent post
  					case "recent": 
	 					?>
	 						<ul class="wmt-list-content" data-page="<?php echo esc_html($page); ?>">
	 							<?php 
								$recent = new WP_Query( array('ignore_sticky_posts' => 1, 'posts_per_page' => $post_num, 'post_status' => 'publish', 'orderby' => 'post_date', 'order' => 'desc', 'paged' => $page));         
								$last_page = $recent->max_num_pages;      
								while ($recent->have_posts()) : $recent->the_post(); ?>
	   	 							<li class="clearfix">
	   	 								<?php if ( $show_thumb == 1 && has_post_thumbnail()) : ?>	
		   	 								<div class="wmt-thumb">
		   	 									<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>">	 
													<?php the_post_thumbnail('wmt_'.$thumb_size); ?> 
			                                    </a>
		   	 								</div>
		   	 							<?php endif; ?>	
	   	 								<div class="wmt-widget-title"><a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php echo wp_trim_words(the_title(), $title_length,'...'); ?></a></div>
	   	 								<div class="wmt-widget-meta clearfix">
	   	 									<?php if( $show_date == 1) : ?>
								    			<span class="wmt-datetime-meta"><?php if( $show_icon_meta == 1) : ?><i class="ti-time"></i><?php endif ?><?php the_time( get_option('date_format') ); ?></span>
							   				<?php endif ?>
							   				<?php if( $show_comment_count == 1) : ?>
								    			<?php if ( ! post_password_required() && comments_open() ) { ?>				
													<span class="wmt-comment-meta"><?php if( $show_icon_meta == 1) : ?><i class="ti-comment"></i><?php endif ?><?php comments_popup_link( esc_html__('No Comment','wmt-tab-widget'),  esc_html__('1 Comment','wmt-tab-widget'), '% '.__('Comments','wmt-tab-widget')); ?></span>					
												<?php } ?>	
							   				<?php endif ?>
							   				<?php if( $show_author == 1) : ?>
								    			<span class="wmt-author-meta"><?php if( $show_icon_meta == 1) : ?><i class="ti-user"></i><?php endif ?><?php the_author_posts_link() ?></span>
							   				<?php endif ?> 
							   			</div> 
							   			<?php if ( $show_excerpt == 1 ) : ?>	
			                                <div class="wmt_excerpt">
			                                    <p><?php echo wp_trim_words(get_the_excerpt(), $excerpt_length, '...'); ?></p>
			                                </div>
			                            <?php endif; ?>	
	   	 							</li>
	   	 						<?php endwhile; wp_reset_query(); ?>	
							</ul>
							
  						<?php
  					break;
  					// Comments list
  					case "comments": 
	 					?>
	 						<ul class="wmt-list-content" data-page="<?php echo esc_html($page); ?>">
								<?php 
								$comment_args = apply_filters( 'wmt_comments_args', array( 'type' => 'comments', 'status' => 'approve' ));     
								$comments_total = new WP_Comment_Query();
								$comments_total_number = $comments_total->query( array_merge( array('count' => 1 ), $comment_args ) );
								$last_page = (int) ceil($comments_total_number / $comment_num);
								$comments_query = new WP_Comment_Query();
								$offset = ($page-1) * $comment_num;
								$comments = $comments_query->query( array_merge( array( 'number' => $comment_num, 'offset' => $offset ), $comment_args ) ); 
								foreach ( $comments as $comment ) : ?>  
									<li class="clearfix"> 
									    <?php if ( $show_avatar == 1 ) : ?>                   
											<div class="wmt-comment-avatar">	
												<a href="<?php echo get_comment_link($comment->comment_ID); ?>">
													<?php echo get_avatar( $comment->comment_author_email, 61); ?>     
								                </a>				
							                </div> 
						                <?php endif; ?>	                   
					                    <div class="wmt-comment-content">  
						                    <a href="<?php echo get_comment_link($comment->comment_ID); ?>">         
												<span class="wmt-comment-author"><?php echo get_comment_author( $comment->comment_ID ); ?></span><span class="wmt-comment-in"><?php esc_html_e('in', 'wmt-tab-widget'); ?></span><?php echo wp_trim_words( get_comment_text($comment->comment_ID ), 10, '...' ); ?>
											</a> 
										</div>    
									</li>    
								<?php endforeach;  ?>  
							</ul> 
							
  						<?php
  					break;
  					// Tag 
  					case "tags":        
					?>           
					<div class="tagcloud">         
						<?php        
						$tags = get_tags(array('get'=>'all'));             
						if($tags) {               
							foreach ($tags as $tag): ?>    
								<a href="<?php echo get_term_link($tag); ?>"><?php echo esc_html($tag->name); ?></a>          
								<?php            
							endforeach;       
						} else {          
							esc_html_e('No tags created.', 'wmt-tab-widget');           
						}            
						?>           
					</div>            
					<?php            
				break; 
  				}
			wp_die(); 

		}
		 
    }
}
add_action( 'widgets_init', create_function( '', 'register_widget( "wmt_tab_widget" );' ) );

