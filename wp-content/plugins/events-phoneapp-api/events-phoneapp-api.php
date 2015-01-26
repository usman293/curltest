<?php
/*Plugin Name: Event Phone app api
Plugin URI: http://wpquantum.com/
Description: Give data to Phone app from events and pages
Version: 1.0
Author: Usman Iqbal
Author URI: http://wpquantum.com/
*/

/**
 *  nb_user_oauth_login for user login from Oauth
 */
class nb_user_oauth_login{
    private $plugin_url  = null; 
  
    function __construct(){
        $this->plugin_url    = plugins_url( '' , __FILE__ );
        add_action( 'init', array( $this, 'nb_wpa_autologin_via_url' ));
        add_shortcode( 'googleoauth_api_call', array( $this, 'nb_googleoauth_api_call' ) );
        add_shortcode( 'get_googleoauth_response', array( $this, 'nb_get_googleoauth_response' ) );    
    }

    /**
     *  nb_googleoauth_api_call Shortcode for sending authenticating request to app
     */
    public function nb_googleoauth_api_call(){
        if(isset($_POST['user_authentication'])){
            $hostname = get_option('oauth_app_host_url');
            //$url = "http://cwa1150.uconnect.dev:3000/oauth/authorize/"; 
            $url = $hostname."oauth/authorize/"; 
            $params = array(
                "response_type" => "code",
                "client_id" => get_option('oauth_client_id'),
                "redirect_uri" => get_option('oauth_redirect_uri'),
                //"client_secret" => get_option('oauth_client_secret'),
                //"scope" => "https://www.googleapis.com/auth/userinfo.email"
                );                                       
           $request_to = $url . '?' . http_build_query($params);
           
           echo '<script>location.href="'.$request_to.'";</script>';
        }
        
        echo '<form style="margin-left:100px;width:80%" method="post" action="">';
            //echo 'Email <input type="text" name="user_email" value=""><br>' ;
            echo '<input type="submit" name="user_authentication" value="Login With Gmail">';    
        echo '</form>';             
    }//public function nb_googleoauth_api_call()
    
