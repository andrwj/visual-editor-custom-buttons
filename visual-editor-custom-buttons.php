<?php
/*
Plugin Name: Editor Custom Buttons
Plugin URI: http://andrwj.com/wordpress/editor-custom-buttons
Description: Create custom buttons in Wordpress Visual Editor.
Version: 1.5.2.3
Author: A.J
Author URI: http://andrwj.com
Text Domain: editor-custom-buttons
License: GPL
*/

//require_once(__DIR__ . '/vendor/autoload.php');
//PhpConsole\Helper::register();

add_action('init',           'vecb_editor_buttons');
add_action("admin_init",     'vecb_admin_init');
add_action('save_post',      'vecb_save_options');
add_action('init',           'vecb_initual_setup');
add_action('admin_menu' ,    'vecb_setting_page');
add_action('admin_init',     'vecb_settings_store');


/*  Settings Page */
function vecb_setting_page() {
    add_submenu_page('edit.php?post_type=vecb_editor_buttons', 'Visual Editor Custom Button Settings', 'Settings', 'edit_posts', basename(__FILE__), 'vecb_settings');
}

function vecb_settings_store() {
    register_setting('vecb_settings', 'vecb_row');
    global $wp_roles;
    $roles = $wp_roles->get_names();
    foreach($roles as $role) {
        register_setting('vecb_settings', 'vecb_access_'.$role);
    }
    wp_enqueue_script( 'quicktags' );
}

/* Settings Screen: Placement in toolbar, Access Rights among roles */
function vecb_settings() { ?>
    <div class="wrap">
    <h2>Visual Editor Custom Buttons Settings</h2>
    <form method="post" action="options.php">
<?php settings_fields('vecb_settings');

    $rowvalue = get_option('vecb_row');
    $selected1 = "";
    $selected2 = "";
    $selected3 = "";

    if($rowvalue == "" || $rowvalue == NULL) { $selected1 = "checked"; }
    if($rowvalue == "_2") { $selected2 = "checked"; }
    if($rowvalue == "_3") { $selected3 = "checked"; }

    $access_sel = array();
    for($i=0;$i<=5;$i++) {$access_sel[$i] = "";}
?>
    <div class="recb_inputblock"><div class="vecb_label">Which row do you want to place custom buttons in Editor Toolar?</div>
    <input type="radio" name="vecb_row"  value="" <?php echo $selected1 ?> />
    <label style="margin-right: 30px;" for="wrap">First Row</label>
    <input type="radio" name="vecb_row"  value="_2" <?php echo $selected2 ?> />
    <label style="margin-right: 30px;" for="_2">Second Row</label><input type="radio" name="vecb_row" value="_3" <?php echo $selected3 ?> />
    <label style="margin-right: 30px;" for="_3">Third Row</label></div>
    <div class="recb_inputblock"><div class="vecb_label">Allow other user roles to administer buttons</div>
<?php
    global $wp_roles;
    $roles = $wp_roles->get_names();
    $accessvalue = array();
    foreach ($roles as $role) :
        $accessvalue[$role] = get_option('vecb_access_'.$role);
    endforeach;

    $count = 0;
    foreach ($roles as $role) {
        echo get_option('vecb_access_.'.$role);
        $count ++;
        $checked = ($role == $accessvalue[$role] ) ? "checked" : "";
        if($role !="Administrator" && $role !="Subscriber") {
            ?>
            <input name="vecb_access_<?php echo $role ?>" type="checkbox" <?php echo $checked ?> value="<?php echo $role ?>" /> <?php echo $role ?><br />
<?php   }
    }
?>
    <p class="submit"><input type="submit" class="button-primary" value="Save Settings" /></p>
    </form>
    </div>
<?php
}

