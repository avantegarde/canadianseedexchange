<?php

// Prevent direct script access
if ( !defined('ABSPATH') )
    die ( 'No direct script access allowed' );
?>
<?php
if ( ! class_exists( 'G1_Socials_Widget' ) ):

    class G1_Socials_Widget extends WP_Widget {
        /**
         * Register widget with WordPress.
         */
        function __construct() {
            parent::__construct(
                'g1_socials', // Base ID
                'G1 Socials', // Name
                array( 'description' => esc_html__( 'A Socials Widget', 'g1_socials' ), ) // Args
            );
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget( $args, $instance ) {
            extract( $args );

            // compose shortcode attribute list
            $elems = array(
                'include',
                'exclude',
                'template',
                'icon_size',
                'icon_color',
            );

            $attrs = '';

            foreach ( $elems as $elem ) {
                if ( !empty( $instance[$elem] ) ) {
                    $attrs .= sprintf(' %s="%s"', $elem, $instance[$elem]);
                }
            }

            // User-selected settings.
            $title = apply_filters( 'widget_title', $instance['title'] );

            // translate title
            if ( function_exists('icl_translate') ) {
                $title = icl_translate( 'G1 Socials', 'label', $title );
            }

            // Title of widget (before and after defined by themes)
            if ( $title ) {
                $title = $before_title . $title . $after_title;
            }

            // Compose output
            $out =
                $before_widget .
                $title .
                do_shortcode('[g1_socials '. $attrs .']') .
                $after_widget;

            // Render
            echo $out;
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form( $instance ) {
            $include = !empty( $instance['include'] ) ? $instance['include'] : '';
            $exclude = !empty( $instance['exclude'] ) ? $instance['exclude'] : '';
            $template = !empty( $instance['template'] ) ? $instance['template'] : '';
            $icon_size = !empty( $instance['icon_size'] ) ? $instance['icon_size'] : '';
            $icon_color = !empty( $instance['icon_color'] ) ? $instance['icon_color'] : '';

            // Backward compatibility.
            if ( 'light' === $icon_color || 'dark' === $icon_color ) {
                $icon_color = 'text';
            }

            $title = !empty( $instance['title'] ) ? $instance['title'] : '';

            if ( function_exists('icl_register_string') ) {
                icl_register_string( 'G1 Socials', 'label', $title );
            }
            ?>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Widget Title', 'g1_socials' ); ?>:</label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />

            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>"><?php esc_html_e( 'Include (optional)', 'g1_socials' ); ?>:</label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include' ) ); ?>" type="text" value="<?php echo esc_attr( $include ); ?>" />
                <small><?php esc_html_e( 'Include only specified icons (eg. facebook,twitter). Leave empty to include all.', 'g1_socials' ); ?></small>
            </p>

            <p>
               <label for="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>"><?php esc_html_e( 'Exclude (optional)', 'g1_socials' ); ?>:</label>
               <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>" />
               <small><?php esc_html_e( 'Exclude specified icons (eg. facebook,twitter).', 'g1_socials' ); ?></small>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>"><?php esc_html_e( 'Template', 'g1_socials' ); ?>:</label>
                <select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>">
                    <option value="grid" <?php selected( $template, 'grid' ); ?>><?php esc_html_e( 'grid', 'g1_socials' ); ?></option>
                </select>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'icon_size' ) ); ?>"><?php esc_html_e( 'Icon size', 'g1_socials' ); ?>:</label>
                <select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'icon_size' ) ); ?>">
                    <option value="32" <?php selected( $icon_size, '32' ); ?>><?php esc_html_e( '32px', 'g1_socials' ) ?></option>
                    <option value="48" <?php selected( $icon_size, '48' ); ?>><?php esc_html_e( '48px', 'g1_socials' ) ?></option>
                    <option value="64" <?php selected( $icon_size, '64' ); ?>><?php esc_html_e( '64px', 'g1_socials' ) ?></option>
                </select>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'icon_color' ) ); ?>"><?php esc_html_e( 'Icon color', 'g1_socials' ); ?>:</label>
                <select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'icon_color' ) ); ?>">
                    <option value="original" <?php selected( $icon_color, 'original' ); ?>><?php esc_html_e( 'original', 'g1_socials' ) ?></option>
                    <option value="text" <?php selected( $icon_color, 'text' ); ?>><?php esc_html_e( 'text', 'g1_socials' ) ?></option>
                </select>
            </p>
        <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update( $new_instance, $old_instance ) {
            $instance = array();

            $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['include'] = ( !empty( $new_instance['include'] ) ) ? strip_tags( $new_instance['include'] ) : '';
            $instance['exclude'] = ( !empty( $new_instance['exclude'] ) ) ? strip_tags( $new_instance['exclude'] ) : '';
            $instance['template'] = ( !empty( $new_instance['template'] ) ) ? strip_tags( $new_instance['template'] ) : '';
            $instance['icon_size'] = ( !empty( $new_instance['icon_size'] ) ) ? absint( $new_instance['icon_size'] ) : '';
            $instance['icon_color'] = ( !empty( $new_instance['icon_color'] ) ) ? strip_tags( $new_instance['icon_color'] ) : '';

            return $instance;
        }
    }

endif;
