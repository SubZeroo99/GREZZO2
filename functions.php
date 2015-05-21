add_action( 'wp_enqueue_scripts', 'cyb_enqueue_scripts' );
function cyb_enqueue_scripts() {
    //Change the key and url with yours
    wp_register_script('my-js', get_stylesheet_directory_uri(). '/js/my-js.js', array( 'jquery' ) );
    wp_enqueue_script('my-js');
 
    //Localize script data to be used in my-js.js
    $scriptData = array();
    $scriptData['ajaxurl'] = admin_url( 'admin-ajax.php' );
    $scriptData['action'] = 'save_goals_category';
 
    wp_localize_script( 'my-js', 'my_js_data', $scriptData );
 
}
 
add_action("wp_ajax_save_goals_category", "save_goals_category");
add_action("wp_ajax_save_goals_category", "save_goals_category");
 
function save_goals_category(){
       
        $user_id = get_current_user_id();
        if ($user_id == 0) {
                echo 'You are currently not logged in.';
        } else {
                $catarr = array(
                                                  'cat_name' => $_POST['addCatText'],
                                                  'category_description' => 'Users Category',
                                                  'category_nicename' => '',
                                                  'category_parent' => '',
                                                  'taxonomy' => 'goals-category'
                                                  );
         
                $my_cat_id = wp_insert_category( $catarr, $wp_error );
               
                if($my_cat_id !=0){
                        $meta_key = 'cats';
                         $new_meta_key = get_user_meta($user_id, $meta_key,true);
                        if( ! is_array( $new_meta_key ) ) $new_meta_key = array();
                        $new_meta_key[] = $my_cat_id;
                        $meta_value = $new_meta_key;
                        $updateMetaVal = update_user_meta( $user_id, $meta_key, $meta_value);
                        echo json_encode(array('result'=>'success','msg'=>'Category saved successfully','newCategoryId'=>$my_cat_id,'newCategoryName'=>$_POST['addCatText']));
                }else{
                        echo json_encode(array('result'=>'error','msg'=>'Error in saving category'));
                }
        }
        exit;
}
