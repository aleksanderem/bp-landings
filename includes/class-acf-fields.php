<?php
/**
 * ACF Field Definitions for Landings
 */

defined('ABSPATH') || exit;

class BP_Landings_ACF_Fields {

    public static function register() {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key'      => 'group_landing_details',
            'title'    => 'Landing Details',
            'fields'   => [
                [
                    'key'          => 'field_landing_custom_path',
                    'label'        => 'Custom URL Path',
                    'name'         => 'landing_custom_path',
                    'type'         => 'text',
                    'placeholder'  => 'law-enforcement/contact-us',
                    'instructions' => 'Custom URL path without leading/trailing slashes. Leave empty to use the default slug.',
                    'prepend'      => '/',
                    'append'       => '/',
                ],
                [
                    'key'           => 'field_landing_source',
                    'label'         => 'Source',
                    'name'          => 'landing_source',
                    'type'          => 'button_group',
                    'choices'       => [
                        'wordpress' => 'WordPress',
                        'directory' => 'Directory',
                    ],
                    'default_value' => 'wordpress',
                    'layout'        => 'horizontal',
                    'return_format' => 'value',
                    'instructions'  => 'WordPress = built with Elementor. Directory = static files in a root folder.',
                ],
                [
                    'key'               => 'field_landing_url',
                    'label'             => 'Directory URL',
                    'name'              => 'landing_url',
                    'type'              => 'url',
                    'required'          => 0,
                    'placeholder'       => 'https://buspatrol.com/colorado/',
                    'instructions'      => 'Full URL of the static landing page.',
                    'conditional_logic' => [
                        [
                            [
                                'field'    => 'field_landing_source',
                                'operator' => '==',
                                'value'    => 'directory',
                            ],
                        ],
                    ],
                ],
                [
                    'key'               => 'field_landing_directory',
                    'label'             => 'Directory Name',
                    'name'              => 'landing_directory',
                    'type'              => 'text',
                    'placeholder'       => 'colorado',
                    'instructions'      => 'Root directory name on the server (e.g. colorado, florida).',
                    'conditional_logic' => [
                        [
                            [
                                'field'    => 'field_landing_source',
                                'operator' => '==',
                                'value'    => 'directory',
                            ],
                        ],
                    ],
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'bp_landing',
                    ],
                ],
            ],
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
        ]);
    }
}
