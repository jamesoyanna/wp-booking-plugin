<?php
namespace BackhoeBooking;
class backhoe_booking_post_type 
{
    function post_type_pricing_structure() {
        $name = 'Price Structure';
        $singular_name = 'Price Structure';
        register_post_type( 
            'pricing_structure',
            array(
                'labels' => array(
                    'name'               => esc_html__( $name, 'backhoebooking' ),
                    'singular_name'      => esc_html__( $singular_name, 'backhoebooking'),
                    'menu_name'          => esc_html__( $name, 'backhoebooking' ),
                    'name_admin_bar'     => esc_html__( $singular_name, 'backhoebooking' ),
                    'add_new'            => esc_html__( 'Add New', 'backhoebooking' ),
                    'add_new_item'       => esc_html__( 'Add New ' . $singular_name, 'backhoebooking' ),
                    'new_item'           => esc_html__( 'New ' . $singular_name, 'backhoebooking' ),
                    'edit_item'          => esc_html__( 'Edit ' . $singular_name , 'backhoebooking' ),
                    'view_item'          => esc_html__( 'View ' . $singular_name , 'backhoebooking' ),
                    'all_items'          => esc_html__( 'All ' . $name , 'backhoebooking'),
                    'search_items'       => esc_html__( 'Search ' . $name , 'backhoebooking'),
                    'parent_item_colon'  => esc_html__( 'Parent :' . $name , 'backhoebooking'),
                    'not_found'          => esc_html__( 'No ' . strtolower( $name ) , 'backhoebooking'),
                    'not_found_in_trash' => esc_html__( 'No ' . strtolower( $name )  , 'backhoebooking')
                ),
                'supports'            => array( 'title' ),
                'public'             => true,
                'hierarchical'       => false,
                'rewrite'            => array( 'slug' => $name ),
            )
        );
    }

    function pricing_structure_add_meta_boxes() {
        add_meta_box( 'pricing_structure-fields', 'Pricing Structure', array($this,'pricing_structure_meta_box_display'), 'pricing_structure', 'normal', 'default');
        add_meta_box( 'pricing_structure-extra-duration-fields', 'Extra Duration', array($this,'pricing_structure_extra_duration_meta_box_display'), 'pricing_structure', 'normal', 'default');
    }
 function pricing_structure_extra_duration_meta_box_display() {
    global $post;
    $extra_multiplier = get_post_meta($post->ID, 'extra_multiplier', true);

     ?>
     <table>
        <thead>
            <tr>
                <th width="30%">Each extra</th>
                <th width="10%">Multiplier</th>
                <th width="35%">Product Price</th>
            </tr>
        </thead>
        <tr>
            <td>days</td>
            <td><input type="text" class="widefat" name="extra_multiplier" value="<?php echo $extra_multiplier ?>" /></td>
            <td> * product price</td>
        
           
        </tr>
        <tbody>

        </tbody>
     </table>
     <?php 
 }
    function pricing_structure_meta_box_display() {
        global $post;
    
        $repeatable_fields = get_post_meta($post->ID, 'pricing_structure_fields', true);
       
        wp_nonce_field( 'pricing_structure_meta_box_nonce', 'pricing_structure_meta_box_nonce' );
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function( $ ){
            $( '#add-row' ).on('click', function() {
                var row = $( '.empty-row.screen-reader-text' ).clone(true);
                row.removeClass( 'empty-row screen-reader-text' );
                row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
                return false;
            });
          
            $( '.remove-row' ).on('click', function() {
                $(this).parents('tr').remove();
                return false;
            });
        });
        </script>
      
        <table id="repeatable-fieldset-one" width="100%">
        <thead>
            <tr>
                <th width="30%">Duration</th>
                <th width="10%">By</th>
                <th width="35%">Multiplier</th>
                <th width="15%">Product Price</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody>
        <?php
        
        if ( $repeatable_fields ) :
        
        foreach ( $repeatable_fields as $field ) {
        ?>
        <tr>
            <td><input type="text" class="widefat" name="duration[]" value="<?php if($field['duration'] != '') echo esc_attr( $field['duration'] ); ?>" /></td>
            <td>Days</td>
            <td><input type="text" class="widefat" name="multiplier[]" value="<?php if ($field['multiplier'] != '') echo esc_attr( $field['multiplier'] );  ?>" /></td>
            <td> * product price</td>
        
            <td><a class="button remove-row" href="#">Remove</a></td>
        </tr>
        <?php
        }
        else :
        // show a blank one
        ?>
        <tr>
            <td><input type="text" class="widefat" name="duration[]" /></td>
            <td>Days</td>
            <td><input type="text" class="widefat" name="multiplier[]" value="" /></td>
            <td> * product price</td>
        
            <td><a class="button remove-row" href="#">Remove</a></td>
        </tr>
        <?php endif; ?>
        
        <!-- empty hidden one for jQuery -->
        <tr class="empty-row screen-reader-text">
            <td><input type="text" class="widefat" name="duration[]" /></td>
            <td> Days</td>
            <td><input type="text" class="widefat" name="multiplier[]" value="" /></td>
            <td> * product price</td>
              
            <td><a class="button remove-row" href="#">Remove</a></td>
        </tr>
        </tbody>
        </table>
        
        <p><a id="add-row" class="button" href="#">Add another</a></p>
        <?php
    }

    function pricing_structure_meta_box_save($post_id) {
        if ( ! isset( $_POST['pricing_structure_meta_box_nonce'] ) ||
        ! wp_verify_nonce( $_POST['pricing_structure_meta_box_nonce'], 'pricing_structure_meta_box_nonce' ) )
            return;
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        
        if (!current_user_can('edit_post', $post_id))
            return;
        
        $old = get_post_meta($post_id, 'pricing_structure_fields', true);
        $oldpricing_structure_fields = get_post_meta($post_id, 'extra_multiplier', true);

        $new = array();
       
        
        $names = $_POST['duration'];
        $urls = $_POST['multiplier'];
        $extra_multiplier = $_POST['extra_multiplier'];

        $count = count( $names );
        
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $names[$i] != '' ) :
                $new[$i]['duration'] = stripslashes( strip_tags( $names[$i] ) );
                
               
            
                if ( $urls[$i] == '' )
                    $new[$i]['multiplier'] = '1';
                else
                    $new[$i]['multiplier'] = stripslashes( $urls[$i] ); // and however you want to sanitize
            endif;
        }
    
        if ( !empty( $new ) && $new != $old ) {
            update_post_meta( $post_id, 'pricing_structure_fields', $new );
           

        }elseif ( empty($new) && $old ){
            delete_post_meta( $post_id, 'pricing_structure_fields', $old );
           
        }
        
        delete_post_meta( $post_id, 'extra_multiplier', $oldpricing_structure_fields );
        update_post_meta( $post_id, 'extra_multiplier', $extra_multiplier );
    }
} 