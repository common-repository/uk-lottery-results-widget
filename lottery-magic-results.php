<?php
/*
Plugin Name: UK Lottery Results
Plugin URI: http://www.lotterymagic.co.uk/webmasters/
Description: Widget to show the latest UK lottery results
Author: LotteryMagic
Version: 1.1
Author URI: http://www.lotterymagic.co.uk/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

//Defines
define('LOTTERY_MAGIC_RW_VERSION','1.1');
define('LOTTERY_MAGIC_RW_FEED_URL','http://www.lotterymagic.co.uk/feeds/wordpress_lottery_magic_rw.dat');
define('LOTTERY_MAGIC_RW_LOCAL_DAT', WP_CONTENT_DIR . '/plugins/'.plugin_basename(dirname(__FILE__)).'/' . 'dat/wordpress_lottery_magic_rw.dat');
define('LOTTERY_MAGIC_RW_PLUGIN_URL',WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');

/*
If you are already using the wz_tooltip.js script elsewhere on your Wordpress
install, you can set the below to 0. You can still specify whether
or not to display the additional draw information rollovers from the Widget
admin as normal.
*/
define('LOTTERY_MAGIC_RW_LOAD_WZ_TOOLTIP',1);

//Go go go
class Lottery_Magic_Results_Widget extends WP_Widget {

    //Variales
    private $lottery_magic_lottery_types = array();
    private $lottery_magic_anchor_texts = array();

    function Lottery_Magic_Results_Widget() {
        //Constructor

        //Defines
        //We might add / remove lottery types in the future, so this makes it easier to maintain
        $this->lottery_magic_lottery_types = array("lotto" => "Lotto","thunderball" => "Thunderball","euromillions" => "Euromillions","lotto_plus5" => "Lotto Plus 5","health_lottery" => "Health Lottery");

        //Anchor texts
        $this->lottery_magic_anchor_texts = array("Lottery Results" => "http://www.lotterymagic.co.uk/", "Lottery Checker" => "http://www.lotterymagic.co.uk/lottery-checker/", "Thunderball Results" => "http://www.lotterymagic.co.uk/latest-thunderball-result/",
        					  "Euromillions Checker" => "http://www.lotterymagic.co.uk/euromillions-checker/", "Euromillions Results" => "http://www.lotterymagic.co.uk/latest-euromillions-result/", "Thunderball Checker" => 'http://www.lotterymagic.co.uk/thunderball-checker/',
                                                  "Lottery Magic" => "http://www.lotterymagic.co.uk/", "LotteryMagic.co.uk" => "http://www.lotterymagic.co.uk/",  "Lottery Results History" => "http://www.lotterymagic.co.uk/lottery-results-history/", "Lottery Numbers" => "http://www.lotterymagic.co.uk/",
                                                  "Lotto Numbers" => "http://www.lotterymagic.co.uk/", "Lotto Checker" => "http://www.lotterymagic.co.uk/lottery-checker/"
                                                  );

        //Set Random option here; won't redo activation hook on upgrade
        add_option('lottery_magic_rw_anchor',array_rand($this->lottery_magic_anchor_texts,1));

        //Widget options
        $widget_ops = array('classname' => 'lottery_magic_rw', 'description' => 'Lottery Magic.co.uk results widget');
        $this->WP_Widget('lottery_magic_results', 'Lottery Magic Results', $widget_ops);

        //Queue scripts if widget is active
        if (is_active_widget(false, false, 'lottery_magic_results', true) ) {
          wp_register_style('lottery_magic_rw_style', LOTTERY_MAGIC_RW_PLUGIN_URL . 'style.css');
          wp_enqueue_style('lottery_magic_rw_style');
          if (LOTTERY_MAGIC_RW_LOAD_WZ_TOOLTIP == 1) {
           wp_register_script('wz_tooltip', LOTTERY_MAGIC_RW_PLUGIN_URL . 'js/wz_tooltip.js','',false,true);
           wp_enqueue_script('wz_tooltip');
          }

        }


    }

