<?php

class wpGrade_Trial {

    protected $domain;

    public function __construct(){

        $this->domain = 'localhost';
        return;
    }

    public function create_new_trial($username, $user_id, $admin_email, $template_id, $theme_name ){

        $error = NULL;

        //get the data from the array into strings
        $siteurl = $theme_name. '-' .$username;
        $blogdescription = 'Trial '. $username;
        $blogname = ucfirst($username);
        $dashedsiteurl = str_replace('.', '-', $siteurl);
        $domain = get_blog_details(1)->domain;
        $fulldomain = get_blog_details(1)->domain . "/" . $dashedsiteurl;
        $path = "/" . $dashedsiteurl;

        global $wpdb;

        if(!$error){
            if($exist_id = $wpdb->get_var("SELECT blog_id FROM $wpdb->blogs WHERE path = '$path/'")) {
                _e("<span class=\"error\">The URL $fulldomain already exist, we skipped it!</span>", 'wpgrade' );
                $error = TRUE;
            } else {
                // Start with adding the new blog to the blogs table

                $new_blog_id = insert_blog( $domain, $path, '1');

                if(is_integer($new_blog_id)) {
                    _e("New site created with id: $new_blog_id<br/>", 'wpgrade' );
                } else {
                    _e("<span class=\"error\">The URL $fulldomain already exist, we skipped it!</span>", 'wpgrade' );
                    $error = TRUE;
                }
            }
        }


        //Next duplicate all tables from the template
        if(!$error){

            $template_like = $wpdb->prefix . $template_id . "_";
            $template_new = $wpdb->prefix . $new_blog_id . "_";
            $temp_like = str_replace('_', '\_', $template_like); //escape the _ for correct sql!!
            $template_tables = $wpdb->get_results( "SHOW TABLES LIKE '$temp_like%'", ARRAY_N );


            foreach ($template_tables as $old_table) {
                $new_table = str_replace($template_like, $template_new, $old_table[0]);

                // check if table already exists
                if($wpdb->get_var("SHOW TABLES LIKE '$new_table'") != $new_table) {
                    // duplicate the old table structure
                    $result = $wpdb->query( "CREATE TABLE $new_table LIKE $old_table[0]" );
                    if($result === FALSE) {
                        _e("<span class=\"error\">Failed to create $new_table.</span>", 'wpgrade' );
                        $error = TRUE;
                    } else {
//                        _e("Table created: $new_table.<br>", 'wpgrade' );
                        // copy data from old_table to new_table
                        $result = $wpdb->query( "INSERT INTO $new_table SELECT * FROM $old_table[0]" );
                        if($result === FALSE) {
                            _e("<span class=\"error\">Failed to copy data from $old_table[0] to $new_table.</span>", 'wpgrade' );
                            $error = TRUE;
                        } else {
//                            _e("Copied data from $old_table[0] to $new_table.<br/>", 'wpgrade' );

//                            if ( $new_table == $wpdb->prefix . $new_blog_id . "_posts" ) {
//                                $wpdb->query( "UPDATE wp_posts WHERE post_type = `attachment` SET guid = REPLACE (guid, 'http://localhost/site', 'http://newsite')" );
//                            }

                        }
                    }
                } else {
                    _e("<span class=\"error\">The table $new_table already existed.</span>", 'wpgrade' );
                    $error = TRUE;
                }
            }

            if ( !$error ) {
                // update links for all attachments.


            }
        }

        // Then add user to the new blog
        if(!$error) {
            $role = "administrator";
            if ( add_user_to_blog( $new_blog_id, $user_id, $role ) ) {
                _e( 'Added user '.$user_id.' as '.$role.' to site '.$new_blog_id.'.<br/>',  'wpgrade' );

                // remove the new user from the main site ( only super-admin should be there)
                remove_user_from_blog($user_id, 1);

            } else {
                _e( 'Failed to add user '.$user_id.' as '.$role.' to site '.$new_blog_id.'.<br/>', 'wpgrade' );
                $error = TRUE;
            }
        }

        // Add custom data to newly duplicated blog
        if(!$error) {
            $full_url = "http://" . $fulldomain;
            if(!$blogname) { $blogname = $siteurl; }
            $fileupload_url = $full_url . "/files";

            // update the cloned table with the new data and blog_id
            update_blog_option ($new_blog_id, 'siteurl', $full_url);
            update_blog_option ($new_blog_id, 'blogname', $blogname);
            update_blog_option ($new_blog_id, 'blogdescription', $blogdescription);
            update_blog_option ($new_blog_id, 'admin_email', $admin_email);
            update_blog_option ($new_blog_id, 'home', $full_url);
            update_blog_option ($new_blog_id, 'fileupload_url', $fileupload_url);
            update_blog_option ($new_blog_id, 'upload_path', 'wp-content/blogs.dir/' . $new_blog_id . '/files');
            $new_options_table = $wpdb->prefix . $new_blog_id . '_options';
            $old_name = $wpdb->prefix . $template_id . '_user_roles';
            $new_name = $wpdb->prefix . $new_blog_id . '_user_roles';
            $result = $wpdb->update( $new_options_table, array('option_name' => $new_name), array('option_name' => $old_name));

            // 'check' if it went ok - NOTE: is just a basic check could give an error anyway...
            if(get_blog_option($new_blog_id, 'blogdescription') != $blogdescription) {
                //$error = TRUE;
                _e("<span class=\"error\">Maybe we had an error updating the options table with the new data.</span>", 'wpgrade' );
            } else {
                _e("Updated the options table with cloned data<br>", 'wpgrade' );
            }
        }

        // add template_id to option table for later reference
        if(!$error) {
            $savearray = array ('template-id' => $template_id, 'lasttime' => time());
            add_blog_option ($new_blog_id, 'add-cloned-sites', serialize($savearray));
            //get it back with:
            //get_option('add-cloned-sites') == "" ? "" : $new = unserialize(get_option('add-cloned-sites'));
        }

        //reset permalink structure
//        if(!$error) {
//            switch_to_blog($new_blog_id);
//            //_e("Switched from here to $new_blog_id to reset permalinks<br>", 'wpgrade' );
//            global $wp_rewrite;
////            $wp_rewrite->init();
//            $wp_rewrite->flush_rules();
//            flush_rewrite_rules();
//            //now that we are here, update the date of the new site
////            wpmu_update_blogs_date( );
//            //go back to admin
////            restore_current_blog();
//            //_e("Permalinks updated.<br>", 'wpgrade' );
//        }

        // count succesfull and failed sites
        if(!$error) {
            _e("Job done, sucesfully created <a href=\"http://$fulldomain\">$fulldomain</a> with site id: $new_blog_id<br>", 'wpgrade' );
        } else {
            // count failed sites
            $failed[] = $siteurl;
            $error = NULL;
        }


    }

