<?php
defined('ABSPATH') || exit;

add_action('admin_menu', 'dooroora_newsletter_admin_menu');
function dooroora_newsletter_admin_menu() {
    add_submenu_page(
        'dooroora-settings',
        __('Newsletter Subscribers', 'dooroora'),
        __('Newsletter', 'dooroora'),
        'manage_options',
        'dooroora-newsletter',
        'dooroora_newsletter_admin_page'
    );
}

add_action('admin_post_dooroora_newsletter_bulk', 'dooroora_newsletter_handle_bulk');
function dooroora_newsletter_handle_bulk() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');
    check_admin_referer('dooroora_newsletter_bulk');

    $action = isset($_REQUEST['bulk_action']) ? sanitize_text_field($_REQUEST['bulk_action']) : '';
    $ids    = isset($_REQUEST['subscriber_ids']) ? array_map('intval', (array) $_REQUEST['subscriber_ids']) : array();

    if (empty($ids)) {
        wp_redirect(add_query_arg(array('page' => 'dooroora-newsletter', 'msg' => 'none_selected'), admin_url('admin.php')));
        exit;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'dooroora_newsletter';
    $ids_placeholder = implode(',', array_fill(0, count($ids), '%d'));

    if ($action === 'delete') {
        $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id IN ($ids_placeholder)", $ids));
        wp_redirect(add_query_arg(array('page' => 'dooroora-newsletter', 'msg' => 'deleted'), admin_url('admin.php')));
    } elseif ($action === 'export') {
        $rows = $wpdb->get_results($wpdb->prepare("SELECT email, created_at FROM $table WHERE id IN ($ids_placeholder) ORDER BY created_at DESC", $ids), ARRAY_A);
        dooroora_newsletter_export_csv($rows);
    } else {
        wp_redirect(add_query_arg(array('page' => 'dooroora-newsletter', 'msg' => 'invalid'), admin_url('admin.php')));
    }
    exit;
}

add_action('admin_post_dooroora_newsletter_export_all', 'dooroora_newsletter_export_all');
function dooroora_newsletter_export_all() {
    if (!current_user_can('manage_options')) wp_die('Unauthorized');
    check_admin_referer('dooroora_newsletter_export_all');

    global $wpdb;
    $table = $wpdb->prefix . 'dooroora_newsletter';
    $rows  = $wpdb->get_results("SELECT email, created_at FROM $table WHERE status = 'active' ORDER BY created_at DESC", ARRAY_A);
    dooroora_newsletter_export_csv($rows);
    exit;
}

function dooroora_newsletter_export_csv($rows) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=newsletter-subscribers-' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF");
    fputcsv($output, array('Email', 'Subscribed Date'));
    foreach ($rows as $row) {
        fputcsv($output, array($row['email'], $row['created_at']));
    }
    fclose($output);
    exit;
}

