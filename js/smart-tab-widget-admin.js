/*
Plugin Name: Smart Tab Widget
Author: WPMagTheme
Author URI: wpmagtheme.com
Version: 1.0
*/

jQuery(document).on('click', function(e) {
    var $this = jQuery(e.target);
    var $container = $this.closest('.wmt_options_wrapper');
    
    if ($this.is('.wmt_tab_select_header a')) {
        e.preventDefault();
        var $content = $container.find('.wmt_select_tabs');
        $content.slideToggle();
    }
    else if ($this.is('.wmt_comments_options_header a')) {
        e.preventDefault();
        var $content = $container.find('.wmt_comments_option_wraper');
        $content.slideToggle();
    }
    else if ($this.is('.wmt_enable_popular')) {
        var $content = $container.find('.wmt_popular_options');
        var val = $this.is(':checked');
        if (val) {
            $content.slideDown();
        } else {
            $content.slideUp();
        }
    } 
    else if ($this.is('.wmt_enable_comments')) {
        var $content = $container.find('.wmt_comment_options');
        var val = $this.is(':checked');
        if (val) {
            $content.slideDown();
        } else {
            $content.slideUp();
        }
    } 
    else if ($this.is('.wmt_enable_pagination')) {
        var $content = $container.find('.wmt_pagination_type');
        var val = $this.is(':checked');
        if (val) {
            $content.slideDown();
        } else {
            $content.slideUp();
        }
    }else if ($this.is('.wmt_show_thumbnails')) {
        var $content = $container.find('.wmt_thumbnail_size');
        var val = $this.is(':checked');
        if (val) {
            $content.slideDown();
        } else {
            $content.slideUp();
        }
    } else if ($this.is('.wmt_show_excerpt')) {
        var $content = $container.find('.wmt_excerpt_length');
        var val = $this.is(':checked');
        if (val) {
            $content.slideDown();
        } else {
            $content.slideUp();
        }
    } else if ($this.is('.wmt_tab_order_header a')) {
        e.preventDefault();
        var $content = $container.find('.wmt_tab_order');
        $content.slideToggle();
    } else if ($this.is('.wmt_post_options_header a')) {
        e.preventDefault();
        var $content = $container.find('.wmt_post_options');
        $content.slideToggle();
    } else if ($this.is('.wmt_popular_options_header a')) {
        e.preventDefault();
        var $content = $container.find('.wmt_popular_option_wraper');
        $content.slideToggle();
    }
});