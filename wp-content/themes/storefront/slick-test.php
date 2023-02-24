<?php
/* Template name: Slick test */
get_header();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"  referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"  referrerpolicy="no-referrer" />

<div class="main">
  <div class="slider slider-for">
    <div><h3>1</h3></div>
    <div><input type="file" name="files[]" id="galleryImages" multiple>
	</div>
  </div>
</div>

<style>
body{
  background:#ccc;
}
.main {
  font-family:Arial;
  width:500px;
  display:block;
  margin:0 auto;
}
h3 {
    background: #fff;
    color: #3498db;
    font-size: 36px;
    line-height: 100px;
    margin: 10px;
    padding: 2%;
    position: relative;
    text-align: center;
}
.action{
  display:block;
  margin:100px auto;
  width:100%;
  text-align:center;
}
.action a {
  display:inline-block;
  padding:5px 10px; 
  background:#f30;
  color:#fff;
  text-decoration:none;
}
.action a:hover{
  background:#000;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" referrerpolicy="no-referrer"></script>

<script>
jQuery(document).ready(function(){
	jQuery('.slider-for').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		fade: true
	});


	jQuery('#galleryImages').on('change', function() {
	
    $this = jQuery(this);
		var files = event.target.files;
		form_data = new FormData();
	
    file_data = $this.prop('files');

		for (let i = 0; i < files.length; i++) {
        form_data.append('file[]', file_data[i]);
    }
        
    form_data.append('action', 'file_upload');

      console.log(form_data);
  
      jQuery.ajax({
          url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
          type: 'POST',
          contentType: false,
          processData: false,
          data: form_data,
          success: function (response) {
              $this.val('');
              //window.location.reload();
              console.log(response);
          }
      });
	});
  

});
</script>


<?php get_footer(); ?>