    public function create_user_for_trial($username,$email){

        if ( username_exists( $username ) ){

            $user_id = username_exists( $username );

        } elseif ( email_exists($email) ) {

            $user_id = email_exists($email);

        } else {
            $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            $user_data = array(
                'user_pass' => 'admin', //$random_password,
                'user_login' => $username,
                'user_nicename' => $username,
                'user_email' => $email
            );

            $user_id = wp_insert_user( $user_data );
        }


        return $user_id;
    }

}

function you_need_access() {
//    $file = '/usr/share/nginx/trial/test.txt';
//
//    // Open the file to get existing content
//    $current = file_get_contents($file);
//// Append a new person to the file
//    $current .= "Cron with NO NO NO access \n";
//// Write the contents back to the file
//    file_put_contents($file, $current);

}

function clear_old_trials(){
    // get a list with all blog id's
    global $wpdb;

    // life sucks ... you need to exclude the blog demos from here
    // 1 = the godfather
    // 2 = senna
    // 3 = swipe

    $blogs = $wpdb->get_results("
        SELECT blog_id
        FROM {$wpdb->blogs}
        WHERE site_id = '{$wpdb->siteid}'
        AND spam = '0'
        AND deleted = '0'
        AND archived = '0'
        AND blog_id != 1
        AND blog_id != 2
        AND blog_id != 3
    ");

    ob_start();

    foreach ( $blogs as $blog ) {

        // the freaking date
        $registered_date = get_blog_details($blog->blog_id)->registered;
        $now = new DateTime();
//        echo '<pre>';
//        var_dump( $registered_date );
//        echo '</pre>';

        $start_date = new DateTime( $registered_date );
        $since_start = $start_date->diff( $now );
//        echo $since_start->days.' days total<br>';
//        echo $since_start->y.' years<br>';
//        echo $since_start->m.' months<br>';
//        echo $since_start->d.' days<br>';
//        echo $since_start->h.' hours<br>';
//        echo $since_start->i.' minutes<br>';
//        echo $since_start->s.' seconds<br>';

        if ( $since_start->h >= 3 ) {
            echo '<p>I\'m kinda sorry but your are cut out</p>';

            wpmu_delete_blog( $blog->blog_id , true);

        } else {

            echo '<p>you will live to tell teh story</p>';

        }

    }

    echo ob_get_clean();
    exit('Ciao!');
}

add_action('wp_ajax_clear_old_trials', 'clear_old_trials');
add_action('wp_ajax_nopriv_clear_old_trials', 'you_need_access');