    /**
     *  xml_execution using for sending access token curl request to app
     */
    public function xml_execution($url_exec , $params_exec){
        $ch = curl_init(); 
        curl_setopt($ch,CURLOPT_URL,$url_exec);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, count($params_exec));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_exec);   
        $output = curl_exec($ch);
        curl_close($ch);
        
        $output = json_decode($output);
        
        return $output;
    }

    /**
     *  userprofile_xml_execution using for sending userprofile curl request with access token to app
     */
    public function userprofile_xml_execution( $url_info , $accessToken ){

        $curl = curl_init($url_info);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        $curlheader[0] = "Authorization: Bearer " . $accessToken;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

        $json_response = curl_exec($curl);
        curl_close($curl);

        $responseObj = json_decode($json_response);

        return $responseObj;
    }

    /**
     *  nb_wpa_autologin_via_url using for logging user into wordpress after receiving userinfo from app
     */
    function nb_wpa_autologin_via_url(){
        if(isset($_GET['nb_login_user_automatically_1'])){

            $email = $_GET['user_goole_email'];
            $uname = $_GET['user_google_uname'];
            $user_app_id = $_GET['user_google_id'];

            $user = get_user_by( 'email', $email );
            if(!empty($user)){
                update_user_meta($user->data->ID , 'user_token' , $output->access_token);
                $nb_userid = $user->data->ID;
                if ( !is_user_logged_in() ){
                    $res = wp_set_auth_cookie($user->data->ID);
                }
                wp_redirect(home_url());
                exit;
                
            }else{
                $name = strtolower($uname);
                //$name = str_replace(" ","_", trim($name));
                $name = $name.$user_app_id;
                $userdata = array(
                    'user_login'  =>  $name,
                    'user_email' => $email ,
                    'user_pass'   =>  NULL  // When creating an user, `user_pass` is expected.
                );
                $user_id = wp_insert_user( $userdata ) ;
                update_user_meta($user_id , 'user_token' , $output->access_token , $name);  
                if ( !is_user_logged_in() ){
                    wp_set_auth_cookie($user_id);
                }
                wp_redirect(home_url());
                exit;
            }   
        }
    }

    /**
     *  nb_get_googleoauth_response shortcode using for access token and user profile info
     */
    public function nb_get_googleoauth_response(){
        // echo '<pre>';
        // print_r($_GET);
        // echo '</pre>';

       if(isset($_GET['code'])) {
            // try to get an access token
            $code = $_GET['code'];
            //$url = 'https://accounts.google.com/o/oauth2/token';
            $hostname = get_option('oauth_app_host_url');
            // $url = "http://cwa1150.uconnect.dev:3000/oauth/token/";
            $url = $hostname."oauth/token/";
            $params = array(
                "code" => $code,
                "client_id" => get_option('oauth_client_id'),
                "client_secret" => get_option('oauth_client_secret'),
                "redirect_uri" => get_option('oauth_redirect_uri'),
                "grant_type" => "authorization_code"
            );

            $data_code = self::xml_execution($url , $params);                      

            $accessToken = $data_code->access_token;           

            if(!empty($accessToken)){

                //$url_info = 'https://www.googleapis.com/oauth2/v1/userinfo';
                $hostname = get_option('oauth_app_host_url');
                $url_info = $hostname."userinfo/";
                //$url_info = "http://cwa1150.uconnect.dev:3000/userinfo/";
                $user_profile =  self::userprofile_xml_execution($url_info , $accessToken);

                $email = $user_profile->email;
                $uname = $user_profile->first_name;
                $uid = $user_profile->id;

                $argss = array(
                    "nb_login_user_automatically_1" => "",
                    "user_goole_email" => $email,
                    "user_google_uname" => $uname,
                    "user_google_id"    => $uid
                    );                  
               $request_to = get_bloginfo('url') . '/?' . http_build_query($argss);
               echo '<script>location.href="'.$request_to.'";</script>';               
            }
        }
    }//public function nb_get_googleoauth_response()
}//class nb_user_oauth_login
new nb_user_oauth_login();

/**
 *  nb_events_phoneapp_api using for giving menus,pages and events data to app
 */
