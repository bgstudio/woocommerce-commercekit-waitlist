//Add this code to the functions.php file of your theme.

function add_alert_column($columns) {
    $columns['alert'] = 'Alert';
    return $columns;
}
add_filter('manage_edit-product_columns', 'add_alert_column', 10);


function add_admin_css() {
    echo '<style>
        .alert-red {
            color: red;
            font-weight: bold;
        }
    </style>';
}
add_action('admin_head', 'add_admin_css');


function fill_alert_column($column, $post_id) {
    if ($column == 'alert') {
        global $wpdb;

        // Retrieve the product
        $product = wc_get_product($post_id);
        $ids = $product->is_type('variable') ? $product->get_children() : array($post_id);

        // Build and execute the SQL query
        $where = "product_id IN (" . implode(',', array_map('intval', $ids)) . ")";
        $sql = "SELECT COUNT(*) FROM " . $wpdb->prefix . "commercekit_waitlist WHERE " . $where;
        $count = $wpdb->get_var($sql);

        // Apply the CSS class if the number of alerts is greater than 10
        $css_class = $count > 10 ? 'alert-red' : '';

        echo "<span class='$css_class'>". $count . " alert(s)</span>";
    }
}

add_action('manage_product_posts_custom_column', 'fill_alert_column', 10, 2);
