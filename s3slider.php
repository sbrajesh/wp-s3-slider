<?php
/*
 * Plugin name: Wordpress S3 Slider Widget
 * Author: Brajesh Singh
 * Plugin URI: http://buddydev.com/plugins/wordpress-s3-slider-widget/
 * Version: 1.0.1
 * Last Updated: Feb 13, 2011
 * Description: This plugin uses s3 slider by http://www.serie3.info/s3slider/. Please Note, s3 slider is licensed under Creative Commons Attribution 2.5.
 */
class BPMag_Slideshow_Widget extends WP_Widget{

  function bpmag_slideshow_widget() {
		parent::WP_Widget( false, $name = __( 'WP S3 Slider Widget', 'bpmag' ) );
	}

	function widget($args, $instance) {
		extract( $args );

		 echo $before_widget;
                 if(!empty($instance['title']))
                    echo $before_title. $instance['title']. $after_title;
                 else if(function_exists("bpmag_get_theme_option"))
                     echo "<div class=\"widget-content\">";//allow hiding of title in bpmag
               //noww check which slideshow is available and output that
                do_action("bpmag_s3_content");
                //if ( function_exists('show_nivo_slider') )
                  //  show_nivo_slider();
                $inc_list=BPMag_Slideshow_Widget::find_included_cats($instance);
                $included=join(",",$inc_list);
		BPMag_Slideshow_Widget::show_posts($included,$instance);
                 // BPMag_Slideshow_Widget::show_posts();

		 echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {

            $instance=$old_instance;
//$instance["num_links"]=intval($new_instance["num_links"]);
$instance["num_posts"]=intval($new_instance["num_posts"]);
$instance["title"]=$new_instance["title"];
$instance["overlay_position"]=$new_instance["overlay_position"];
$instance["height"]=$new_instance["height"];
$instance["width"]=$new_instance["width"];
$instance["timeout"]=$new_instance["timeout"];


$cat_ids=get_all_category_ids();
foreach($cat_ids as $cat_id)
  $instance["cat_id_".$cat_id]=$new_instance["cat_id_".$cat_id];

return $instance;
	}

function form($instance){
    $instance=wp_parse_args((array)$instance,array("title"=>"S3 Slideshow","num_posts"=>5,"timeout"=>4000,"overlay_position"=>"bottom","width"=>620,"height"=>260));
    $title=strip_tags($instance["title"]);
    $timeout=intval($instance["timeout"]);
    $height=intval($instance["height"]);
    $width=intval($instance["width"]);
    $num_posts=intval($instance["num_posts"]);
    $overlay_position=$instance["overlay_position"];

    
    $cat_ids=get_all_category_ids();?>
        <p>
           <label for="slide-widget-title"><?php _e('Title:', 'bpmag'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( stripslashes( $title ) ); ?>" />
            </label>
        </p>

	<p>
            <label for="slide-widget-max-posts"><?php _e( 'Maximum No. of Posts to show' , 'bpmag'); ?>
                <input type="text" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" class="widefat" value="<?php echo esc_attr( absint( $num_posts ) ); ?>" />
            </label>
	</p>

        <p>
            <label for="slide-widget-overlay-position"><?php _e( 'Overlay Posiion' , 'bpmag'); ?>
                <select  name="<?php echo $this->get_field_name( 'overlay_position' ); ?>" id="<?php echo $this->get_field_id( 'overlay_position' ); ?>"">
                  <option  class="widefat" value="left" <?php if($overlay_position=="left"):?> selected="selected" <?php endif;?>>Left</option>
                 <option class="widefat" value="right" <?php if($overlay_position=="right"):?> selected="selected" <?php endif;?> >Right</option>
                <option class="widefat" value="top" <?php if($overlay_position=="top"):?> selected="selected" <?php endif;?> >Top</option>
                 <option class="widefat" value="bottom" <?php if($overlay_position=="bottom"):?> selected="selected" <?php endif;?> >Bottom</option>
                </select>
            </label>
	</p>

         <p>
            <label for="slide-widget-max-posts"><?php _e( 'Transition delay(In mili seconds)' , 'bpmag'); ?>
                <input type="text" id="<?php echo $this->get_field_id( 'timeout' ); ?>" name="<?php echo $this->get_field_name( 'timeout' ); ?>" class="widefat" value="<?php echo esc_attr( absint( $timeout ) ); ?>" />
            </label>
	</p>

        <p>
            <label for="slide-widget-image-width"><?php _e( 'Image Width' , 'bpmag'); ?>
                <input type="text" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" class="widefat" value="<?php echo esc_attr( absint( $width ) ); ?>" />px
            </label>
	</p>
        <p>
            <label for="slide-widget-max-posts"><?php _e( 'Image height' , 'bpmag'); ?>
                <input type="text" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" class="widefat" value="<?php echo esc_attr( absint( $height ) ); ?>" />px
            </label>
	</p>
         <p>
            <label for="featured-posts-widget-id"><?php _e( 'Check Categories to Include' , 'bpmag'); ?> </label>
    <?php
	// $cats=get_categories();
   //print_r($cats);
       foreach($cat_ids as $cat_id){//show the form
    	$name=$this->get_field_name( 'cat_id_'.$cat_id );
		$opt_id=$this->get_field_id( 'cat_id_'.$cat_id );
		$checked=0;
		$key='cat_id_'.$cat_id ;
		//print_r($instance);
		if(array_key_exists($key,$instance)&&$instance[$key ]=="yes")
			$checked=true;
	?>
	<label  style="padding:5px;display:block;float:left;">
	<input type="checkbox" name="<?php  echo $name; ?>" id="<?php $opt_id;?>" value="yes" <?php if($checked) echo "checked='checked'" ;?>/>
	<?php echo get_cat_name($cat_id);?>
	</label>



<?php
   }
   ?>

        </p>
		<?php
//for showing the form code
}


//helper function to find the excluded categories
function  find_included_cats($instance){
//instance of current width
$cat_ids=get_all_category_ids();
$included=array();
foreach($cat_ids as $cat_id){
	$key="cat_id_".$cat_id;
	if(array_key_exists($key,$instance)&&$instance[$key]=="yes")
		$included[]=$cat_id;
	}
return $included;

}


function show_posts($categories,$instance){?>
    <div id="slider">

<?php

$rp=new WP_Query();

$rp->query( array('cat'=>$categories,'posts_per_page'=>$instance['num_posts'],'meta_key'=>'_thumbnail_id' ));

         if( $rp->have_posts() ) :?>
         <ul id="sliderContent">
         <?php   while( $rp->have_posts() ) : $rp->the_post(); ?>
       
	<?php if(has_post_thumbnail()) : ?>
            <li class="sliderImage">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">

                    <img src="<?php  echo bp_s3_get_custom_image_url(get_the_ID(),$instance['width'],$instance['height'] );?>"  alt="<?php the_title_attribute();?>" />
                    
                </a>
                    <div  class="<?php echo $instance['overlay_position'];?>"><strong><?php the_title();?></strong><br /><?php the_excerpt();?></div>
            </li>
	<?php endif;endwhile; ?>
             </ul>
	<?php  endif;?>
	
       
        <script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery('#slider').s3Slider({
            timeOut: <?php echo $instance['timeout'];?>
        });
    });
</script>
        
    </div>
<?php
}
}

