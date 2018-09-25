<?php
   /*
   Plugin Name: Simple Google Tag Manager
   Plugin URI: http://fabiowallner.com
   description: Add a google tag manager code without touching any php files.
   Version: 1.0
   Author: Fabio Wallner
   Author URI: http://fabiowallner.com
   License: GPL2
   */

   add_action('admin_init', 'sgtm_settings_init' );
   add_action('admin_menu', 'sgtm_options_page' );


   function sgtm_tagmanager() {
      $options = get_option( 'sgtm_options' );

      if(!isset($options['sgtm_field_code'])) {
         return;
      }

      ?>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= $options['sgtm_field_code'] ?>');</script>
<!-- End Google Tag Manager -->

      <?php
   }

   add_action('wp_head', 'sgtm_tagmanager');

   add_filter('body_class', 'sgtm_body', 1000000); // LOWEST PRIORITY SO IT DOES NOT BREAK!!!

   function sgtm_body($classes) {
      $options = get_option( 'sgtm_options' );
      
      if(!isset($options['sgtm_field_code'])) {
         echo "not set!";
         return $classes;
      }
         
      $classes[] = "\">

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=" . $options['sgtm_field_code'] ."\"
height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<span></span style=\"display: none;";
      return $classes;
   }

   function sgtm_settings_init() {
      register_setting( 'sgtm', 'sgtm_options' );

      add_settings_section(
         'sgtm_section_settings', 
         __( 'Tag Manager', 'sgtm' ), 
         'sgtm_section_settings_cb', 
         'sgtm');

      add_settings_field(
         'sgtm_field_code',
         __( 'GTM Code', 'sgtm' ),
         'sgtm_field_code_cb',
         'sgtm',
         'sgtm_section_settings',
         array(
            'label_for' => 'sgtm_field_code',
            'class' => 'sgtm_row',
            'sgtm_custom_data' => 'custom',
         )
      );
      
   }

   function sgtm_section_settings_cb( $args ) {
      ?>
         <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can enter the GTM Code. (Format: GTM-XXXXXX)', 'sgtm' ); ?></p>
      <?php
   }

   function sgtm_field_code_cb( $args ) {

      $options = get_option( 'sgtm_options' );
      ?>
         <input id="<?php echo esc_attr( $args['label_for'] ); ?>"
                 data-custom="<?php echo esc_attr( $args['sgtm_custom_data'] ); ?>"
                 name="sgtm_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
                 type="text"
                 value="<?= isset($options[ $args['label_for']]) ? ($options[ $args['label_for']]) : ( 'GTM-XXXXXX') ?>">
        
      <?php
   }

   function sgtm_options_page() {
      add_menu_page(
         'Simple Google Tag Manager',
         'Tag Manager',
         'manage_options',
         'sgtm',
         'sgtm_options_page_html'
      );
   }

   function sgtm_options_page_html() {

      if ( ! current_user_can( 'manage_options' ) ) {
         return;
      }
    
      if ( isset( $_GET['settings-updated'] ) ) {
         add_settings_error( 'sgtm_messages', 'sgtm_message', __( 'Settings saved.', 'sgtm' ), 'updated' );
         $options = get_option( 'sgtm_options' );
      }
    
      settings_errors( 'sgtm_messages' );
      ?>
         <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
      <?php
      // output security fields for the registered setting "sgtm"
      settings_fields( 'sgtm' );
 
      do_settings_sections( 'sgtm' );

      submit_button( 'Save' );

      ?>
            </form>
         </div>
      <?php
   }





?>