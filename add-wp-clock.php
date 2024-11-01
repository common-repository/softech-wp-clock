<?php

/* ======= Get the all List of Time Zones  =========== */

function softech_wp_tz_list() 
{
  $zones_array = array();
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) 
  {
    date_default_timezone_set($zone);
    $zones_array[$key]['zone'] = $zone;
    $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
  }
  return $zones_array;
}


function softech_wp_clock_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'softech_clock'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records

    $name = $_POST['name'];
    $time_zone = $_POST['time_zone'];
    $bgcolor = $_POST['bgcolor'];
    $font_color = $_POST['font_color'];
    $date_font_size = $_POST['date_font_size'];
    $time_font_size = $_POST['time_font_size'];
    $zone_font_size = $_POST['zone_font_size'];
    $padding = $_POST['padding'];
    $text_align = $_POST['text_align'];
    $clock_style = $_POST['clock_style'];

    $clock_css=array('bgcolor' => $bgcolor,
                     'font_color' => $font_color,
                     'date_font_size' => $date_font_size,
                     'time_font_size' => $time_font_size,
                     'zone_font_size' => $zone_font_size,
                     'padding' => $padding,
                     'text_align' => $text_align,
                     'clock_style' => $clock_style,
                 );
    $clock_css=json_encode($clock_css);
    $default = array(
        'id' => 0,
        'name' => '',
        'time_zone' => '',
        'clock_css' => $clock_css,
    );

    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) 
    {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        
            if ($item['id'] == 0) 
            {
                $result = $wpdb->insert($table_name, array( 'id'=>0,'name'=>$name,'time_zone'=>$time_zone,'clock_css'=>$clock_css) );
                $item['id'] = $wpdb->insert_id;

                $updateshortcode=array( 
                        'shortcode' => '[softech-wp-clock id="'.$item['id'].'" name= "'.$name.'"]'
                    );
                
                $result = $wpdb->update( $table_name, $updateshortcode, array('id' => $item['id']));

                if ($result) 
                {                     
                    $message = __('Time Zone was successfully Saved', 'softech-wp-clock');
                    ?><!--<script>window.location = "<?php //echo home_url('/wp-admin/admin.php?page=softech-wp-all-zone-list/');?>"</script>--><?php
                } 
                else 
                {
                    $notice = __('There was an error while saving Time Zone', 'softech-wp-clock');
                }
            } 
            else 
            {
                $result = $wpdb->update( $table_name, array( 'name'=>$name,'time_zone'=>$time_zone,'clock_css'=>$clock_css), array('id'=>$item['id']) );
                if ($result) 
                {
                    $message = __('Time Zone was successfully updated', 'softech-wp-clock');
                    ?><!--<script>window.location = "<?php echo home_url('/wp-admin/admin.php?page=softech-wp-all-zone-list/');?>"</script>--><?php
                } 
                else 
                {
                    $notice = __('There was an error while updating Time Zone', 'softech-wp-clock');
                }
            }
    }
    else 
    {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) 
        {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) 
            {
                $item = $default;
                $notice = __('Time Zone not found', 'softech-wp-clock');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('softech_form_meta_box', __('Softech Wp Clock Data','softech-wp-clock'), 'softech_wp_clock_new_zone', 'softech', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h1><?php esc_html_e('Time Zone', 'softech-wp-clock')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=softech-wp-all-zone-list');?>"><?php esc_html_e('back to list', 'softech-wp-clock')?></a>
    </h1>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('softech', 'normal', $item); ?>
                    <input type="submit" value="<?php esc_html_e('Save', 'softech-wp-clock')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 */
function softech_wp_clock_new_zone($item)
{
    $clock_css=$item['clock_css']; 
    $clock_css = json_decode($clock_css, true);
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php esc_html_e('Time Zone Name', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="zonename" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name']);?>"
                   size="50" placeholder="<?php esc_html_e('Time Zone name', 'softech-wp-clock')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php esc_html_e('Your Time Zone', 'softech-wp-clock')?></label>
        </th>
        <td>
            <select name="time_zone">
                <option value=""><?php esc_html_e('Please, select Time zone', 'softech-wp-clock')?></option>
                <?php foreach(softech_wp_tz_list() as $t) { ?>
                <option value="<?php echo $t['zone']; ?>" <?php echo esc_attr($item['time_zone']) == $t['zone'] ? 'selected="selected"' : ''; ?>> <?php print $t['diff_from_GMT'] . ' - ' . $t['zone'] ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="clock_style"><?php esc_html_e('Clock Style', 'softech-wp-clock')?></label>
        </th>
        <td>
            <select name="clock_style" id="clock_style">
                        <option value="Default" <?php echo esc_attr( $clock_css['clock_style'] ) == 'Default' ? 'selected="selected"' : ''; ?>>Default</option>
                        <option value="Analog Clock" <?php echo esc_attr( $clock_css['clock_style'] ) == 'Analog Clock' ? 'selected="selected"' : ''; ?>>Analog Clock</option>
                        <!-- <option value="Flip Clock" <?php echo esc_attr( $clock_css['clock_style'] ) == 'Flip Clock' ? 'selected="selected"' : ''; ?>>Flip Clock</option> -->
                        <option value="digital-design" <?php echo esc_attr( $clock_css['clock_style'] ) == 'digital-design' ? 'selected="selected"' : ''; ?>>Digital Design</option>
                       
            </select>
        </td>
    </tr>
    <tr class="form-field" id="bgcolor">
        <th valign="top" scope="row">
            <label for="bgcolor"><?php esc_html_e('Background Color', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="bgcolor" name="bgcolor" type="color" style="width: 10%" value="<?php echo esc_attr($clock_css['bgcolor'])?>"
                   size="50">
        </td>
    </tr>
    <tr class="form-field" id="font_color">
        <th valign="top" scope="row">
            <label for="font_color"><?php esc_html_e('Font Color', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="font_color" name="font_color" type="color" style="width: 10%" value="<?php echo esc_attr($clock_css['font_color'])?>"
                   size="50">
        </td>
    </tr>
    <tr class="form-field" id="padding">
        <th valign="top" scope="row">
            <label for="padding"><?php esc_html_e('Padding', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="time-padding" name="padding" type="number" style="width: 15%" value="<?php echo esc_attr($clock_css['padding'])?>"
                   size="50" placeholder="15" min="0" >
            <span class="note"><?php esc_html_e('* Value in "px"', 'softech-wp-clock')?></span>
        </td>
    </tr>
    <tr class="form-field" id="date_font_size">
        <th valign="top" scope="row">
            <label for="date_font_size"><?php esc_html_e('Date Font Size', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="date_font_size" name="date_font_size" type="number" style="width: 15%" value="<?php echo esc_attr($clock_css['date_font_size'])?>"
                   size="50"  placeholder="24" min="8" >
            <span class="note"><?php esc_html_e('* Value in "px"', 'softech-wp-clock')?></span>
        </td>
    </tr>
    <tr class="form-field" id="time_font_size">
        <th valign="top" scope="row">
            <label for="time_font_size"><?php esc_html_e('Time Font Size', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="time_font_size" name="time_font_size" type="number" style="width: 15%" value="<?php echo esc_attr($clock_css['time_font_size'])?>"
                   size="50"  placeholder="80" min="8" >
            <span class="note"><?php esc_html_e('* Value in "px"', 'softech-wp-clock')?></span>
        </td>
    </tr>
    <tr class="form-field" id="zone_font_size">
        <th valign="top" scope="row">
            <label for="zone_font_size"><?php esc_html_e('Time Zone Font Size', 'softech-wp-clock')?></label>
        </th>
        <td>
            <input id="zone_font_size" name="zone_font_size" type="number" style="width: 15%" value="<?php echo esc_attr($clock_css['zone_font_size'])?>"
                   size="50"  placeholder="12" min="8" >
            <span class="note"><?php esc_html_e('* Value in "px"', 'softech-wp-clock')?></span>
        </td>
    </tr>
    <tr class="form-field" id="text_align">
        <th valign="top" scope="row">
            <label for="text_align"><?php esc_html_e('Text Align', 'softech-wp-clock')?></label>
        </th>
        <td>
            <select name="text_align">
                        <option value=""><?php esc_html_e('Select Text Align', 'softech-wp-clock')?></option>
                        <option value="Left" <?php echo esc_attr( $clock_css['text_align'] ) == 'Left' ? 'selected="selected"' : ''; ?>>Left</option>
                        <option value="Center" <?php echo esc_attr( $clock_css['text_align'] ) == 'Center' ? 'selected="selected"' : ''; ?>>Center</option>
                        <option value="Right" <?php echo esc_attr( $clock_css['text_align'] ) == 'Right' ? 'selected="selected"' : ''; ?>>Right</option>
            </select>
        </td>
    </tr>
    </tbody>
</table>
<style type="text/css">
tr.form-field.hide {
    display: none;
}
</style>
<?php
} ?>