<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'storefront_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked in to storefront_footer action
			 *
			 * @hooked storefront_footer_widgets - 10
			 * @hooked storefront_credit         - 20
			 */
			do_action( 'storefront_footer' );
			?>

		</div><!-- .col-full -->
	</footer><!-- #colophon -->

	<?php do_action( 'storefront_after_footer' ); ?>

</div><!-- #page -->

<script>
	jQuery(".rtcl-widget-search-sortable-wrapper .rtcl-category-search").prop('required',true);


	(function($) {
    var queryString = window.location.search;
	
    // Parse the query string and get the value of the 'id' parameter
    var params = new URLSearchParams(queryString);
	
    var def_category = params.get('rtcl_category');
    var ad_type = params.get('filters[ad_type]');
    var def_location = params.get('rtcl_location');
    var loc = location.href; 
	
    // If the last char is a slash trim it, otherwise return the original loc
    loc = loc.lastIndexOf('/') == (loc.length -1) ? loc.substring(0,loc.length-1) : loc.substring(0,loc.lastIndexOf('/'));
    var targetValue = loc.substring(loc.lastIndexOf('/') + 1);
    if(targetValue != 'listing' && def_category == null){
        def_category = targetValue;
    }

    $('.rtcl-category-search').prop("required", true);

    $('.ui-checkbox').each(function(){
        if($(this).is(":checked")){
            $(this).parent('.ui-link-tree-item').addClass("checked");
        }
    });

    $('.pagination').find('.page-item').find('a.page-link').each(function(){
        var current_origin = window.location.origin;
        var current_pathname = window.location.pathname;
        var current_location = current_origin + current_pathname;
        var old_url = $(this).attr('href');
        var new_url;
        var urlstring;
        if(old_url != undefined){
        
            var pageno = old_url.split('page/').pop();
            pageno = pageno.split('/')[0];
        }

        var listing_page = params.get('listing-page');
        if(listing_page){
            urlstring = removeURLParameter(queryString, 'listing-page');
        }
        else{
            urlstring = queryString
        }
        
        if(urlstring != ""){
            
            if(isNaN(pageno)){
                new_url = current_location + urlstring;
            }else{
                new_url = current_location + urlstring+"&listing-page="+pageno;
                
            }
        }else{
            if(isNaN(pageno)){
                new_url = current_location;
            }else{
                new_url = current_location + "?listing-page="+pageno;
                
            }
        }

        $(this).attr('href', new_url);
        
    });

    function removeURLParameter(url, parameter) {
        //prefer to use l.search if you have a location/link object
        var urlparts = url.split('?');   
        if (urlparts.length >= 2) {
    
            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);
    
            //reverse iteration as may be destructive
            for (var i = pars.length; i-- > 0;) {    
                //idiom for string.startsWith
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                    pars.splice(i, 1);
                }
            }
    
            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    }


    


    $('.rtcl-location ').val(def_location);

    $(document).ready(function() {
        
       
        var selectEl;
        var selected_category;

        // Show categories having atleast a single post 
        var taxonomy;
        taxonomy = 'rtcl_category';
        // console.log(taxonomy);

        change_category_options();

        function change_category_options(){
            $.ajax({
                type : "post",
                dataType : "html",
                url : OBJ.ajaxurl,
                data : { 
                    action: "show_categories_having_post_ajax",
                    taxonomy_name: taxonomy,
                    def_category: def_category
                },
                success: function(data) {
                    //console.log(data);
                    $('[id^="rtcl-category-search-"]').html(data);
                    if(def_category != ' '){
                        change_type_options();
                    }
                }
            });
        }
        

        function change_type_options(){
            var selectedOption = $('.rtcl-category-search').find('option:selected');
            var selected_category = selectedOption.data('id');
            selectEl = $('[id^="rtcl-search-type-"]');
            $.ajax({
                type : "post",
                dataType : "html",
                url : OBJ.ajaxurl,
                data : { 
                    action: "sub_type_handle_ajax",
                    selected_category: selected_category,
                    ad_type: ad_type
                },
                success: function(data) {
                   // console.log(data);
                    $(document).find('[id^="rtcl-search-type-"]').html(data);
                }
            });
        }

		$('.rtcl-category-search').on("change", function(){
            change_type_options();   
        });

    });
})(jQuery);

</script>

<?php wp_footer(); ?>

</body>
</html>