    function widget($args, $instance) {
        // prints the widget
        extract($args);
        $small_ex = "";

         //Check whether to use small version
         if ($instance['show_small_css'] == 'on') {
          $small_ex = "_small";
         }

         //Version
        echo '<!-- Lottery Magic.co.uk Results Widget Version ' . LOTTERY_MAGIC_RW_VERSION . ' -->' . "\n";


        //Before Widget
        echo $before_widget;

        //Title
        $title = apply_filters('widget_title', $instance['title'] );
        if ($title) {echo $before_title . $title . $after_title;}

        /*
        XML too bulky here; use an encoded chunk. This is just flat data; there
        is nothing executable passed.
        */

        //Quick proceed marker
        $can_show_results = true;
        $lm_data_array = array();

        //Check date dat file was last pulled
        if (get_option('lottery_magic_rw_time') < (time() - 7200) ) {
         //Need to update the file
         if (@copy(LOTTERY_MAGIC_RW_FEED_URL,LOTTERY_MAGIC_RW_LOCAL_DAT)) {
          //File successfully copied
          update_option('lottery_magic_rw_time', time());
         } else {
          echo '<div class="lottery_magic_rw_plugin_error">Unable to copy lottery results data file. Have you chmod\'ed the dat directory to 777?</div>';
          $can_show_results = false;
          update_option('lottery_magic_rw_time',0);
         }
        }

        //If no issues so far, attempt to open the file
        if ($can_show_results == true) {
         $lm_data_array = unserialize(base64_decode(@file_get_contents(LOTTERY_MAGIC_RW_LOCAL_DAT)));
         if (!(sizeof($lm_data_array) > 1)) {
          echo '<div class="lottery_magic_rw_plugin_error">Unable to open lottery results data file. It might be corrupt. Please try refreshing this page.</div>';
          $can_show_results = false;
          update_option('lottery_magic_rw_time',0);
         }
        }


        if ($can_show_results == true) {

         //Extra info divs
         $div_pops = "";


         //Output lottery results
         foreach($this->lottery_magic_lottery_types as $this_type => $this_type_text) {
          if ( ($instance['show_' . $this_type] == 'on') and (sizeof($lm_data_array[$this_type]) > 0) ) {

           //Get Results data
           $result_data = array();
           $result_data = explode("-",$lm_data_array[$this_type]['r']);

           //Heading and draw date
           echo '<div class="lottery_magic_rw_result_title' . $small_ex . '">Latest ' . $this_type_text . ' Results</div>';
           echo '<div class="lottery_magic_rw_sub_title' . $small_ex . '">Draw #' . $result_data[0] . ' on ';
           if ($instance['date_type'] == 1) {echo $result_data[2];} else {echo $result_data[1];}
           if ( ($instance['show_rollover'] == 'on') and ($lm_data_array[$this_type]['p'] != "") ) {
            //Echo info icon
            echo '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="TagToTip(\'ib_' . $result_data[0] . '_' . $this_type . '\',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, WIDTH, 350)" onmouseout="UnTip()" class="lottery_magic_rw_info_icon" title="Click for more information on this ' . $this_type_text . ' draw">[i]</a>';

            //Build hidden div
            $div_pops .= '<div class="lottery_magic_rw_info_box" id="ib_' . $result_data[0] . '_' . $this_type . '" style="display: none;"><img src="' . LOTTERY_MAGIC_RW_PLUGIN_URL . 'img/lm.jpg" width="86" height="20" alt="Powered By Lottery Magic.co.uk" style="float: right;" />';
            $div_pops .= '<b>Additional information for ' . $this_type_text . ' draw #' . $result_data[0] . ' on ';
            if ($instance['date_type'] == 1) {$div_pops .= $result_data[2];} else {$div_pops .= $result_data[1];}
            $div_pops .= '</b><br /><br />Prize Information:<br /><br />';
            $div_pops .= $lm_data_array[$this_type]['p'] . '<br />';

            //Machine
            if ($lm_data_array[$this_type]['m'] != "") {
             $div_pops .= 'Draw Machine: ';
             $div_pops .= $lm_data_array[$this_type]['m'] . '<br /><br />';
            }

            //Ball Set
            if ($lm_data_array[$this_type]['b'] != "") {
             $div_pops .= 'Ball Set: ';
             $div_pops .= $lm_data_array[$this_type]['b'] . '<br /><br />';
            }

            $div_pops .= '</div>';


           }
           echo '</div>';

           //Ball Results
           echo '<div class="lottery_magic_rw_ball_row' . $small_ex . '" style="background: url(' . LOTTERY_MAGIC_RW_PLUGIN_URL . 'img/bg_' . $this_type . $small_ex .'.gif)">';

           //First 5 balls; all lottos have these
           for ($i = 1; $i<=5; $i++) {
            echo '<div class="bb">' . $result_data[($i+2)] . '</div>';
           }

           //Remaining Balls based on Lottery Type
           switch ($this_type) {
             case 'lotto':
             case 'lotto_plus5':
               //6th Ball
               echo '<div class="bb">' . $result_data[8] . '</div>';
               //Bonus Ball
               echo '<div class="bb_right">' . $result_data[9] . '</div>';
             break;
             case 'thunderball':
               //Thunderball
               echo '<div class="bb_right">' . $result_data[8] . '</div>';
             break;
             case 'euromillions':
               //Lucky Stars
               echo '<div class="bb_star">' . $result_data[9] . '</div>';
               echo '<div class="bb_star">' . $result_data[8] . '</div>';
            break;
          }


           echo '</div>' . "\n";

          //Instance enabled
          }

         //Foreach lottery type
         }

        //Can show results
        }

        //Echo info boxes;
        if ($instance['show_rollover'] == 'on') {
         echo $div_pops;
        }

        /**********************************************************************/

        if ( (is_front_page()) and (!is_paged()) ) {

        //Only on front page. Link can be hidden in Widget admin, please don't
        //remove this code.

         if ($instance['show_backlink'] != 'on') {
          echo '<div class="lottery_magic_rw_powered_by_off"';
         } else {
          echo '<div class="lottery_magic_rw_powered_by"';
         }
         echo '><a href="' . $this->lottery_magic_anchor_texts[get_option('lottery_magic_rw_anchor')] . '" target="_blank">' . get_option('lottery_magic_rw_anchor') . '</a> by Lottery Magic</div>' . "\n";

        }

        /**********************************************************************/

        //After Widget
        echo $after_widget;



         //Version
        echo '<!-- End of Lottery Magic.co.uk Results Widget Version ' . LOTTERY_MAGIC_RW_VERSION . ' -->' . "\n";

    }

