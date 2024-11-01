/*
Plugin Name: Smart Tab Widget
Author: WPMagTheme
Author URI: wpmagtheme.com
*/

/**
* Cache handle
* Created: 05.23.2017
*/
var wmtCache = {};

( function () {
    "use strict";

    wmtCache = {
        data: {},
        remove: function (resource_id) {
            delete wmtCache.data[resource_id];
        },
        exist: function (resource_id) {
            return wmtCache.data.hasOwnProperty(resource_id) && wmtCache.data[resource_id] !== null;
        },
        get: function (resource_id) {
            return wmtCache.data[resource_id];
        },
        set: function (resource_id, cachedData) {
            wmtCache.remove(resource_id);
            wmtCache.data[resource_id] = cachedData;
        }
    };
})();

/**
* Ajax handle
* Created: 05.10.2017
*/

var wmtTabAjax = {};

( function() {
    "use strict"; 

    wmtTabAjax = { 

 
        wmtAjaxTabsRequest: function(container, tab_name, page, lastpage, args, user_event) {  
            var container = jQuery(container);  
            var requestData = {
                action: 'wmt_ajax_widget_content', 
                page: page,
                tab: tab_name,
                args: args
            };

            if(args.pagination_type == 'infinite') {
                if (user_event == 'select_tab') {
                     
                    wmtTabAjax.wmtInfiniteHandle(container, page, lastpage);
                    // Load Cache 
                    var currentDataCache = JSON.stringify(requestData); 
                    if ( wmtCache.exist(currentDataCache) ) { 
                        wmtTabAjax.wmtAjaxTabsCacheBeforeLoad(container);
                        wmtTabAjax.wmtAjaxTabsCacheLoad(wmtCache.get(currentDataCache), container, user_event ); 
                        return;
                    }
                }
               
            } else {
                 // Disable/Enable next and previous button 
                wmtTabAjax.wmtNavPrevHandle(container, page);
                wmtTabAjax.wmtNavNextHandle(container, page, lastpage);  
                
                // Load Cache 
                var currentDataCache = JSON.stringify(requestData); 
                if ( wmtCache.exist(currentDataCache) ) { 
                    wmtTabAjax.wmtAjaxTabsCacheBeforeLoad(container);
                    wmtTabAjax.wmtAjaxTabsCacheLoad(wmtCache.get(currentDataCache), container, user_event ); 
                    return;
                }
            }

            wmtTabAjax.wmtAjaxTabsBeforeLoad(container, user_event);
            jQuery.ajax({
                url: wmt_tabs_widget.ajax_url,
                type: 'post', 
                data: requestData,
                cache: true,
                success: function( result, textStatus, XMLHttpRequest ) { 
                    if (user_event != 'infinite') {
                        wmtCache.set(currentDataCache, result); 
                        wmtTabAjax.wmtAjaxTabsProcess(result, container, user_event);
                    } else {
                        wmtTabAjax.wmtAjaxTabsProcessInfinite(result, container);
                        wmtTabAjax.wmtInfiniteHandle(container, page, lastpage);
                    }
                }
            }); 
        },  

        wmtNavPrevHandle: function(container, page) {     
             if (page > 1) {
                container.find( '.wmt-pagination .previous' ).addClass('previous-visible');
             }
             else {
                container.find( '.wmt-pagination .previous' ).removeClass('previous-visible');
             }
        },

        wmtNavNextHandle: function(container, page, lastpage) {    
             if (page != lastpage) {
                container.find( '.wmt-pagination .next' ).addClass('next-visible');
             }
             else {
                container.find( '.wmt-pagination .next' ).removeClass('next-visible');
             } 
        },
        wmtInfiniteHandle: function(container, page, lastpage) {     
            if (page == lastpage) {
                container.find( '.wmt-loadmore' ).addClass('disabled');
            } 
            else {
                container.find( '.wmt-loadmore' ).removeClass('disabled');
            } 
        },

        wmtAjaxTabsCacheLoad: function(result, container, user_event) {    
            container.find( '.wmt-tab-content' ).html( result );
            switch(user_event) {
                case 'next': 
                    container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 wmt_fadeInRight');
                    break;
                case 'prev':
                    container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 wmt_fadeInLeft');
                    break;

                case 'select_tab':
                    container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 wmt_fadeInDown');
                    break; 
            } 
            setTimeout( function() {
                container.find( '.wmt-tab-content' ).removeClass('block_inner_overflow');
                container.find( '.wmt-tab-content' ).css('height', 'auto');
            },200);
        },
        wmtAjaxTabsCacheBeforeLoad: function(container) {  
                 container.find( '.wmt-tab-content' ).removeClass('wmt_fadeInDown wmt_fadeInLeft wmt_fadeInRight wmt_animated_8'); 
                 container.find( '.wmt-tab-content' ).addClass('block_inner_overflow');
                 var ContentHeight = container.find( '.wmt-tab-content' ).innerHeight();
                 container.find( '.wmt-tab-content' ).css('height', ContentHeight);
        },

        wmtAjaxTabsProcess: function(result, container, user_event ) { 

            container.find( '.wmt-tab-loading .wmt-loading' ).fadeOut(300);
            container.find( '.wmt-tab-content' ).removeClass('wmt_animated_5 fadeOut1');

            container.find( '.wmt-tab-content' ).html( result ); 
            switch(user_event) {
                case 'next': 
                    container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 wmt_fadeInRight');
                    break;
                case 'prev':
                    container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 wmt_fadeInLeft');
                    break;

                case 'select_tab':
                    container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 wmt_fadeInDown');
                    break; 
            }
            setTimeout( function() {
               container.find( '.wmt-tab-content' ).removeClass('block_inner_overflow');
               container.find( '.wmt-tab-content' ).css('height', 'auto');
            },200); 
        }, 
        wmtAjaxTabsProcessInfinite: function(result, container ) {  
            container.find( '.wmt-loadmore a' ).css('opacity','1');
            container.find( '.wmt-loadmore .wmt-loading' ).fadeOut(300);  
            container.find( '.wmt-tab-content' ).append( jQuery(result).addClass('wmt_animated_5 wmt_fadeInDown'));  

        },

        wmtAjaxTabsBeforeLoad: function(container, user_event) {  
            if (user_event != 'infinite') {
                container.find( '.wmt-tab-content' ).removeClass('wmt_animated_5 wmt_fadeInDown wmt_fadeInLeft wmt_fadeInRight');
                container.find( '.wmt-tab-loading .wmt-loading' ).fadeIn(300); 
                container.find( '.wmt-tab-content' ).addClass('wmt_animated_5 fadeOut1');
                var ContentHeight = container.find( '.wmt-tab-content' ).innerHeight();
                container.find( '.wmt-tab-content' ).css('height', ContentHeight);
            }
            else {
                container.find( '.wmt-loadmore a' ).css('opacity','0');
                container.find( '.wmt-loadmore .wmt-loading' ).fadeIn(300);
            }
           
        },   

    };
})();

