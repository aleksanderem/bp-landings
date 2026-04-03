<?php
/**
 * Landings CPT
 *
 * Represents standalone landing pages that live in root directories
 * of the site (e.g. /colorado/, /florida/) with their own navigation and styles.
 * URLs are root-level: /colorado/ instead of /landing/colorado/
 */

defined('ABSPATH') || exit;

class BP_Landings_CPT {

    public static function register() {
        register_post_type('bp_landing', [
            'labels' => [
                'name'               => __('Landings', 'bp-landings'),
                'singular_name'      => __('Landing', 'bp-landings'),
                'add_new'            => __('Add New', 'bp-landings'),
                'add_new_item'       => __('Add New Landing', 'bp-landings'),
                'edit_item'          => __('Edit Landing', 'bp-landings'),
                'new_item'           => __('New Landing', 'bp-landings'),
                'view_item'          => __('View Landing', 'bp-landings'),
                'search_items'       => __('Search Landings', 'bp-landings'),
                'not_found'          => __('No landings found', 'bp-landings'),
                'not_found_in_trash' => __('No landings found in Trash', 'bp-landings'),
                'all_items'          => __('All Landings', 'bp-landings'),
                'menu_name'          => __('Landings', 'bp-landings'),
            ],
            'public'          => true,
            'show_in_rest'    => true,
            'has_archive'     => false,
            'hierarchical'    => true,
            'capability_type' => 'page',
            'supports'        => ['title', 'editor', 'thumbnail', 'page-attributes', 'revisions', 'elementor'],
            'menu_icon'       => 'dashicons-admin-page',
            'rewrite'         => ['slug' => 'bp-landing-page', 'with_front' => false],
        ]);

        // Root-level URLs (no /landing/ prefix)
        add_filter('post_type_link', [__CLASS__, 'remove_slug_from_permalink'], 10, 2);
        add_filter('request', [__CLASS__, 'resolve_root_landing_request']);
        add_filter('redirect_canonical', [__CLASS__, 'prevent_canonical_redirect'], 10, 2);

        add_filter('manage_bp_landing_posts_columns', [__CLASS__, 'admin_columns']);
        add_action('manage_bp_landing_posts_custom_column', [__CLASS__, 'admin_column_content'], 10, 2);
        add_action('admin_head', [__CLASS__, 'admin_column_styles']);

        // Ensure Elementor supports this post type
        add_action('admin_init', [__CLASS__, 'ensure_elementor_support']);
        add_filter('option_elementor_cpt_support', [__CLASS__, 'add_elementor_support']);
        add_filter('default_option_elementor_cpt_support', [__CLASS__, 'add_elementor_support']);
    }

    public static function remove_slug_from_permalink($post_link, $post) {
        if ($post->post_type === 'bp_landing' && $post->post_status === 'publish') {
            // get_page_uri returns full hierarchical path: parent/child
            $uri = get_page_uri($post);
            return home_url('/' . $uri . '/');
        }
        return $post_link;
    }

    public static function resolve_root_landing_request($query_vars) {
        if (is_admin()) {
            return $query_vars;
        }

        $slug = '';
        if (!empty($query_vars['pagename'])) {
            $slug = $query_vars['pagename'];
        } elseif (!empty($query_vars['error'])) {
            $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
            $home_path = trim(parse_url(home_url(), PHP_URL_PATH) ?: '', '/');
            if ($home_path && strpos($path, $home_path) === 0) {
                $path = trim(substr($path, strlen($home_path)), '/');
            }
            $slug = $path;
        }

        if (empty($slug)) {
            return $query_vars;
        }

        // Check if a WP page matches this path (pages take priority)
        if (get_page_by_path($slug, OBJECT, 'page')) {
            return $query_vars;
        }

        // For single-segment slugs, check leadership posts (they also use root URLs)
        if (strpos($slug, '/') === false) {
            global $wpdb;
            $leadership_id = $wpdb->get_var($wpdb->prepare(
                "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = 'leadership' AND post_status = 'publish' LIMIT 1",
                $slug
            ));
            if ($leadership_id) {
                return $query_vars;
            }
        }

        // Check if a bp_landing matches this path (supports hierarchical paths like parent/child)
        $landing = get_page_by_path($slug, OBJECT, 'bp_landing');

        if ($landing) {
            $query_vars = [
                'post_type' => 'bp_landing',
                'page_id'   => $landing->ID,
            ];
        }

        return $query_vars;
    }

    public static function prevent_canonical_redirect($redirect_url, $requested_url) {
        if (is_singular('bp_landing')) {
            return false;
        }
        return $redirect_url;
    }

    public static function ensure_elementor_support() {
        $option = get_option('elementor_cpt_support');
        if (is_array($option) && !in_array('bp_landing', $option, true)) {
            $option[] = 'bp_landing';
            update_option('elementor_cpt_support', $option);
        }
    }

    public static function add_elementor_support($value) {
        if (!is_array($value)) {
            $value = ['post', 'page'];
        }
        if (!in_array('bp_landing', $value, true)) {
            $value[] = 'bp_landing';
        }
        return $value;
    }

    public static function admin_column_styles() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'edit-bp_landing') {
            echo '<style>.column-thumb{width:60px;} .column-landing_url{width:30%;}</style>';
        }
    }

    public static function admin_columns($columns) {
        $new = [];
        foreach ($columns as $key => $label) {
            if ($key === 'title') {
                $new['thumb'] = __('Image', 'bp-landings');
            }
            $new[$key] = $label;
            if ($key === 'title') {
                $new['landing_url'] = __('URL', 'bp-landings');
            }
        }
        return $new;
    }

    public static function admin_column_content($column, $post_id) {
        if ($column === 'thumb') {
            $img = get_the_post_thumbnail($post_id, [50, 50], ['style' => 'border-radius:4px;object-fit:cover;']);
            echo $img ?: '<span class="dashicons dashicons-admin-page" style="font-size:36px;color:#ccc;"></span>';
        }

        if ($column === 'landing_url') {
            $url = get_field('landing_url', $post_id);
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank" style="word-break:break-all;">' . esc_html($url) . '</a>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
        }
    }
}