class nb_events_phoneapp_api{
    private $plugin_slug           = 'events-phoneapp-api';
    private $plugin_url            = null;
    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    function __construct(){
        $this->plugin_url     = plugins_url( '' , __FILE__ );
        add_action( 'init', array( $this, '_init' ) );
        add_action('admin_menu', array($this,'nb_add_admin_menus'));
        add_action( 'add_meta_boxes', array( $this, 'nb_add_meta_box_for_api' ) );
        add_action( 'save_post', array( $this, 'nb_save_meta_box_data' ) );
    }
    /**
     *  _init using for returning json on app request
     */
    function _init(){
        if( is_admin() ){
            wp_enqueue_style( $this->plugin_slug, $this->plugin_url . '/css/phoneappapi.css' );
        }    
        if(isset($_POST['event_phoneapp_api_request'])) {
            $selected_menu = get_option('selected_menu_for_app');
            $menus = get_registered_nav_menus();
            $locations = get_nav_menu_locations();
            $menu = wp_get_nav_menu_object( $locations[$selected_menu] );
            $menuitems = wp_get_nav_menu_items( $menu->term_id );
            $json_data = array();
            //$json_concate_data = '['; 
            /**
             * For returning Parent Menus
             */
            if(isset($_POST['get_parent_menus'])){
                foreach ($menuitems as $page) {
                    $data_ = array();
                    $yesno = get_post_meta( $page->object_id, 'api_include_yes_no_key', true );
                    if($page->menu_item_parent == 0 && $yesno == 'yes'){
                        $data_[] = $page->ID;
                        $data_[] = $page->title;
                        $json_data[] = $data_;   
                    }  
                }
                $json_encoded_data = json_encode($json_data);
                echo $json_encoded_data;
                die();  
            }
            /**
             * For returning Child Menus from Parent Id
             */
            if(isset($_POST['get_child_for_this_id'])){
                $parent_id = $_POST['get_child_for_this_id'];
                foreach ($menuitems as $page) {
                    $data_ = array();
                    $yesno = get_post_meta( $page->object_id, 'api_include_yes_no_key', true );
                    if($page->ID == $parent_id){
                        $real_post_obj = get_post($page->object_id); 
                        if(!empty($real_post_obj->post_content)){
                            $data_[] = $page->ID;
                            $data_[] = $page->title;
                            $content = apply_filters( 'the_content', $real_post_obj->post_content );
                            // $args=array('post_type'=>'page','post__in' => array($page->object_id));
                            // query_posts($args);
                            // while ( have_posts() ) : the_post();
                            //     ob_start(); 
                            //     the_content();
                            //     $content = ob_get_clean();
                            // endwhile;
                            // wp_reset_query();
                            $data_[] = stripslashes( $content );;
                            $json_data[] = $data_;             
                        }
                    }
                    if($page->menu_item_parent == $parent_id && $yesno == 'yes'){
                        $real_post_obj = get_post($page->object_id); 
                        $data_[] = $page->ID;
                        $data_[] = $page->title;
                        $content = apply_filters( 'the_content', $real_post_obj->post_content ); 
                        // $args=array('post_type'=>'page','post__in' => array($page->object_id));
                        // query_posts($args);
                        // while ( have_posts() ) : the_post();
                        //     ob_start(); 
                        //     the_content();
                        //     $content = ob_get_clean();
                        // endwhile;
                        // wp_reset_query(); 
                        //$content = wp_strip_all_tags( $content );
                        $data_[] = stripslashes( $content );
                        $json_data[] = $data_;   
                    }  
                }
                 $json_encoded_data = json_encode($json_data);
                echo $json_encoded_data;
                die(); 
            }
            /**
             * For returning Events Data
             */
            if(isset($_POST['get_events_data'])){
                $event_query = new WP_Query( "post_type=event&meta_key=api_include_yes_no_key&meta_value=yes&order=ASC" );
                foreach ($event_query->posts as $event) {
                    $data_ = array(); 
                    $data_[] = $event->ID;
                    $data_[] = $event->post_title;
                    $content = apply_filters( 'the_content', $event->post_content );
                    $data_[] = stripslashes( $content );  
                    $json_data[] = $data_;   
                }
                $json_encoded_data = json_encode($json_data);
                echo $json_encoded_data;
                die();  
            }
           
        }

        
    }

    /**
     *  nb_add_admin_menus  for adding admin menus
     */
    public function nb_add_admin_menus(){
         add_menu_page( 'Select Menu', 'Select Menu', 'administrator', $this->plugin_slug, array(&$this, 'nb_for_selecting_menu'));
         add_submenu_page( $this->plugin_slug , 'Oauth2 Credentials', 'Oauth2 Credentials', 'administrator', 'oauth-credentials', array(&$this, 'nb_oauth_credentials_settings'));
    }