    function update($new_instance, $old_instance) {
        //save the widget
        $instance = $old_instance;

        //Title
        $instance['title'] = strip_tags($new_instance['title']);

        //Lottery enabling checkboxes
        foreach($this->lottery_magic_lottery_types as $this_type => $this_type_text) {
         $instance['show_' . $this_type] = $new_instance['show_' . $this_type];
        }

        //Other Options
        $instance['date_type'] = $new_instance['date_type'];
        $instance['show_rollover'] = $new_instance['show_rollover'];
        $instance['show_backlink'] = $new_instance['show_backlink'];
        $instance['show_small_css'] = $new_instance['show_small_css'];

        return $instance;
    }

    function form($instance) {
        //widgetform in backend

       //Defines
       $defaults = array('title' => 'Latest Lottery Results');
       $instance = wp_parse_args((array)$instance,$defaults);

       //Title
?>

       	<p>
	 <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
	 <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
	</p>
<?php
        //Checkbox options for each lottery type
        foreach($this->lottery_magic_lottery_types as $this_type => $this_type_text) {
?>
	<p>
	 <input class="checkbox" type="checkbox" value="on" <?php checked($instance['show_' . $this_type], 'on' ); ?> id="<?php echo $this->get_field_id('show_' . $this_type); ?>" name="<?php echo $this->get_field_name('show_' . $this_type); ?>" />
	 <label for="<?php echo $this->get_field_id('show_' . $this_type); ?>"><?php _e('Display ' . $this_type_text . ' results?'); ?></label>
	</p>
<?php
        }

        //Show Results Rollover
?>
        <p>
	 <input class="checkbox" type="checkbox" value="on" <?php checked($instance['show_rollover'], 'on' ); ?> id="<?php echo $this->get_field_id('show_rollover'); ?>" name="<?php echo $this->get_field_name('show_rollover'); ?>" />
	 <label for="<?php echo $this->get_field_id('show_rollover'); ?>"><?php _e('Show additional info rollovers?'); ?></label>
	</p>

<?php
	//Use Small CSS
?>
        <p>
	 <input class="checkbox" type="checkbox" value="on" <?php checked($instance['show_small_css'], 'on' ); ?> id="<?php echo $this->get_field_id('show_small_css'); ?>" name="<?php echo $this->get_field_name('show_small_css'); ?>" />
	 <label for="<?php echo $this->get_field_id('show_small_css'); ?>"><?php _e('Use \'small\' version for themes with narrow sidebars?'); ?></label>
	</p>

<?php
	//Show Powered By Link
?>
        <p>
	 <input class="checkbox" type="checkbox" value="on" <?php checked($instance['show_backlink'], 'on' ); ?> id="<?php echo $this->get_field_id('show_backlink'); ?>" name="<?php echo $this->get_field_name('show_backlink'); ?>" />
	 <label for="<?php echo $this->get_field_id('show_backlink'); ?>"><?php _e('Support Lottery Magic.co.uk by displaying a small link back to us?'); ?></label>
	</p>

<?php
        //UK / European Date
?>
        	<p>
			<label for="<?php echo $this->get_field_id('date_type'); ?>"><?php _e('Date Format:'); ?></label>
			<select id="<?php echo $this->get_field_id('date_type'); ?>" name="<?php echo $this->get_field_name('date_type'); ?>" class="widefat">
				<option value="0" <?php if ( ($instance['date_type'] == 0) or ($instance['date_type'] == "")  ) {echo 'selected="selected"';} ?>>DD/MM/YYYY</option>
				<option value="1" <?php if ( $instance['date_type'] == 1 ) {echo 'selected="selected"';} ?>>MM/DD/YYYY</option>
			</select>
		</p>



<?php
    }
}



/******************************************************************************/
/* Setup functions */
/******************************************************************************/

//Set options on activation
function set_lottery_magic_rw_options_on() {
 add_option('lottery_magic_rw_time',0);
}

function set_lottery_magic_rw_options_off() {
 delete_option('lottery_magic_rw_time');
 delete_option('lottery_magic_rw_anchor');
}

//Bind activate function
register_activation_hook( __FILE__, 'set_lottery_magic_rw_options_on' );
register_deactivation_hook( __FILE__, 'set_lottery_magic_rw_options_off' );

//Register widget
add_action('widgets_init', 'Register_Lottery_Magic_Results_Widget');
function Register_Lottery_Magic_Results_Widget() {
    register_widget('Lottery_Magic_Results_Widget');
}

/******************************************************************************/

?>