function bpmag_addons_register_s3_widget() {
	add_action('widgets_init', create_function('', 'return register_widget("BPMag_Slideshow_Widget");') );
       }
add_action( 'plugins_loaded', 'bpmag_addons_register_s3_widget' );

add_action("wp_print_scripts","bpmag_enque_s3_slider");
function bpmag_enque_s3_slider(){
    $plugin_url=plugin_dir_url(__FILE__);
    wp_enqueue_script("s3_slider",$plugin_url."s3Slider.js",array("jquery"));
}

add_action("wp_print_styles","bpmag_enque_s3_css");
function bpmag_enque_s3_css(){
    $plugin_url=plugin_dir_url(__FILE__);
    wp_enqueue_style("s3_slider",$plugin_url."s3.css");
}

///from  thanks to Shawn fo Pointing to the ticket and Victor for the code

/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */

function bp_s3_get_custom_image_url($post_id,$width,$height,$crop=true){
    $meta_key="slide_image".$height."x".$width;//we have a unique key like slide_image200x300;

    $image_url=get_post_meta($post_id, $meta_key, true);
    if(!empty ($image_url))
        return $image_url;
    //if we are here, the image was not generated and stored earlier, right
    $thumb = get_post_thumbnail_id($post_id);
    $image = vt_resize( $thumb,'' , $width, $height, $crop);
    if(!empty($image)){
       $image_url=$image['url'];
       update_post_meta($post_id, $meta_key, $image_url);//store for later use

    }

    //if $image_ur, return it otherwise single post thumbnail
    if($image_url)
        return $image_url;
    //else
      //  return get_the_post_thumbnail( $post_id, 'single-post-thumbnail');
    
}

if(!function_exists("vt_resize")):
function vt_resize( $attach_id = null, $img_url = null, $width=200, $height=200, $crop = false ) {

	// this is an attachment, so we have the ID
	if ( $attach_id ) {

		$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
		$file_path = get_attached_file( $attach_id );

	// this is not an attachment, let's use the image url
	} else if ( $img_url ) {

		$file_path = parse_url( $img_url );
		$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

		//$file_path = ltrim( $file_path['path'], '/' );
		//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

		$orig_size = getimagesize( $file_path );

		$image_src[0] = $img_url;
		$image_src[1] = $orig_size[0];
		$image_src[2] = $orig_size[1];
	}

	$file_info = pathinfo( $file_path );
	$extension = '.'. $file_info['extension'];

	// the image path without the extension
	$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

	$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

	// checking if the file size is larger than the target size
	// if it is smaller or the same size, stop right here and return
	if ( $image_src[1] > $width || $image_src[2] > $height ) {

		// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
		if ( file_exists( $cropped_img_path ) ) {

			$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

			$vt_image = array (
				'url' => $cropped_img_url,
				'width' => $width,
				'height' => $height
			);

			return $vt_image;
		}

		// $crop = false
		if ( $crop == false ) {

			// calculate the size proportionaly
			$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
			$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

			// checking if the file already exists
			if ( file_exists( $resized_img_path ) ) {

				$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $resized_img_url,
					'width' => $proportional_size[0],
					'height' => $proportional_size[1]
				);

				return $vt_image;
			}
		}

		// no cache files - let's finally resize it
		$new_img_path = image_resize( $file_path, $width, $height, $crop );
		$new_img_size = getimagesize( $new_img_path );
		$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

		// resized output
		$vt_image = array (
			'url' => $new_img,
			'width' => $new_img_size[0],
			'height' => $new_img_size[1]
		);

		return $vt_image;
	}

	// default output - without resizing
	$vt_image = array (
		'url' => $image_src[0],
		'width' => $image_src[1],
		'height' => $image_src[2]
	);

	return $vt_image;
}

endif;
?>