    /**
     *  nb_oauth_credentials_settings  for oauth credentials settings 
     */
    public function nb_oauth_credentials_settings(){
        if(isset($_POST['oauth_credentials_submittion'])){
            $oauth_client_id = stripslashes($_POST['oauth_client_id']);
            $oauth_client_secret = stripslashes($_POST['oauth_client_secret']);
            $oauth_redirect_uri = stripslashes($_POST['oauth_redirect_uri']);
            $oauth_app_host_url = stripslashes($_POST['oauth_app_host_url']);
            update_option('oauth_client_id' , trim($oauth_client_id));
            update_option('oauth_client_secret' , trim($oauth_client_secret));
            update_option('oauth_redirect_uri' , trim($oauth_redirect_uri));
            update_option('oauth_app_host_url' , trim($oauth_app_host_url));
        }
        echo '<h2>Oauth2 Credentials</h2>';
        echo '<form class="oauth2_form_cred" method="post" action="">';
            echo '<label>CLient Id</label><br>';
            echo '<input type="text" name="oauth_client_id" value="'.get_option('oauth_client_id').'" /><br>';

            echo '<label>Client Secret</label><br>';
            echo '<input type="text" name="oauth_client_secret" value="'.get_option('oauth_client_secret').'" /><br>';

            echo '<label>Redirect Url</label><br>';
            echo '<input type="text" name="oauth_redirect_uri" value="'.get_option('oauth_redirect_uri').'" /><br>';

            echo '<label>App Host Url</label><br>';
            echo '<input type="text" name="oauth_app_host_url" value="'.get_option('oauth_app_host_url').'" /><br>';

            echo '<input type="submit" class="button button-primary button-large" name="oauth_credentials_submittion" value="Save" >';
        echo '</form>';
    }
    /**
     *  nb_oauth_credentials_settings  for selecting menu
     */
    public function nb_for_selecting_menu(){
        echo "<h1>Menu Selection</h1>";
        if(isset($_POST['save_selected_menu_for_app'])){
            update_option('selected_menu_for_app', $_POST['selected_menu_for_app']);
        }
        $list_of_reg_menus = get_registered_nav_menus();
        $selected_menu = get_option('selected_menu_for_app');
        echo '<form method="post" action="">';
            echo '<label>Select Menu name</label>';
            echo '<select name="selected_menu_for_app">';
            foreach ($list_of_reg_menus as $location => $description) {
                $selected = '';
                if($selected_menu == $location){
                    $selected = 'selected';
                }
                echo '<option value="'.$location.'" '.$selected.'>'.$description.'</option>';
            }
            echo '</select>';
            echo '<input type="submit" class="button button-primary button-large" value="Save" name="save_selected_menu_for_app">';
        echo '</form>';         
    }
    /**
     * Adds the meta box container.
     */
    public function nb_add_meta_box_for_api( $post_type ) {
        $post_types = array('event', 'page');     //limit meta box to certain post types
        if ( in_array( $post_type, $post_types )) {
            add_meta_box(
                'include_phoneapp_api'
                ,__( 'Include In Api', 'myplugin_textdomain' )
                ,array( $this, 'nb_render_meta_box_content' )
                ,$post_type
                ,'advanced'
                ,'high'
            );
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function nb_render_meta_box_content( $post ) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'nb_events_phoneapp_api_action', 'nb_events_phone_api_nonce' );
        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta( $post->ID, 'api_include_yes_no_key', true );
        $yes = $no = '';
        if($value == 'yes'){
            $yes = "selected";
        }else if($value == 'no'){
            $no = "selected";
        }
        // Display the form, using the current value.
        echo '<label for="myplugin_new_field">';
        _e( 'Include in api', 'myplugin_textdomain' );
        echo '</label> ';
        echo '<select name="api_include_yes_no"><option  value="">Choose Option</option>';
        echo '<option value="yes" '.$yes.'>Yes</option><option value="no" '.$no.'>No</option>';
        echo '</select>';        
    }
    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function nb_save_meta_box_data( $post_id ) {
    
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['nb_events_phone_api_nonce'] ) )
            return $post_id;

        $nonce = $_POST['nb_events_phone_api_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'nb_events_phoneapp_api_action' ) )
            return $post_id;

        // If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $post_id;

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) )
                return $post_id;
    
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize the user input.
        $mydata = sanitize_text_field( $_POST['api_include_yes_no'] );

        // Update the meta field.
        update_post_meta( $post_id, 'api_include_yes_no_key', $mydata );
    }
   
}//    class nb_events_phoneapp_api
new nb_events_phoneapp_api();