jQuery(document).ready(function() {  
    jQuery('.wmt-smart-tabs').each(function() {
        var $this = jQuery(this); 
        var widget_id = this.id;
        var args = $this.data('args'); 
        var lastpage_popular = $this.find( '.wmt-tabs-header' ).data('lastpage-popular'); 
        var lastpage_recent = $this.find( '.wmt-tabs-header' ).data('lastpage-recent'); 
        var lastpage_comment = $this.find( '.wmt-tabs-header' ).data('lastpage-comment'); 
        
        //Tabs header
        $this.on ( "click", ".wmt-tabs-header a", function( event ) {
            event.preventDefault();   
            jQuery(this).parent().addClass('selected').siblings().removeClass('selected');
            var tab_name = this.id.slice(0, -4); 
            if (tab_name == 'popular') {
                lastpage = lastpage_popular;
                $this.find( '.wmt-pagination' ).show();
            } else if (tab_name == 'recent') {
                lastpage = lastpage_recent;
                $this.find( '.wmt-pagination' ).show();
            } else if (tab_name == 'comments'){
                lastpage = lastpage_comment;
                $this.find( '.wmt-pagination' ).show();
            } else if (tab_name == 'tags'){
                lastpage = 1;
                $this.find( '.wmt-pagination' ).hide();
            }
            jQuery(this).data('lastpage', lastpage);
            jQuery(this).data('tab_name', tab_name);
            wmtTabAjax.wmtAjaxTabsRequest($this, tab_name, 1, lastpage, args, 'select_tab'); 
        }); 

        //Next/Prev
        $this.on ( "click", ".wmt-pagination a", function( event ) {
            event.preventDefault();
            var $this_nav = jQuery(this);     
            lastpage = $this.find( '.wmt-tabs-header .selected a' ).data('lastpage'); 
            tab_name = $this.find( '.wmt-tabs-header .selected a' ).data('tab_name'); 
            var page = parseInt($this.find( '.wmt-tabs-content .wmt-list-content' ).data('page')); 
             if ($this_nav.hasClass('next')) { 
                wmtTabAjax.wmtAjaxTabsRequest($this, tab_name, page + 1, lastpage, args, 'next'); 
            } else {
                wmtTabAjax.wmtAjaxTabsRequest($this, tab_name, page - 1, lastpage, args, 'prev');
            }
            
        });

        //Loadmore
        $this.on ( "click", ".wmt-loadmore .load-infinite", function( event ) {
            event.preventDefault();   
            lastpage = $this.find( '.wmt-tabs-header .selected a' ).data('lastpage'); 
            tab_name = $this.find( '.wmt-tabs-header .selected a' ).data('tab_name');   
            var page = parseInt($this.find( '.wmt-tabs-content .wmt-list-content:last-child' ).data('page'));  
            wmtTabAjax.wmtAjaxTabsRequest($this, tab_name, page + 1, lastpage, args, 'infinite');  
            
        });
        $this.find('.wmt-tabs-header a').first().click();
    });
}); 