/* Initial Setup */
function vecb_initual_setup() {
    // get current user role
    function vecb_get_current_user_role() {
        global $current_user;
        wp_get_current_user();
        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);
        return $user_role;
    };

    $rowvalue = get_option('vecb_row');
    $vecb_current_user = ucfirst(vecb_get_current_user_role());
    $accessvalue = array();

    global $wp_roles;
    $roles = $wp_roles->get_names();

    foreach ($roles as $role) :
        $accessvalue[$role] = get_option('vecb_access_'.$role);
    endforeach;

    if(!in_array($vecb_current_user,$accessvalue) && $vecb_current_user != "Administrator") {
        function vecb_remove_menuitem() {
            echo '
<style>
 li#menu-posts-vecb_editor_buttons, li#wp-admin-bar-new-vecb_editor_buttons {
   display: none;
 }
</style>
';
        }
        add_action('admin_head', 'vecb_remove_menuitem');
    }

    if($vecb_current_user != "Administrator") {
        function vecb_remove_menusettingsitem() {
            echo '
<style>
  li#menu-posts-vecb_editor_buttons ul li:nth-child(4) {
      display: none;
  }
</style>
';
        }
        add_action('admin_head', 'vecb_remove_menusettingsitem');
    }

    $file = WP_PLUGIN_DIR."/visual-editor-custom-buttons/css/editor-style.css";

    // if global CSSS file does not exist, save updates to files
    if(!file_exists($file)) : $args = array( 'post_type' => 'vecb_editor_buttons', 'posts_per_page' => -1, 'order' => 'asc');
    $loop = new WP_Query( $args );
    $count = 0;
    $stylefile = WP_PLUGIN_DIR. '/visual-editor-custom-buttons/css/editor-style.css';
    $style = '@charset "UTF-8";
