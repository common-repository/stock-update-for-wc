<?php
/**
 * Customer note email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-note.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
var_dump("pawan");exit();
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
    <p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( 'Admins' ) ); ?></p>
    <p><?php esc_html_e( 'The stock on '.get_bloginfo('name'). 'has been updated!', PAONE_SU_LANG ); ?></p>

    <p><?php esc_html_e( 'Please find the attached sheet of the latest updated stock values.', 'woocommerce' ); ?></p>

<?php
do_action( 'woocommerce_email_footer', $email );
