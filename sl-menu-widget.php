<?php
/*
Plugin Name: Liersch Menu Widget
Plugin URI: http://www.steffen-liersch.de/wordpress/
Description: This plug-in installs a sidebar widget to display a submenu for the current page.
Version: 1.4
Author: Steffen Liersch
Author URI: http://www.steffen-liersch.de/
*/

final class SL_Menu_Widget extends WP_Widget
{
  /*
    Add the following code to file "function.php" of your theme if it
    provides a special CSS class supporting this widget. Assign the CSS
    class name to this variable. It will be used as default on adding a
    new widget instance to your sidebar.

    if(class_exists(SL_Menu_Widget))
      SL_Menu_Widget::$default_class_name='widget_menu';
  */
  static $default_class_name='';

  // Widget construction
  function __construct()
  {
    $options=array(
      'classname'=>'widget_menu_sl',
      'description'=>__('A list of sub pages.', 'Steffen Liersch'));

    parent::__construct('sl_menu', 'Liersch Menu Widget', $options);
  }

  // Widget rendering
  function widget($args, $instance)
  {
    if(is_page())
    {
      global $post;

      $id=$post->ID;
      $ancestors=$post->ancestors;
      if($ancestors)
      {
        $c=sizeof($ancestors);
        if($c>0)
          $id=$ancestors[$c-1];
      }

      $s='child_of='.$id;
      $s.='&depth='.$instance['depth'];
      $s.='&sort_column=menu_order';
      $s.='&title_li=';
      $s.='&echo=0';

      $children=wp_list_pages($s);
      if($children)
      {
        $page=get_post($id);
        $title=$page->post_title;
        $title=apply_filters('widget_title', $title);

        echo "\n\n<!-- SLMenuWidget by Steffen Liersch -->\n";
        $class=$instance['class'];
        if($class)
        {
          $style=$instance['style'];
          echo '<li class="'.$class.'">'."\n";
          echo '<h2>'.$title.'</h2>';
          echo "<ul>\n".$children."</ul>\n";
          echo '</li>';
        }
        else
        {
          echo $args['before_widget']."\n";
          echo $args['before_title'].$title.$args['after_title'];
          echo "<ul>\n".$children."</ul>\n";
          echo $args['after_widget'];
        }
        echo "\n<!-- SLMenuWidget by Steffen Liersch -->\n\n";
      }
    }
  }

  // Widget settings update
  function update($new_instance, $old_instance)
  {
    $instance=$old_instance;

    $depth=trim(strip_tags(stripslashes($new_instance['depth'])));
    $depth=(int)$depth;
    if(intval($depth)<=0)
      $depth=3;
    $instance['depth']=$depth;

    $instance['class']=trim(strip_tags(stripslashes($new_instance['class'])));
    return $instance;
  }

  // Widget setup form
  function form($instance)
  {
    // Set default values
    $instance=wp_parse_args((array)$instance,
      array('class'=>self::$default_class_name, 'depth'=>'3'));

    $option='depth';
    $value=htmlspecialchars($instance[$option]);
    $id=$this->get_field_id($option);
    echo '<label for="'.$id.'">'.__('Submenu Depth: ', 'Steffen Liersch');
    echo '<input id="'.$id.'" name="'.$this->get_field_name($option);
    echo '" type="text" size="2" value="'.$value.'" /></label><br />';

    $option='class';
    $value=htmlspecialchars($instance[$option]);
    $id=$this->get_field_id($option);
    echo '<label for="'.$id.'">'.__('Style Class: ', 'Steffen Liersch');
    echo '<br /><input id="'.$id.'" name="'.$this->get_field_name($option);
    echo '" type="text" size="20" value="'.$value.'" /></label><br />';

    ?>
    <div style="text-align: center">
      <small>If no style class is specified, theme-specific rendering is peformed.</small>
    </div>
    <br />

    <div style="text-align: center">
      <small>If you like this plug-in, you can leave a donation to support maintenance and development.</small>
    </div>
    <br />

    <div style="text-align: center">
      <a style="outline: none;" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=9566236"><img
        title="Leave a donation to support maintenance and development"
        alt="PayPal - The safer, easier way to pay online!"
        src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif"
        width="147" height="47" border="0"
      /></a>
    </div>
    <br />

    <div style="text-align: center">
      <small>Copyright &copy; 2009-2010 Steffen Liersch<br />
      <a href="http://www.steffen-liersch.de/">www.steffen-liersch.de</a></small>
    </div>
    <?php

    @readfile('http://www.steffen-liersch.de/advertisement/?mode=div&type=short');
  }
}

if(function_exists('add_action'))
  add_action('widgets_init', create_function('', 'register_widget("SL_Menu_Widget");'));

?>