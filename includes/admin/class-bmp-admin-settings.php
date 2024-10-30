<?php

/**
 * WooCommerce Admin Settings Class
 *
 * @package  WooCommerce/Admin
 * @version  3.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BMP_Admin_Settings', false)) :


    class BMP_Admin_Settings
    {


        private static $settings = array();


        private static $errors = array();


        private static $messages = array();

        public static function get_settings_pages()
        {
            if (empty(self::$settings)) {
                $settings = array();

                include_once dirname(__FILE__) . '/settings/class-bmp-settings-page.php';

                $settings[] = include dirname(__FILE__) . '/settings/class-bmp-settings-general.php';
                $settings[] = include dirname(__FILE__) . '/settings/class-bmp-settings-payout.php';

                self::$settings = apply_filters('bmp_get_settings_pages', $settings);
            }

            return self::$settings;
        }

        /**
         * Save the settings.
         */
        public static function save()
        {
            global $current_tab;

            check_admin_referer('bmp-settings');
            // Trigger actions.
            do_action('bmp_settings_save_' . $current_tab);
            do_action('bmp_update_options_' . $current_tab);
            do_action('bmp_update_options');
            do_action('bmp_settings_saved');
        }

        /**
         * Add a message.
         *
         * @param string $text Message.
         */
        public static function add_message($text)
        {
            self::$messages[] = $text;
        }

        /**
         * Add an error.
         *
         * @param string $text Message.
         */
        public static function add_error($text)
        {
            self::$errors[] = $text;
        }

        /**
         * Output messages + errors.
         */
        public static function show_messages()
        {
            global $wp_session;

            if (!empty($wp_session['bmp_save_error'])) {
                echo '<div id="message" class="error inline"><p><strong>' . esc_html($wp_session['bmp_save_error']) . '</strong></p></div>';
                unset($wp_session['bmp_save_error']);
            }
            if (!empty($wp_session['bmp_save_message'])) {
                echo '<div id="message" class="updated inline"><p><strong>' . esc_html($wp_session['bmp_save_message']) . '</strong></p></div>';
                unset($wp_session['bmp_save_message']);
            }
        }

        /**
         * Settings page.
         *
         * Handles the display of the main woocommerce settings page in admin.
         */

        public static function output()
        {
            global $current_section, $current_tab;

            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

            do_action('bmp_settings_start');

            // Get tabs for the settings page.
            $tabs = apply_filters('bmp_settings_tabs_array', array());

            include dirname(__FILE__) . '/views/html-admin-settings.php';
        }

        /**
         * Get a setting from the settings API.
         *
         * @param string $option_name Option name.
         * @param mixed  $default     Default value.
         * @return mixed
         */
        public static function get_option($option_name, $default = '')
        {
            // Array value.
            if (strstr($option_name, '[')) {

                parse_str($option_name, $option_array);

                // Option name is first key.
                $option_name = current(array_keys($option_array));

                // Get value.
                $option_values = get_option($option_name, '');

                $key = key($option_array[$option_name]);

                if (isset($option_values[$key])) {
                    $option_value = $option_values[$key];
                } else {
                    $option_value = null;
                }
            } else {
                // Single value.
                $option_value = get_option($option_name, null);
            }

            if (is_array($option_value)) {
                $option_value = array_map('stripslashes', $option_value);
            } elseif (!is_null($option_value)) {
                $option_value = stripslashes($option_value);
            }

            return (null === $option_value) ? $default : $option_value;
        }

        /**
         * Output admin fields.
         *
         * Loops though the woocommerce options array and outputs each field.
         *
         * @param array[] $options Opens array to output.
         */
        public static function output_fields($options)
        {

            echo '<div class="container-fluid umw-setting">';
            echo '<div class="form-row">';
            if (!empty($options)) {
                foreach ($options as $value) {
                    if (!isset($value['type'])) {
                        continue;
                    }
                    if (!isset($value['id'])) {
                        $value['id'] = '';
                    }
                    if (!isset($value['title'])) {
                        $value['title'] = isset($value['name']) ? $value['name'] : '';
                    }
                    if (!isset($value['class'])) {
                        $value['class'] = '';
                    }
                    if (!isset($value['css'])) {
                        $value['css'] = '';
                    }
                    if (!isset($value['default'])) {
                        $value['default'] = '';
                    }
                    if (!isset($value['desc'])) {
                        $value['desc'] = '';
                    }
                    if (!isset($value['desc_tip'])) {
                        $value['desc_tip'] = false;
                    }
                    if (!isset($value['placeholder'])) {
                        $value['placeholder'] = '';
                    }
                    if (!isset($value['suffix'])) {
                        $value['suffix'] = '';
                    }
                    if (!isset($value['readonly'])) {
                        $value['readonly'] = '';
                    }

                    if (!isset($value['row_class'])) {
                        $value['row_class'] = 'col-md-6';
                    }

                    if (!isset($value['col_left'])) {
                        $value['col_left'] = 'float:left;';
                    }

                    if (!isset($value['lebel_hide'])) {
                        $value['lebel_hide'] = '';
                    }

                    if (!isset($value['required'])) {
                        $value['required'] = '';
                    }

                    if (!isset($value['onclick'])) {
                        $value['onclick'] = '';
                    }


                    // Custom attribute handling.
                    $custom_attributes = array();

                    if (!empty($value['custom_attributes']) && is_array($value['custom_attributes'])) {
                        foreach ($value['custom_attributes'] as $attribute => $attribute_value) {
                            $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
                        }
                    }

                    // Description handling.
                    $field_description = self::get_field_description($value);
                    $description       = $field_description['description'];
                    $tooltip_html      = $field_description['tooltip_html'];

                    // Switch based on type.
                    switch ($value['type']) {

                        case 'group_title_start':

                            if (!empty($value['title'])) {
?>
                                <div id="<?php echo esc_attr(sanitize_title($value['id'])); ?>-description" class="<?php echo $value['row_class']; ?>">

                                    <label for="<?php echo esc_attr($value['id']); ?>" data-toggle="tooltip" title="<?php echo $tooltip_html; ?>"><?php echo esc_html($value['title']); ?></label>
                                    <div class="form-group">

                                    <?php }

                                break;

                            case 'group_title_end':

                                echo '</div></div>';

                                break;

                            case 'div':
                                    ?>
                                    <div id="<?php echo esc_attr(sanitize_title($value['id'])); ?>" class="<?php echo $value['row_class']; ?>" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group"> &nbsp; </div>
                                    </div>
                                <?php
                                break;

                            case 'button':
                                ?>
                                    <div class="<?php echo $value['row_class']; ?>" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group">
                                            <button class="btn <?php echo $value['btn_class']; ?>" type="button" onclick="<?php echo $value['onclick']; ?>"><?php echo esc_html($value['title']); ?></button>
                                        </div>
                                    </div>
                                <?php
                                break;

                                // Section Titles.
                            case 'title':
                                if (!empty($value['title'])) {
                                    echo '<div class="col-md-12"><h2>' . esc_html($value['title']) . '</h2></div><br>';
                                }
                                if (!empty($value['desc'])) {
                                    echo '<div id="' . esc_attr(sanitize_title($value['id'])) . '-description" class="col-md-12">';
                                    echo wp_kses_post(wpautop(wptexturize($value['desc'])));
                                    echo '</div>';
                                }
                                //echo '<table class="form-table">' . "\n\n";
                                if (!empty($value['id'])) {
                                    do_action('bmp_settings_' . sanitize_title($value['id']));
                                }
                                break;

                                // Section Ends.
                            case 'sectionend':
                                if (!empty($value['id'])) {
                                    do_action('bmp_settings_' . sanitize_title($value['id']) . '_end');
                                }
                                //echo '</table>';
                                if (!empty($value['id'])) {
                                    do_action('bmp_settings_' . sanitize_title($value['id']) . '_after');
                                }
                                break;

                                // Standard text inputs and subtypes like 'number'.
                            case 'text':
                            case 'password':
                            case 'datetime':
                            case 'datetime-local':
                            case 'date':
                            case 'month':
                            case 'time':
                            case 'week':
                            case 'number':
                            case 'email':
                            case 'url':
                            case 'tel':
                                $option_value = self::get_option($value['id'], $value['default']);
                                ?>
                                    <div class="<?php echo $value['row_class']; ?>  bmp-section" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group">
                                            <?php if ($value['lebel_hide'] == 'yes') { ?>

                                            <?php } else { ?>
                                                <label for="<?php echo esc_attr($value['id']); ?>" data-toggle="tooltip" title="<?php echo $tooltip_html; ?>!"><?php echo esc_html($value['title']); ?></label>
                                            <?php } ?>

                                            <input name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" type="<?php echo esc_attr($value['type']); ?>" style="<?php echo esc_attr($value['css']); ?>" value="<?php echo esc_attr($option_value); ?>" class="form-control <?php echo esc_attr($value['class']); ?>" placeholder="<?php echo esc_attr($value['placeholder']); ?>" <?php echo $value['readonly']; ?> <?php echo $value['required']; ?>>

                                            <small id="<?php echo esc_attr($value['id']); ?>Help" class="form-text text-muted"><?php echo $tooltip_html; ?></small>

                                        </div>
                                    </div>
                                <?php
                                break;

                                // Textarea.
                            case 'textarea':
                                $option_value = self::get_option($value['id'], $value['default']);

                                ?>
                                    <div class="<?php echo $value['row_class']; ?> bmp-section" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group ">
                                            <?php if ($value['lebel_hide'] == 'yes') { ?>

                                            <?php } else { ?>
                                                <label for="<?php echo esc_attr($value['id']); ?>" data-toggle="tooltip" title="<?php echo $tooltip_html; ?>!"><?php echo esc_html($value['title']); ?></label>
                                            <?php } ?>

                                            <textarea name="<?php echo esc_attr($value['id']); ?>" id="<?php echo esc_attr($value['id']); ?>" style="<?php echo esc_attr($value['css']); ?>" class="form-control <?php echo esc_attr($value['class']); ?>" placeholder="<?php echo esc_attr($value['placeholder']); ?>" <?php echo implode(' ', $custom_attributes); ?> <?php echo $value['readonly']; ?> <?php echo $value['required']; ?>><?php echo esc_textarea($option_value); ?>
										
							</textarea>
                                            <small id="<?php echo esc_attr($value['id']); ?>Help" class="form-text text-muted"><?php echo $tooltip_html; ?></small>
                                        </div>
                                    </div>
                                <?php
                                break;

                                // Select boxes.
                            case 'select':
                            case 'multiselect':
                                $option_value = self::get_option($value['id'], $value['default']);
                                ?>
                                    <div class="<?php echo $value['row_class']; ?>  bmp-section" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group">
                                            <?php if ($value['lebel_hide'] == 'yes') { ?>

                                            <?php } else { ?>
                                                <label for="<?php echo esc_attr($value['id']); ?>" data-toggle="tooltip" title="<?php echo $tooltip_html; ?>!"><?php echo esc_html($value['title']); ?></label>
                                            <?php } ?>

                                            <select name="<?php echo esc_attr($value['id']); ?><?php echo ('multiselect' === $value['type']) ? '[]' : ''; ?>" id="<?php echo esc_attr($value['id']); ?>" style="<?php echo esc_attr($value['css']); ?>" class="form-control <?php echo esc_attr($value['class']); ?>" placeholder="<?php echo esc_attr($value['placeholder']); ?>" <?php echo implode(' ', $custom_attributes); // WPCS: XSS ok. 
                                                                                                                                                                                                                                                                                                                                                                                    ?> <?php echo 'multiselect' === $value['type'] ? 'multiple="multiple"' : ''; ?> <?php echo $value['readonly']; ?> <?php echo $value['required']; ?>>
                                                <?php
                                                foreach ($value['options'] as $key => $val) {
                                                ?>
                                                    <option value="<?php echo esc_attr($key); ?>" <?php

                                                                                                    if (is_array($option_value)) {
                                                                                                        selected(in_array((string) $key, $option_value, true), true);
                                                                                                    } else {
                                                                                                        selected($option_value, (string) $key);
                                                                                                    }

                                                                                                    ?>>
                                                        <?php echo esc_html($val); ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>

                                            <small id="<?php echo esc_attr($value['id']); ?>Help" class="form-text text-muted"><?php echo $tooltip_html; ?></small>

                                        </div>
                                    </div>

                                <?php
                                break;

                                // Radio inputs.
                            case 'radio':
                                $option_value = self::get_option($value['id'], $value['default']);
                                ?>

                                    <div class="<?php echo $value['row_class']; ?>  bmp-section" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group">
                                            <?php if ($value['lebel_hide'] == 'yes') { ?>

                                            <?php } else { ?>
                                                <label for="<?php echo esc_attr($value['id']); ?>" data-toggle="tooltip" title="<?php echo $tooltip_html; ?>!"><?php echo esc_html($value['title']); ?></label>
                                            <?php } ?>
                                            <div class="fieldset">
                                                <ul>
                                                    <?php
                                                    foreach ($value['options'] as $key => $val) {
                                                    ?>
                                                        <li>
                                                            <label>
                                                                <input type="radio" id="<?php echo esc_attr($value['id']); ?>-<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($value['id']); ?>" value="<?php echo esc_attr($key); ?>" class=" <?php echo esc_attr($value['class']); ?>" style="<?php echo esc_attr($value['css']); ?>" <?php echo $value['readonly']; ?> <?php checked($key, $option_value); ?> <?php echo $value['required']; ?> placeholder="<?php echo esc_attr($value['placeholder']); ?>">
                                                                <?php echo esc_html($val); ?>
                                                            </label>
                                                        </li>
                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <small id="<?php echo esc_attr($value['id']); ?>Help" class="form-text text-muted"><?php echo $tooltip_html; ?>

                                            </small>
                                        </div>
                                    </div>

                                <?php
                                break;

                                // Checkbox input.
                            case 'checkbox':
                                $option_value     = self::get_option($value['id'], $value['default']);
                                $visibility_class = array();

                                if (!isset($value['hide_if_checked'])) {
                                    $value['hide_if_checked'] = false;
                                }
                                if (!isset($value['show_if_checked'])) {
                                    $value['show_if_checked'] = false;
                                }
                                if ('yes' === $value['hide_if_checked'] || 'yes' === $value['show_if_checked']) {
                                    $visibility_class[] = 'hidden_option';
                                }
                                if ('option' === $value['hide_if_checked']) {
                                    $visibility_class[] = 'hide_options_if_checked';
                                }
                                if ('option' === $value['show_if_checked']) {
                                    $visibility_class[] = 'show_options_if_checked';
                                }

                                ?>
                                    <div class="<?php echo $value['row_class']; ?>  bmp-section" style="<?php echo $value['col_left']; ?>">
                                        <div class="form-group">
                                            <?php if ($value['lebel_hide'] == 'yes') { ?>

                                            <?php } else { ?>
                                                <label for="<?php echo esc_attr($value['id']); ?>" data-toggle="tooltip" title="<?php echo $tooltip_html; ?>!"><?php echo esc_html($value['title']); ?></label>
                                            <?php } ?>
                                            <div class="fieldset">
                                                <ul>
                                                    <?php
                                                    foreach ($value['options'] as $key => $val) {
                                                    ?>
                                                        <li>
                                                            <label>
                                                                <input type="checkbox" id="<?php echo esc_attr($value['id']); ?>-<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($value['id']); ?>" value="<?php echo esc_attr($key); ?>" class="<?php echo esc_attr($value['class']); ?>" style="<?php echo esc_attr($value['css']); ?>" <?php echo $value['readonly']; ?> <?php checked($option_value, 'yes'); ?> <?php echo $value['required']; ?>>
                                                                <?php echo esc_html($val); ?></label>
                                                        </li>

                                                    <?php
                                                    }
                                                    ?>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
        <?php

                                break;

                            case 'script':

                                echo '<script>';
                                echo $value['desc'];
                                echo '</script>';

                                // Default: run an action.
                            default:
                                do_action('bmp_admin_field_' . $value['type'], $value);
                                break;
                        }
                    }
                }
                echo '</div></div>';
                echo '<script>$(document).ready(function(){$(\'[data-toggle="tooltip"]\').tooltip();});</script>';
            }



            public static function get_field_description($value)
            {
                $description  = '';
                $tooltip_html = '';

                if (true === $value['desc_tip']) {
                    $tooltip_html = $value['desc'];
                } elseif (!empty($value['desc_tip'])) {
                    $description  = $value['desc'];
                    $tooltip_html = $value['desc_tip'];
                } elseif (!empty($value['desc'])) {
                    $description = $value['desc'];
                }

                if ($description && in_array($value['type'], array('textarea', 'radio'), true)) {
                    $description =  wp_kses_post($description);
                } elseif ($description && in_array($value['type'], array('checkbox'), true)) {
                    $description = wp_kses_post($description);
                } elseif ($description) {
                    $description = wp_kses_post($description);
                }

                if ($tooltip_html && in_array($value['type'], array('checkbox'), true)) {
                    $tooltip_html = $tooltip_html;
                } elseif ($tooltip_html) {
                    $tooltip_html = $tooltip_html;
                }

                return array(
                    'description'  => $description,
                    'tooltip_html' => $tooltip_html,
                );
            }


            public static function save_fields($options, $data = null)
            {
                if (is_null($data)) {
                    $data = sanitize_text_field($_POST); // WPCS: input var okay, CSRF ok.
                }
                if (empty($data)) {
                    return false;
                }

                // Options to update will be stored here and saved later.
                $update_options   = array();
                $autoload_options = array();

                // Loop options and get values to save.
                foreach ($options as $option) {
                    if (!isset($option['id']) || !isset($option['type'])) {
                        continue;
                    }

                    // Get posted value.
                    if (strstr($option['id'], '[')) {
                        parse_str($option['id'], $option_name_array);
                        $option_name  = current(array_keys($option_name_array));
                        $setting_name = key($option_name_array[$option_name]);
                        $raw_value    = isset($data[$option_name][$setting_name]) ? wp_unslash($data[$option_name][$setting_name]) : null;
                    } else {
                        $option_name  = $option['id'];
                        $setting_name = '';
                        $raw_value    = isset($data[$option['id']]) ? wp_unslash($data[$option['id']]) : null;
                    }

                    // Format the value based on option type.
                    switch ($option['type']) {
                        case 'checkbox':
                            $value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
                            break;
                        case 'textarea':
                            $value = wp_kses_post(trim($raw_value));
                            break;
                        case 'multiselect':
                        case 'multi_select_countries':
                            $value = array_filter(array_map('wc_clean', (array) $raw_value));
                            break;
                        case 'image_width':
                            $value = array();
                            if (isset($raw_value['width'])) {
                                $value['width']  = wc_clean($raw_value['width']);
                                $value['height'] = wc_clean($raw_value['height']);
                                $value['crop']   = isset($raw_value['crop']) ? 1 : 0;
                            } else {
                                $value['width']  = $option['default']['width'];
                                $value['height'] = $option['default']['height'];
                                $value['crop']   = $option['default']['crop'];
                            }
                            break;
                        case 'select':
                            $allowed_values = empty($option['options']) ? array() : array_map('strval', array_keys($option['options']));
                            if (empty($option['default']) && empty($allowed_values)) {
                                $value = null;
                                break;
                            }
                            $default = (empty($option['default']) ? $allowed_values[0] : $option['default']);
                            $value   = in_array($raw_value, $allowed_values, true) ? $raw_value : $default;
                            break;
                        case 'relative_date_selector':
                            $value = wc_parse_relative_date_option($raw_value);
                            break;
                        default:
                            $value = wc_clean($raw_value);
                            break;
                    }



                    /**
                     * Sanitize the value of an option.
                     *
                     * @since 2.4.0
                     */
                    $value = apply_filters('bmp_admin_settings_sanitize_option', $value, $option, $raw_value);

                    /**
                     * Sanitize the value of an option by option name.
                     *
                     * @since 2.4.0
                     */
                    $value = apply_filters("bmp_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value);

                    if (is_null($value)) {
                        continue;
                    }

                    // Check if option is an array and handle that differently to single values.
                    if ($option_name && $setting_name) {
                        if (!isset($update_options[$option_name])) {
                            $update_options[$option_name] = get_option($option_name, array());
                        }
                        if (!is_array($update_options[$option_name])) {
                            $update_options[$option_name] = array();
                        }
                        $update_options[$option_name][$setting_name] = $value;
                    } else {
                        $update_options[$option_name] = $value;
                    }

                    $autoload_options[$option_name] = isset($option['autoload']) ? (bool) $option['autoload'] : true;

                    /**
                     * Fire an action before saved.
                     *
                     * @deprecated 2.4.0 - doesn't allow manipulation of values!
                     */
                    do_action('bmp_update_option', $option);
                }

                // Save all options in our array.
                foreach ($update_options as $name => $value) {
                    update_option($name, $value, $autoload_options[$name] ? 'yes' : 'no');
                }
                return true;
            }
        }
    endif;
