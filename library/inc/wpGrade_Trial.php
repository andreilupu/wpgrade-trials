<?php

class wpGrade_Trial {

    protected $role;
    protected $log_file;
    protected $themes;
    protected $blogdir_path;
    public $email_sender;
    public function __construct( $config ){

        $this->role = $config['role'];
        $this->log_file = $config['log_path'];
        $this->blogdir_path = $config['blogdir_path'];
        $this->themes = $config['themes'];
        $this->email_sender = 'euthelup@gmail.com';

        add_action('wp_ajax_clear_old_trials', array( $this, 'clear_old_trials' ), 1 );
        add_action('wp_ajax_nopriv_clear_old_trials', array( $this, 'clear_old_trials' ), 1 );
        return;
    }

    public function generate_themes_select_options(){
        ob_start();
        foreach ( $this->themes as $key => $theme ) {
            echo '<option value="'. $key .'_'. $theme .'">'. ucfirst($theme) .'</option>';
        }
        echo ob_get_clean();
    }

    protected function log_to_file( $str, $user ){
        $date = date('Y-m-d h-i-s');
        $ip = $_SERVER["REMOTE_ADDR"];
        $current = file_get_contents($this->log_file);
        $current .= "***[ $date ]*** LOG: $str *** BY User: $user; IP : $ip; ***\r\n ";
        file_put_contents( $this->log_file, $current );
    }