function dooroora_newsletter_admin_page() {
    global $wpdb;
    $table  = $wpdb->prefix . 'dooroora_newsletter';
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $msg    = isset($_GET['msg']) ? sanitize_text_field($_GET['msg']) : '';

    $page     = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $offset   = ($page - 1) * $per_page;

    $where = "WHERE 1=1";
    $query_params = array();
    if (!empty($search)) {
        $where .= " AND email LIKE %s";
        $query_params[] = '%' . $wpdb->esc_like($search) . '%';
    }

    $total_query = "SELECT COUNT(*) FROM $table $where";
    $total = $wpdb->get_var($wpdb->prepare($total_query, $query_params));

    $data_query = "SELECT * FROM $table $where ORDER BY created_at DESC LIMIT %d OFFSET %d";
    $prepared_params = array_merge($query_params, array($per_page, $offset));
    $subscribers = $wpdb->get_results($wpdb->prepare($data_query, $prepared_params));

    $total_pages = ceil($total / $per_page);
    ?>
    <div class="wrap dooroora-newsletter-wrap">
        <h1><?php esc_html_e('Newsletter Subscribers', 'dooroora'); ?></h1>

        <?php if ($msg === 'deleted') : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Selected subscribers deleted.', 'dooroora'); ?></p></div>
        <?php elseif ($msg === 'none_selected') : ?>
            <div class="notice notice-warning is-dismissible"><p><?php esc_html_e('No subscribers selected.', 'dooroora'); ?></p></div>
        <?php elseif ($msg === 'invalid') : ?>
            <div class="notice notice-error is-dismissible"><p><?php esc_html_e('Invalid action.', 'dooroora'); ?></p></div>
        <?php endif; ?>

        <div class="dooroora-newsletter-actions" style="margin:16px 0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" style="display:inline-flex;gap:8px;align-items:center;">
                <input type="hidden" name="page" value="dooroora-newsletter">
                <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search email...', 'dooroora'); ?>" style="min-width:200px;">
                <button type="submit" class="button"><?php esc_html_e('Search', 'dooroora'); ?></button>
                <?php if (!empty($search)) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=dooroora-newsletter')); ?>" class="button"><?php esc_html_e('Clear', 'dooroora'); ?></a>
                <?php endif; ?>
            </form>
            <span style="margin-left:auto;font-weight:600;"><?php echo intval($total); ?> <?php esc_html_e('subscribers', 'dooroora'); ?></span>
        </div>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="dooroora_newsletter_bulk">
            <?php wp_nonce_field('dooroora_newsletter_bulk'); ?>

            <div class="tablenav top" style="margin-bottom:12px;">
                <div class="alignleft actions">
                    <select name="bulk_action">
                        <option value=""><?php esc_html_e('Bulk Actions', 'dooroora'); ?></option>
                        <option value="delete"><?php esc_html_e('Delete', 'dooroora'); ?></option>
                        <option value="export"><?php esc_html_e('Export CSV', 'dooroora'); ?></option>
                    </select>
                    <button type="submit" class="button"><?php esc_html_e('Apply', 'dooroora'); ?></button>
                </div>
                <div class="alignright">
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=dooroora_newsletter_export_all'), 'dooroora_newsletter_export_all')); ?>" class="button button-primary"><?php esc_html_e('Export All to CSV', 'dooroora'); ?></a>
                </div>
                <br class="clear">
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td scope="col" class="manage-column column-cb check-column" style="width:40px;">
                            <input type="checkbox" id="dooroora-select-all">
                        </td>
                        <th scope="col"><?php esc_html_e('Email', 'dooroora'); ?></th>
                        <th scope="col" style="width:180px;"><?php esc_html_e('Date Subscribed', 'dooroora'); ?></th>
                        <th scope="col" style="width:100px;"><?php esc_html_e('Status', 'dooroora'); ?></th>
                        <th scope="col" style="width:120px;"><?php esc_html_e('Actions', 'dooroora'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subscribers)) : ?>
                        <tr><td colspan="5"><?php esc_html_e('No subscribers found.', 'dooroora'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($subscribers as $sub) : ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="subscriber_ids[]" value="<?php echo esc_attr($sub->id); ?>">
                                </th>
                                <td><strong><?php echo esc_html($sub->email); ?></strong></td>
                                <td><?php echo esc_html($sub->created_at); ?></td>
                                <td>
                                    <span style="display:inline-block;padding:2px 10px;border-radius:10px;font-size:0.8125rem;font-weight:500;background:<?php echo $sub->status === 'active' ? '#d1fae5' : '#fee2e2'; ?>;color:<?php echo $sub->status === 'active' ? '#065f46' : '#991b1b'; ?>;">
                                        <?php echo esc_html($sub->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=dooroora_newsletter_bulk&bulk_action=delete&subscriber_ids[]=' . $sub->id), 'dooroora_newsletter_bulk')); ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e('Delete this subscriber?', 'dooroora'); ?>');"><?php esc_html_e('Delete', 'dooroora'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="dooroora-select-all-2"></td>
                        <th scope="col"><?php esc_html_e('Email', 'dooroora'); ?></th>
                        <th scope="col"><?php esc_html_e('Date Subscribed', 'dooroora'); ?></th>
                        <th scope="col"><?php esc_html_e('Status', 'dooroora'); ?></th>
                        <th scope="col"><?php esc_html_e('Actions', 'dooroora'); ?></th>
                    </tr>
                </tfoot>
            </table>

            <?php if ($total_pages > 1) : ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links(array(
                            'base'      => add_query_arg('paged', '%#%'),
                            'format'    => '',
                            'prev_text' => '&laquo;',
                            'next_text' => '&raquo;',
                            'total'     => $total_pages,
                            'current'   => $page,
                        ));
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
    (function() {
        var selectAll1 = document.getElementById('dooroora-select-all');
        var selectAll2 = document.getElementById('dooroora-select-all-2');
        var checkboxes = document.querySelectorAll('input[name="subscriber_ids[]"]');

        function syncSelectAll(source, others) {
            if (!source) return;
            source.addEventListener('change', function() {
                checkboxes.forEach(function(cb) { cb.checked = source.checked; });
                if (others) others.forEach(function(o) { if (o) o.checked = source.checked; });
            });
        }
        syncSelectAll(selectAll1, [selectAll2]);
        syncSelectAll(selectAll2, [selectAll1]);
    })();
    </script>
    <?php
}