/* Editor Button CSS */
';

    while ( $loop->have_posts() ) : $loop->the_post();
    $id = get_the_ID();
    $count ++;
    $custom = get_post_custom($post->ID);
    $left_tag = $custom["left_tag"][0];
    $right_tag = $custom["right_tag"][0];
    $styling = $custom["styling_content"][0];
    $selection = $custom["content-type"][0];
    $block_content = $custom["block_content"][0];
    $icon = $custom["icon"][0];

    // remove Linebreaks
    $block_content = str_replace("\r\n","",$block_content);
    $right_tag = str_replace("\r\n","",$right_tag);
    $left_tag = str_replace("\r\n","",$left_tag);
    $blog_id = get_current_blog_id();
    $file = WP_PLUGIN_DIR. '/visual-editor-custom-buttons/js/button-'.$blog_id.'-'.$count.'.js';
    $current = file_get_contents($file);

    /************************************************************************
     *
     *  Generate Visual Editor Button JS-files
     *
     ************************************************************************/

    if($selection == "wrap") {

        $custom= false;
        $first = substr($icon,0,1);
        if ($first == "_") {
            $icon = substr($icon, 1);
            $custom= true;
        }

        $uploads = wp_upload_dir();
        $uploaddir = $uploads['basedir']."/vecb/";
        $uploadurl = $uploads['baseurl']."/vecb/";

        $current = "// JavaScript Document

function getBaseURL () {
   return location.protocol + '//' + location.hostname +
      (location.port && ':' + location.port) + '/';
}

(function() {
    tinymce.create('tinymce.plugins.vecb_button".$count."', {
        init : function(ed, url) {
            ed.addButton('vecb_button".$count."', {
                title : '".get_the_title()."',";
        if($custom == false) {
            $current .="image : url+'/icons/".$icon."',";
        } else {
            $current .="image : '".$uploadurl.$icon."',";
        }
        $current .=          "onclick : function() {
                     ed.selection.setContent('". $left_tag . "' + ed.selection.getContent() + '". $right_tag ."');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('vecb_button".$count."', tinymce.plugins.vecb_button".$count.");
})();";

    } else {

        $custom= false;
        $first = substr($icon,0,1);
        if ($first == "_") {
            $icon = substr($icon, 1);
            $custom= true;
        }

        $uploads = wp_upload_dir();
        $uploaddir = $uploads['basedir']."/vecb/";
        $uploadurl = $uploads['baseurl']."/vecb/";

        $current = "// JavaScript Document

function getBaseURL () {
   return location.protocol + '//' + location.hostname +
      (location.port && ':' + location.port) + '/';
}

(function() {
    tinymce.create('tinymce.plugins.vecb_button".$count."', {
        init : function(ed, url) {
            ed.addButton('vecb_button".$count."', {
                title : '".get_the_title()."',";
        if($custom == false) {
            $current .="image : url+'/icons/".$icon."',";
        } else {
            $current .="image : '".$uploadurl.$icon."',";
        }
        $current .=          "onclick : function() {
                     ed.selection.setContent('".$block_content."');
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('vecb_button".$count."', tinymce.plugins.vecb_button".$count.");
})();";

    }
    $style .= $styling . "
";

    // Write the contents back to the file
    file_put_contents($file, $current);
    endwhile;

    file_put_contents($stylefile, $style);

    // add_action('admin_print_footer_scripts',  '_add_my_quicktags');
    add_action('after_wp_tiny_mce', '_add_my_quicktags');
    add_action('admin_init', 'vecb_add_buttons');

    endif;
}

function vecb_editor_buttons() {
    //  Adds a filter to append the default stylesheet to the tinymce editor
    // TODO: get_post_field('post_content', 'twentysixteen', 'display') 함수를 통해 DB에서 현재 활성화된 테마의 Custom CSS를 가져와서 설정하기
    if ( ! function_exists('tdav_css') ) {
        function tdav_css($wp) {
            $uploads = wp_upload_dir();
            $wp_custom_css = $uploads["baseurl"] . '/custom-css-js/wp-custom-style.css';
            $url = plugins_url()."/visual-editor-custom-buttons";
            $wp .= ',' . $wp_custom_css . "," . $url.'/css/editor-style.css';
            return $wp;
        }
    }
    add_filter( 'mce_css', 'tdav_css' );

    /*  Adds Form Admin Style to Admin Head */
    function vecb_customAdmin() {

        //$file = dirname(__FILE__) . '/js/admin_scripts.js';
        //$url = plugin_dir_url($file) . 'admin_scripts.js';

        $url = plugins_url()."/visual-editor-custom-buttons";

        echo '<script type="text/javascript" src="' . $url . '/js/admin_scripts.js"></script>';
        echo '<link rel="stylesheet" type="text/css" href="' . $url .  '/css/admin-style.css">';

        echo '<script src="' . $url . '/js/msdropdown/jquery.dd.min.js" type="text/javascript"></script>';
        echo '<link rel="stylesheet" type="text/css" href="' . $url . '/css/msdropdown/dd.css" />';

        $wp_version = get_bloginfo('version');
        if ($wp_version >=3.8) {
            echo '<style>

#menu-posts-vecb_editor_buttons .wp-menu-image {
	background:none !important;
}

#menu-posts-vecb_editor_buttons .wp-menu-image:before {
	content: \'\f111\' !important;
}

.mceIcon img {
	opacity: 0.655;
}

.mceIcon:hover img {
	opacity: 1;
}
.mce-i-none {
	opacity: 0.655;
}

#vecb_btnpreview {
	opacity: 0.655;
}

#vecb_btnpreview:hover {
	opacity: 1;
}
</style>';

        }
        //echo '<link rel="stylesheet" type="text/css" href="' . plugin_dir_url($file) .  'editor-style.css">';
    }

        add_action('admin_head', 'vecb_customAdmin');


    /*function vecb_frontendstyle() {
      echo '<link rel="stylesheet" type="text/css" href="' . $url .  '/css/editor-style.css">';
      }
      add_action('wp_head', 'vecb_frontendstyle');
    */


    /*  Register Post Type And Add Custom Fields */
    $labels = array(
        'name' => 'Visual Editor Custom Buttons',
        'singular_name' => 'Custom button',
        'add_new' => 'Add new',
        'add_new_item' => 'Add new button',
        'edit_item' => 'Edit button',
        'new_item' => 'New button',
        'all_items' => 'All buttons',
        'view_item' => 'View button',
        'search_items' => 'Search buttons',
        'not_found' =>  'No button found',
        'not_found_in_trash' => 'No button found in trash',
        'parent_item_colon' => '',
        'menu_name' => 'Editor Custom Buttons'
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => true,
        'supports' => array('title'),
        'menu_position' => 100,
        'exclude_from_search' => true,
        'show_in_nav_menus' => false
    );
    register_post_type( 'vecb_editor_buttons' , $args );

    function vecb_admin_init(){
        add_meta_box("_vecb_tags", "Button Content", "vecb_tag_options", "vecb_editor_buttons", "normal", "low");
        add_meta_box("_vecb_editor", "Display In Editor", "vecb_editor_options", "vecb_editor_buttons", "normal", "low");
        add_meta_box("_vecb_styling", "Button Specific CSS Style", "vecb_styling_options", "vecb_editor_buttons", "normal", "low");
        add_meta_box("_vecb_script", "Button Specific JavaScript", "vecb_script_options", "vecb_editor_buttons", "normal", "low");
    }

    function vecb_tag_options() {
        global $post;
        $custom = get_post_custom($post->ID);
        $left_tag = $custom["left_tag"][0];
        $right_tag = $custom["right_tag"][0];
        $block_content = $custom["block_content"][0];
        $radio = $custom["content-type"][0];
        if ($radio == "wrap" || $radio == NULL) {
            $wrap = "checked";
            $block = "";
        } else if ($radio == "block") {
            $wrap = "";
            $block = "checked";
        }

        $content = ' <div class="recb_inputblock"><input type="radio" name="content-type" class="vecb_radiobtn" id="vecb_wrap" value="wrap" '.$wrap.'>
  <label for="wrap">&nbsp;Wrap Selection</label>&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="radio" class="vecb_radiobtn" name="content-type" id="block" value="block" '.$block.'>
  <label for="block">&nbsp;Single Block</label></div>';

            $content .= '<section id="vecb_wrap-selection" class="vecb_inputbox recb_inputblock"><div class="vecb_label">Before</div>
	<textarea name="left_tag" id="left_tag" cols="45" rows="2">' . $left_tag  . '</textarea>';

            $content .= '<div class="recb_inputblock vecb_less_space"><div class="vecb_label">After</div>
	<textarea name="right_tag" id="left_tag" cols="45" rows="2">' . $right_tag  . '</textarea></div></section>';

            $content .= '<div id="vecb_single-block" class="vecb_inputbox recb_inputblock"><div class="vecb_label">Content</div>
	<textarea name="block_content" cols="45" rows="5">' . $block_content . '</textarea></div>';

            echo $content;

        }

    function vecb_editor_options() {
        global $post;
        $custom = get_post_custom($post->ID);
        $rich_editor = $custom["rich_editor"][0];
        $html_editor = $custom["html_editor"][0];
        $icon = $custom["icon"][0];
        $quicktag = $custom["quicktag"][0];
        $script = $custom["script_content"][0];
        $sel1 = "";
        $sel2 = "";
        $sel3 = "";

        //Get files from folder//
        $dir = WP_PLUGIN_DIR."/visual-editor-custom-buttons/js/icons/";

        $uploads = wp_upload_dir();
        $uploaddir = $uploads['basedir']."/vecb/";
        $uploadurl = $uploads['baseurl']."/vecb/";

        if(is_dir($uploaddir)){
            $uploadfiles = scandir($uploaddir);
        }

        $files = scandir($dir);
        $btnicons[] = "none.png";

        if(is_array($uploadfiles) && count($uploadfiles) >2) {
            //$customicons[] = "-----------------";

            foreach($uploadfiles as $file) {
                if($file != "." && $file != "..") {
                    $customicons[] = $file;
                }
            }

            //$customicons[] = "-----------------";
        }

        foreach($files as $file) {
            if($file != "none.png" && $file != "." && $file != "..") {
                $btnicons[] = $file;
            }
        }

        $sel = array();
        for ($i=0;$i<count($btnicons);$i++) {
            if ($icon == $btnicons[$i]) {
                $sel[$i] = "selected";
            } else {
                $sel[$i] = "";
            }
        }

        $thisicon = substr($icon, 1);

        if(is_array($customicons)) {
            for ($i=0;$i<count($customicons);$i++) {
                if ($thisicon == $customicons[$i]) {
                    $customsel[$i] = "selected";
                } else {
                    $customsel[$i] = "";
                }
            }

        }

        $re = ($rich_editor != "") ? "checked" : "";
        $he = ($html_editor != "") ? "checked" : "";

        $content = ' <div class="recb_inputblock"><input type="checkbox" name="rich_editor" id="vecb_rich_editor" value="rich_editor" '.$re.'>
  <label for="rich_editor">&nbsp;Visual Editor</label>&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="checkbox" name="html_editor" id="vecb_html_editor" value="html_editor" '.$he.'>
  <label for="html_editor">&nbsp;Text Editor</label></div>';

        $content .= '<div id="vecb_btnicon"><div class="vecb_iconselect"><div class="recb_label">Button Icon</div><select name="icon" id="vecb_icon">';
        $ticon = explode(".", $btnicons[0]);
        $theicon = str_replace("_"," ",$ticon[0]);
        $theicon = ucfirst($theicon);
        $content .= '<option value="'.$btnicons[0].'" '.$sel[0].' >'.$theicon.'</option>';

        if(is_array($customicons)) {
            for ($i=0;$i<count($customicons);$i++) {
                $ticon = explode(".", $customicons[$i]);
                $theicon = str_replace("_"," ",$ticon[0]);
                $theicon = ucfirst($theicon);
                if($customicons[$i] != "-----------------") {
                    $content .= '<option value="_'.$customicons[$i].'" data-image="'.$uploadurl.$customicons[$i].'" '.$customsel[$i].' >'.$theicon.'</option>';
                } else {
                    $content .= '<option value="none.png" '.$customsel[$i].' >'.$theicon.'</option>';
                }
            }
        }

        $url = plugins_url()."/visual-editor-custom-buttons";

        for ($i=1;$i<count($btnicons);$i++) {
            $ticon = explode(".", $btnicons[$i]);
            $theicon = str_replace("_"," ",$ticon[0]);
            $theicon = ucfirst($theicon);
            $content .= '<option value="'.$btnicons[$i].'" data-image="'.$url .'/js/icons/'.$btnicons[$i].'" '.$sel[$i].' >'.$theicon.'</option>';
        }

        $content .= '</select></div>';
            $content .= '<div id="vecb_pluginurl" style="display:none">'.plugins_url().'</div>';
            $content .= '<div id="vecb_custompluginurl" style="display:none">'.$uploadurl.'</div>';
            $content .= '<div class="vecb_preview"><div style="padding:23px 0 0 8px"><span id="vecb_btnpreview"><div id="vecb_btnimg"><img src="'.plugins_url().'/visual-editor-custom-buttons/js/icons/none.png"></div></span><div class="vecb_preview_text">Preview</div></div></div>';
            $content .= ' <div class="recb_inputblock"><div class="vecb_label">Custom Icons</div>
    <div class="vecb_desc">Add your custom icons by creating a new folder called <strong>vecb</strong> in the wordpress upload directory and adding your icons (40x40px) there. The correct path for the custom icons should be: <br>
	<strong>...wp-content/uploads/vecb/</strong>. When added, the icons will automatically show up the Button Icon dropdown-menu.
  </div></div>

  ';
            $content .= '</div><div id="vecb_quicktag" class="recb_inputblock"><div class="vecb_label">Quicktag Label</div>
  <div class="vecb_desc">If not set, button title will be used.</div>
  <input type="text" class="vecb_text" value="'.$quicktag.'" name="quicktag"></div>';

            echo $content;
    }

    function vecb_styling_options() {
        global $post;
        $custom = get_post_custom($post->ID);
        $styling = $custom["styling_content"][0];
        $content = '<section class="recb_inputblock"><div class="vecb_label">CSS</div>
<div class="vecb_desc">Only for visualization in the Visual Editor. Use normal stylesheet for Front End styling.</div>
<textarea name="styling_content" id="styling_content" cols="45" rows="5">' . $styling  . '</textarea></section>';
        echo $content;
    }

    function vecb_script_options() {
        global $post;
        $custom = get_post_custom($post->ID);
        $script_content = $custom["script_content"][0];
        $use_script_content = $custom["use_script_content"][0];
        $content = '<section class="recb_inputblock"><div class="vecb_label">JavaScript</div>
<script type="text/javascript">
  function apply_valid_option(el) {
    const toggle = Object.is(parseInt(el.value), NaN) ? 0 : parseInt(el.value);
    el.value = 1 - toggle;
  }
</script>
<div class="vecb_desc">
  <br/>
  <input id="use_script_content" name="use_script_content" type="checkbox" onChange="apply_valid_option(this)" ';
        if($use_script_content == "1") $content .= " checked ";
        $content .= 'value="';
        $content .= $use_script_content;
        $content .= '" /> Use the following script content rather than auto generated</div>
<textarea name="script_content" id="script_content" cols="45" rows="15">' . $script_content  . '</textarea></section>';
        echo $content;
    }


    /* 각 버튼 설정 저장*/
    function vecb_save_options()
    {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

        global $post;
        if ( 'vecb_editor_buttons' == get_post_type() ) :
            update_post_meta($post->ID, "left_tag", $_POST["left_tag"]);
            update_post_meta($post->ID, "right_tag", $_POST["right_tag"]);
            update_post_meta($post->ID, "styling_content", $_POST["styling_content"]);
            update_post_meta($post->ID, "content-block", $_POST["content-block"]);
            update_post_meta($post->ID, "content-type", $_POST["content-type"]);
            update_post_meta($post->ID, "block_content", $_POST["block_content"]);
            update_post_meta($post->ID, "rich_editor", $_POST["rich_editor"]);
            update_post_meta($post->ID, "html_editor", $_POST["html_editor"]);
            update_post_meta($post->ID, "icon", $_POST["icon"]);
            update_post_meta($post->ID, "quicktag", $_POST["quicktag"]);
            update_post_meta($post->ID, "row", $_POST["row"]);
            update_post_meta($post->ID, "use_script_content", $_POST["use_script_content"]);
            update_post_meta($post->ID, "script_content", $_POST["script_content"]);

            $target_post_id = $post->ID;

            // Save updates to files
            $args = array( 'post_type' => 'vecb_editor_buttons',
                           'posts_per_page' => -1,
                           'suppress_filters' => true,
                           'orderby' => 'ID',
                           'order' => 'ASC');
            $loop = new WP_Query( $args );
            // 'ASC'로 달라고해도 $loop 안에서는 DESC 형태로 정렬되어 있다
            $stylefile = WP_PLUGIN_DIR. '/visual-editor-custom-buttons/css/editor-style.css';
            $style = '@charset "UTF-8";
/* CSS Document */
';
            $use_script_content = $_POST["use_script_content"];
            while ( $loop->have_posts() ) :
                $loop->the_post();
            $id = get_the_ID();
            $count += 1;
            $custom = get_post_custom($post->ID);
            $left_tag = $custom["left_tag"][0];
            $right_tag = $custom["right_tag"][0];
            $styling = $custom["styling_content"][0];
            $script_content = $custom["script_content"][0];
            $selection = $custom["content-type"][0];
            $block_content = $custom["block_content"][0];
            $icon = $custom["icon"][0];
            $rownr = $custom["row"][0];

            //Remove Linebreaks
            $block_content = str_replace("\r\n","",$block_content);
            $right_tag = str_replace("\r\n","",$right_tag);
            $left_tag = str_replace("\r\n","",$left_tag);
            $blog_id = get_current_blog_id();
            $file = WP_PLUGIN_DIR. '/visual-editor-custom-buttons/js/button-'.$blog_id.'-'.$count.'.js';
            $current = file_exists($file) ? file_get_contents($file) : "";

            /* Generate Visual Editor Button JS-files */
            if($selection == "wrap") {
                $custom= false;
                $first = substr($icon,0,1);
                if ($first == "_") {
                    $icon = substr($icon, 1);
                    $custom= true;
                }
                $uploads = wp_upload_dir();
                $uploaddir = $uploads['basedir']."/vecb/";
                $uploadurl = $uploads['baseurl']."/vecb/";
                $current = "
(function() {
  tinymce.create('tinymce.plugins.vecb_button".$count."', {
    init : function(ed, url) {
      ed.addButton('vecb_button".$count."', {
        title : '".get_the_title()."',";
                if($custom == false) {
                    $current .= "image : url+'/icons/".$icon."',";
                } else {
                    $current .= "image : '".$uploadurl.$icon."',";
                }
                $current .= " onclick : function() {
              ed.selection.setContent('". $left_tag . "' + ed.selection.getContent() + '". $right_tag ."');
         }
      });
    },
    createControl : function(n, cm) {
        return null;
    },
  });
  tinymce.PluginManager.add('vecb_button".$count."', tinymce.plugins.vecb_button".$count.");
})();
";

            } else {
                /* 단순 넣기 형식의 버튼인 경우 */
                $custom= false;
                $first = substr($icon,0,1);
                if ($first == "_") {
                    $icon = substr($icon, 1);
                    $custom= true;
                }

                $uploads = wp_upload_dir();
                $uploaddir = $uploads['basedir']."/vecb/";
                $uploadurl = $uploads['baseurl']."/vecb/";
                $current = "(function() {
                tinymce.create('tinymce.plugins.vecb_button".$count."', {init : function(ed, url) {ed.addButton('vecb_button".$count."', {title : '".get_the_title()."',";
                if($custom == false) {
                    $current .="image : url+'/icons/".$icon."',";
                } else {
                    $current .="image : '".$uploadurl.$icon."',";
                }
                $current .= "onclick : function() {
                ed.selection.setContent('".$block_content."');}});
            }, createControl : function(n, cm) {return null;},});
                tinymce.PluginManager.add('vecb_button".$count."', tinymce.plugins.vecb_button".$count.");
	    })();
";
            }

            if($id == $target_post_id) {
                if($use_script_content) file_put_contents($file, str_replace("\r","", $script_content));
                else {
                    file_put_contents($file, $current);
                    update_post_meta($post->ID, "script_content", $current);
                }
            }

            $styling = str_replace("\r","",$styling);
                if($styling != "" ) $style .= $styling . "
";
            endwhile;

            // 스타일 생성
        file_put_contents($stylefile, $style);
        endif;
    }
}

/* Add HTML-Editor Button */
if( !function_exists('_add_my_quicktags') ){
    function _add_my_quicktags(){
        global $post;
        $content = '<script type="text/javascript">';

        $args = array( 'post_type' => 'vecb_editor_buttons',
                       'order' => 'asc');


        $loop = new WP_Query( $args );
        $count = 0;

        while ( $loop->have_posts() ) : $loop->the_post();

        $custom = get_post_custom($post->ID);
        $left_tag = $custom["left_tag"][0];
        $right_tag = $custom["right_tag"][0];
        $quicktag = $custom["quicktag"][0];
        $html = $custom["html_editor"][0];
        $radio = $custom["content-type"][0];
        $block_content = $custom["block_content"][0];
        $count++;

        //Remove Linebreaks
        $block_content = str_replace("\r\n","",$block_content);
        $right_tag = str_replace("\r\n","",$right_tag);
        $left_tag = str_replace("\r\n","",$left_tag);
        //

        if ($quicktag != "") {
            $tagtitle = $quicktag;
        } else {
            $tagtitle = get_the_title();
        }

        if ($html == "html_editor") :

            if ($radio == "wrap") {
                $content .= "QTags.addButton( 'btn".$count."', '".$tagtitle."', '".$left_tag."', '".$right_tag."' );
   ";
            } else {
                $content .= "QTags.addButton( 'btn".$count."', '".$tagtitle."', '".$block_content."', '' );
	";
            }
        endif;

        endwhile;

        $content .= "

    QTags.addButton( 'tag', 'Link Tag', prompt_user );
    function prompt_user(e, c, ed) {
        prmt = prompt('Enter Tag Name');
        if ( prmt === null ) return;
        rtrn = '[tag]' + prmt + '[/tag]';
        this.tagStart = rtrn;
        QTags.TagButton.prototype.callback.call(this, e, c, ed);
    }
    </script>";
        echo $content;

    }
    add_action('admin_print_footer_scripts',  '_add_my_quicktags');
}

add_action('admin_init', 'vecb_add_buttons');

/** Create Our Initialization Function */
function vecb_add_buttons() {
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
        return;
    }
    if ( get_user_option('rich_editing') == 'true' ) {
        add_filter( 'mce_external_plugins', 'vecb_add_plugin' );
        $rowvalue = get_option('vecb_row');
        add_filter( 'mce_buttons'.$rowvalue, 'vecb_register_button' );
    }
}

/** Register Button */
function vecb_register_button( $buttons ) {
    $count_posts = wp_count_posts('vecb_editor_buttons');
    $count_posts = $count_posts->publish;
    for ($count=1;$count<=$count_posts;$count++) {
        array_push( $buttons, "vecb_button".$count);
    }
    return $buttons;
}

/** Register TinyMCE Plugin */
function vecb_add_plugin( $plugin_array ) {
    $count_posts = wp_count_posts('vecb_editor_buttons');
    $count_posts = $count_posts->publish;
    $url = plugins_url()."/visual-editor-custom-buttons";
    for ($count=1;$count<=$count_posts;$count++) {
        $blog_id = get_current_blog_id();
        $plugin_array['vecb_button'.$count] = $url.'/js/button-'.$blog_id.'-'.$count.'.js';
    }
    return $plugin_array;
}
?>