    public function create_new_trial($args){

        $username =$args['username'];
        $user_id = $args['user_id'];
        $admin_email = $args['email'];
        $template_id = $args['blog_id'];
        $theme_name = $args['theme_name'];

        $new_blog_id = 1;// for fallback
        $error = array();
        $success = array();
        //get the data from the array into strings
        $siteurl = $theme_name. '-' .$username;
        $blogdescription = ucfirst( $theme_name). ' Trial for '. $username;
        $blogname = ucfirst($username);
        $dashedsiteurl = str_replace('.', '-', $siteurl);
        $domain = get_blog_details(1)->domain;
        $fulldomain = get_blog_details(1)->domain . "/" . $dashedsiteurl;
        $path = "/" . $dashedsiteurl;
        global $wpdb;

        if( empty( $error ) ){
            if($exist_id = $wpdb->get_var("SELECT blog_id FROM $wpdb->blogs WHERE path = '$path/'")) {
                $this->log_to_file("The URL $fulldomain already exist, we skipped it!", $username );
                $error['domain_exists'] = "This domain already exists.";
            } else {
                // Start with adding the new blog to the blogs table
                $new_blog_id = insert_blog( $domain, $path, '1');
                if(is_integer($new_blog_id)) {
                    $this->log_to_file("New site created with id: $new_blog_id", $username );
                    $success['blog_created'] = "Your blog has been created";
                } else {
                    $this->log_to_file("The URL $fulldomain already exist, we skipped it!", $username );
                    $error['domain_exists'] = "This domain already exists.";
                }
            }
        }

        //Next duplicate all tables from the template
        if( empty( $error ) ){

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
                        $this->log_to_file("Failed to create $new_table.", $username );
                        $error['creat_table'] = "Failed to create $new_table.";
                    } else {
                        // copy data from old_table to new_table
                        $result = $wpdb->query( "INSERT INTO $new_table SELECT * FROM $old_table[0]" );
                        if($result === FALSE) {
                            $this->log_to_file("Failed to copy data from $old_table[0] to $new_table.", $username );
                            $error['copy_table'] = "Failed to copy data from $old_table[0] to $new_table.";
                        } else {
//                            _e("Copied data from $old_table[0] to $new_table.<br/>", 'wpgrade' );
                        }
                    }
                } else {
                    $this->log_to_file("The table $new_table already existed.", 'wpgrade' );
                    $error['table_exists'] = "The table $new_table already existed.";
                }
            }

            if ( empty($error) ) {
                // update links for all attachments.
            }
        }

        // Then add user to the new blog
        if( empty($error) ) {
            $role = $this->role;
            if ( add_user_to_blog( $new_blog_id, $user_id, $role ) ) {
                $this->log_to_file("Added user ".$user_id.' as '.$role.' to site '.$new_blog_id.'.', $username );
            } else {
                $this->log_to_file('Failed to add user '.$user_id.' as '.$role.' to site '.$new_blog_id.'.', $username );
                $error['add_user_to_blog'] = 'Failed to add user '.$user_id.' as '.$role.' to site '.$new_blog_id.'.';
            }
        }

        // Add custom data to newly duplicated blog
        if(empty($error)) {
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
                $error['update_tables'] = 'Maybe we had an error updating the options table with the new data.';
                $this->log_to_file("Maybe we had an error updating the options table with the new data.", $username );
            } else {
                $this->log_to_file("Updated the options table with cloned data", $username );
            }
        }

        // add template_id to option table for later reference
        if(empty($error)) {
            $savearray = array ('template-id' => $template_id, 'lasttime' => time());
            add_blog_option ($new_blog_id, 'add-cloned-sites', serialize($savearray));
            //get it back with:
            //get_option('add-cloned-sites') == "" ? "" : $new = unserialize(get_option('add-cloned-sites'));
        }

        if ( empty($error) ) {

            global $wp_filesystem;

            $dir_to_copy = ABSPATH . 'wp-content/blogs.dir/' . $template_id . '/files/sites/'. $template_id;
            $dir_to_copy_into = ABSPATH .'wp-content/blogs.dir/' . $new_blog_id . '/files/sites/'. $new_blog_id;

            if ( is_dir( $dir_to_copy ) ) {

                if ( wp_mkdir_p( $dir_to_copy_into ) ) {

                    require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
                    require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );

                    if( isset( $wp_filesystem ) )
                        $orig_filesystem = wp_clone( $wp_filesystem );
                    $wp_filesystem = new WP_Filesystem_Direct( false );

                    if ( ! defined('FS_CHMOD_DIR') )
                        define('FS_CHMOD_DIR', 0755 );
                    if ( ! defined('FS_CHMOD_FILE') )
                        define('FS_CHMOD_FILE', 0644 );

                    $this->copy_directory( $dir_to_copy, $dir_to_copy_into );

                    unset( $wp_filesystem );
                    if( isset( $orig_filesystem ) )
                        $wp_filesystem = wp_clone( $orig_filesystem );

                    if ( @file_exists( $dir_to_copy_into . '/sitemap.xml' ) )
                        @unlink( $dir_to_copy_into . '/sitemap.xml' );

                } else {
                    _e("<span class=\"error\">Was unable to copy images and uploads!</span>", 'wpgrade' );
                }
            }
        }

        if ( empty($error) ) {
            $this->log_to_file("Job done, successfully created $fulldomain with site id: $new_blog_id", $username );
            $success['success'] = true;
            $success['url'] = get_blog_option($new_blog_id, 'siteurl ');
            return $success;
        } else {
            $error['success'] = false; // no shit sherlock !?
            return $error;
        }
    }

    public function create_user($username,$email){

        $return = array();
        if ( email_exists($email) ) {
            $return['id'] = email_exists($email);
            $return['msg'] = '<div class="alert">This user already exists.</div>';
            $return['note'] = 'email_exists';
        } elseif ( username_exists( $username ) ){
            $return['id'] = username_exists( $username );
            $return['msg'] = '<div class="alert alert-error">This user already exists. You will need to go back and pick a different username</div>';
            $return['note'] = 'user_exists';
        } else {
            $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
            $user_data = array(
                'user_pass' => 'admin',//$random_password,
                'user_login' => $username,
                'user_nicename' => $username,
                'user_email' => $email,
                'role' => $this->role
            );
            $return['id'] = wp_insert_user( $user_data );

            if ( !is_wp_error($return['id']) ) {

                $subject = 'Welcome to our trial websites '.$username;
                $message = "Your account has been created. These are your credentials :  \r\n\n";
                $message .= "User : ".$username."\r\n";
                $message .= "Password : $random_password \r\n\n";
                $message .= "You will also recive an email with a confirmation link for your new trial. \r\n";
                $message .= "Have a nice day!";

                // To send HTML mail, the Content-type header must be set
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'To: '. $username .' <'. $email .'>' . "\r\n";
                $headers .= 'From: Pixelgrade Media <'.$this->email_sender.'>' . "\r\n";
                wp_mail($email, $subject, $message, $headers);

                $return['msg'] = '<div class="alert alert-success">User created. An email was sent to you with credentials.</div>';
            }
        }
        return $return;
    }

    public function clear_old_trials() {

        global $wpdb;
        $string = '';
        foreach ( $this->themes as $key => $theme ) {
            $string .= ' AND blog_id != '.$key;
        }

        $blogs = $wpdb->get_results("
        SELECT blog_id
        FROM {$wpdb->blogs}
        WHERE site_id = '{$wpdb->siteid}'
        AND spam = '0'
        AND deleted = '0'
        AND archived = '0'
        AND blog_id != 1".$string);
//        ob_start();

        foreach ( $blogs as $blog ) {

            // the freaking date
            $registered_date = get_blog_details($blog->blog_id)->registered;
            $now = new DateTime();
            $start_date = new DateTime( $registered_date );
            $since_start = $start_date->diff( $now );
            if ( $since_start->s >= 1 ) {

                wpmu_delete_blog( $blog->blog_id , true);
                $path = $this->blogdir_path . $blog->blog_id;

                if ( is_dir( $path ) ) {
                    $this->remove_blog_uploads( $path );
                } else {
                    var_dump($path);
                }

                $this->log_to_file('Blog: '. $blog->blog_id .' expired. Deleting ... ', "CRONTAB" );
            } else {
                //echo '<p>you will live to tell teh story</p>';
            }
        }
//        echo ob_get_clean();
        die();
    }

    public function copy_directory( $source, $destination ) {
        if ( is_dir( $source ) ) {
            @mkdir( $destination );
            $directory = dir( $source );
            while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
                if ( $readdirectory == '.' || $readdirectory == '..' ) {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory;
                if ( is_dir( $PathDir ) ) {
                    $this->copy_directory( $PathDir, $destination . '/' . $readdirectory );
                    continue;
                }
                copy( $PathDir, $destination . '/' . $readdirectory );
            }

            $directory->close();
        }else {
            copy( $source, $destination );
        }
    }

    public function remove_blog_uploads($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->remove_blog_uploads($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
            return true;
        } else {
            return false;
        }
    }

}