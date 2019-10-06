<?php
/*
Plugin Name: L2H-MulticolLinks
Plugin URI: http://l2h.site
Plugin Description: Show the links with multiple columns layout in the sidebar.
Version: 0.0.1
Author: Lambert
Author URI: http://l2h.site
*/

/** core functions */
include ('core.php');

/** l10n */
load_plugin_textdomain('wp-multicollinks', '/wp-content/plugins/wp-multicollinks/languages/');
/**
 * 打印多栏的列表
 * @param args		参数字符串
 */
function wp_multicollinks( $args = '' ) {
	echo create_multicollinks( $args );
}

// -- widget START ------------------------------------------------------------

/*
 * 清除缓存
 */
/*
function wp_delete_multicollinks_cache() {
	wp_cache_delete( 'widget_multicollinks', 'widget' );
}
add_action( 'comment_post', 'wp_delete_multicollinks_cache' );
add_action( 'wp_set_comment_status', 'wp_delete_multicollinks_cache' );
*/


// 註冊自訂的Widget-EX_Widget
function register_ex_widget() {
    register_widget( 'EX_Widget' );
}
add_action( 'widgets_init', 'register_ex_widget' );
 
// 自訂的EX_Widget類別，要繼承WP_Widget
class EX_Widget extends WP_Widget {
    // 一些初始化設定
    function EX_Widget() {
        $widget_ops = array( 'classname' => 'side_ex', 'description' => '设定多列友情链接');
        $control_ops = array( 'width' => 300, 'height' => 500, 'id_base' => 'side_ex-widget' );
        $this->WP_Widget( 'side_ex-widget', '多列友情链接', $widget_ops, $control_ops );
    }
     
    // 描述widget顯示於前台時的外觀
    function widget( $args, $instance ) {
		// 转化参数
		$this_title = empty($instance['title']) ? __('Links', 'wp-multicollinks') : $instance['title'];
		$orderbyParam = 'name';
		if ($instance['orderby'] == 2) {
			$orderbyParam = 'url';
		} else if ($instance['orderby'] == 3) {
			$orderbyParam = 'rating';
		} else if ($instance['orderby'] == 4) {
			$orderbyParam = 'rand';
		}
		$orderParam = 'ASC';
		if ($instance['order'] == 2) {
			$orderParam = 'DESC';
		}
		// 组合参数字符串
		$argsBinding = 	  'limit='	    . $instance['number'] 
						. '&columns='	. $instance['columns'] 
						. '&category='	. $instance['category'] 
						. '&orderby='	. $orderbyParam 
						. '&order='		. $orderParam 
						. '&navigator='	. ($instance['navigator'] ? 'true' : 'false');
        // $args裡可以拿到所在版位的相關資訊，如before_widget、after_widget..等
        extract( $args );
		echo $before_widget;
		echo "<h2 class=\"widget-title\">$this_title</h2>";
		echo '<ul>';
		wp_multicollinks($argsBinding);
		echo '</ul>';       
		echo $after_widget;
		
    }
 
    // 於後台更新Widget時會做的事
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        // 簡單幫設定內容作一下Strip tags，擋掉html tag 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['columns'] = strip_tags( $new_instance['columns'] );
		$instance['category'] = strip_tags( $new_instance['category'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['order'] = strip_tags( $new_instance['order'] );
		$instance['navigator'] = strip_tags( $new_instance['navigator'] );
		
        return $instance;
    }
     
    // Widget在後台模組頁的外觀
    function form( $instance ) {
        // 可以設定預設值
		$defaults = array('title'=>'友情链接');
		
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label  for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e('Title: ', 'wp-multicollinks'); ?>
				<input class="widefat" id="title" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">
					<?php _e('Number of links to show: ', 'wp-multicollinks'); ?>
					
			</label>
			<input style="width: 25px;" id="number" name="<?php echo $this->get_field_name( 'number' );?>" type="text" value="<?php echo $instance['number']; ?>" />
			<br />
			<small><?php _e('(0 for ∞)', 'wp-multicollinks'); ?></small>
		</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'columns' ); ?>">
					<?php _e('Number of columns: ', 'wp-multicollinks'); ?>
					<input style="width: 25px;" id="columns" name="<?php echo $this->get_field_name( 'columns' );?>" type="text" value="<?php echo $instance['columns']; ?>" />
				</label>
				<br />
				<small><?php _e('(default: 1)', 'wp-multicollinks'); ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'category' ); ?>">
					<?php _e('Name of the category: ', 'wp-multicollinks'); ?>
					<input  id="category" name="<?php echo $this->get_field_name( 'category' );?>" type="text" value="<?php echo $instance['columns']; ?>" />
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'orderby' ); ?>">
					<?php _e('Sort by: ', 'wp-multicollinks'); ?>
					<select id="orderby" name="<?php echo $this->get_field_name( 'orderby' );?>" size="1" ?>">
						<option value="1" <?php if($instance['orderby']!= 2 && $instance['colorderbyumns'] != 3 && $instance['columns'] != 4) echo ' selected '; ?>>name</option>
						<option value="2" <?php if($instance['orderby'] == 2) echo ' selected '; ?>>url</option>
						<option value="3" <?php if($instance['orderby'] == 3) echo ' selected '; ?>>rating</option>
						<option value="4" <?php if($instance['orderby'] == 4) echo ' selected '; ?>>rand</option>
					</select>
				</label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'order' ); ?>">
					<?php _e('How to sort? ', 'wp-multicollinks'); ?>
					<select id="order" name="<?php echo $this->get_field_name( 'order' );?>" size="1">
						<option value="1" <?php if($instance['order'] != 2) echo ' selected '; ?>>ASC</option>
						<option value="2" <?php if($instance['order'] == 2) echo ' selected '; ?>>DESC</option>
					</select>
				</label>
			</p>

			<p>
				<label  for="<?php echo $this->get_field_id( 'navigator' );?>">
					<input name="<?php echo $this->get_field_name( 'navigator' );?>" type="checkbox" value="checkbox" <?php if($instance['navigator']) echo "checked='checked'"; ?> />
					 <?php _e('Show \'Show all\' button?', 'wp-multicollinks'); ?>
				</label>
			</p>
<?php
    }
}
// -- widget END ------------------------------------------------------------

// -- head START ------------------------------------------------------------

/**
 * 打印样式和脚本代码
 */
function multicollinks_head() {
	$css_url = get_bloginfo("wpurl") . '/wp-content/plugins/wp-multicollinks/wp-multicollinks.css';
	if ( file_exists(TEMPLATEPATH . "/wp-multicollinks.css") ){
		$css_url = get_bloginfo("template_url") . "/wp-multicollinks.css";
	}
	echo "\n" . '<!-- START of script generated by WP-MulticolLinks -->';
	echo "\n" . '<link rel="stylesheet" href="' . $css_url . '" type="text/css" media="screen" />';
	echo "\n" . '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wp-multicollinks/wp-multicollinks.js"></script>';
	echo "\n" . '<!-- END of script generated by WP-MulticolLinks -->' . "\n";
}

/**
 * 在页面 head 部分插入代码
 */
add_action('wp_head', 'multicollinks_head');

// -- head END ------------------------------------------------------------
?>
