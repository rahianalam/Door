<?php
/**
 * Dooroora Leads: admin inbox for contact form submissions,
 * plus a 301 redirect from /contact to /contact-us.
 *
 * Reads the {prefix}dooroora_contact_messages table that the theme's
 * contact form (inc/ajax.php, action dooroora_contact_form) writes to.
 */
defined('ABSPATH') || exit;

if (!function_exists('dooroora_leads_table')) {

function dooroora_leads_table() {
    global $wpdb;
    return $wpdb->prefix . 'dooroora_contact_messages';
}

/* ---------- /contact -> /contact-us ---------- */
add_action('template_redirect', function () {
    if (!is_404()) return;
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if (in_array(strtolower($path), array('contact', 'contact-me', 'contacts'), true)) {
        wp_safe_redirect(home_url('/contact-us'), 301, 'Dooroora');
        exit;
    }
}, 1);

/* ---------- Mail safety ----------
 * This host has PHP mail() disabled, which makes wp_mail() throw a fatal
 * error (breaking the contact form after the lead is saved). If mail() is
 * missing and no SMTP plugin is configuring PHPMailer, skip sending
 * gracefully instead of crashing. Leads are still stored in the database.
 */
add_filter('pre_wp_mail', function ($return, $atts) {
    if (null !== $return) return $return;
    if (!function_exists('mail') && !has_action('phpmailer_init')) {
        update_option('dooroora_mail_unavailable', current_time('mysql'), false);
        return false;
    }
    return $return;
}, 10, 2);

/* ---------- Helpers ---------- */
function dooroora_leads_unread_count() {
    global $wpdb;
    $t = dooroora_leads_table();
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$t} WHERE status = 'unread'");
}

function dooroora_leads_url($args = array()) {
    return add_query_arg($args, admin_url('admin.php?page=dooroora-leads'));
}

/* ---------- Admin menu ---------- */
add_action('admin_menu', function () {
    $unread = dooroora_leads_unread_count();
    $bubble = $unread ? ' <span class="awaiting-mod count-' . $unread . '"><span class="pending-count">' . number_format_i18n($unread) . '</span></span>' : '';
    add_menu_page(
        __('Leads', 'dooroora'),
        __('Leads', 'dooroora') . $bubble,
        'manage_options',
        'dooroora-leads',
        'dooroora_leads_render_page',
        'dashicons-email-alt',
        26
    );
});

/* ---------- Actions (before output) ---------- */
add_action('admin_init', function () {
    if (!isset($_GET['page']) || $_GET['page'] !== 'dooroora-leads' || !current_user_can('manage_options')) return;
    global $wpdb;
    $t = dooroora_leads_table();

    // CSV export
    if (isset($_GET['dl_export'])) {
        check_admin_referer('dooroora_leads_export');
        $rows = $wpdb->get_results("SELECT id, created_at, name, email, inquiry_type, subject, message, status FROM {$t} ORDER BY id DESC", ARRAY_A);
        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=dooroora-leads-' . gmdate('Y-m-d') . '.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, array('ID', 'Date', 'Name', 'Email', 'Topic', 'Subject', 'Message', 'Status'));
        foreach ($rows as $r) {
            // Neutralise spreadsheet formulas
            foreach ($r as $k => $v) {
                if (is_string($v) && $v !== '' && in_array($v[0], array('=', '+', '-', '@'), true)) $r[$k] = "'" . $v;
            }
            fputcsv($out, $r);
        }
        fclose($out);
        exit;
    }

    // Single-row actions
    if (isset($_GET['dl_action'], $_GET['id'])) {
        $id = absint($_GET['id']);
        $action = sanitize_key($_GET['dl_action']);
        check_admin_referer('dooroora_lead_' . $action . '_' . $id);
        if ($action === 'read' || $action === 'unread') {
            $wpdb->update($t, array('status' => $action), array('id' => $id), array('%s'), array('%d'));
        } elseif ($action === 'delete') {
            $wpdb->delete($t, array('id' => $id), array('%d'));
        }
        wp_safe_redirect(dooroora_leads_url(array('dl_done' => $action)));
        exit;
    }

    // Bulk actions
    if (isset($_POST['dl_bulk'], $_POST['lead_ids']) && is_array($_POST['lead_ids'])) {
        check_admin_referer('dooroora_leads_bulk');
        $ids = array_filter(array_map('absint', $_POST['lead_ids']));
        $bulk = sanitize_key($_POST['dl_bulk']);
        if ($ids) {
            $in = implode(',', array_fill(0, count($ids), '%d'));
            if ($bulk === 'read' || $bulk === 'unread') {
                $wpdb->query($wpdb->prepare("UPDATE {$t} SET status = %s WHERE id IN ($in)", array_merge(array($bulk), $ids)));
            } elseif ($bulk === 'delete') {
                $wpdb->query($wpdb->prepare("DELETE FROM {$t} WHERE id IN ($in)", $ids));
            }
        }
        wp_safe_redirect(dooroora_leads_url(array('dl_done' => $bulk)));
        exit;
    }
});

/* ---------- Page renderer ---------- */
function dooroora_leads_render_page() {
    if (!current_user_can('manage_options')) return;
    global $wpdb;
    $t = dooroora_leads_table();
    $topics = array('general' => 'General', 'feedback' => 'Feedback', 'correction' => 'Correction', 'partnership' => 'Partnership', 'advertising' => 'Advertising', 'technical' => 'Technical');

    echo '<div class="wrap dooroora-leads">';
    echo '<style>
        .dooroora-leads .dl-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin:16px 0}
        .dooroora-leads .dl-card{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:14px 16px}
        .dooroora-leads .dl-card b{display:block;font-size:24px;line-height:1.2;color:#0f2440}
        .dooroora-leads .dl-card span{color:#646970;font-size:12px;text-transform:uppercase;letter-spacing:.04em}
        .dooroora-leads tr.dl-unread td{font-weight:600;background:#fffbea}
        .dooroora-leads .dl-badge{display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;background:#f0f0f1}
        .dooroora-leads .dl-badge.unread{background:#fcd34d;color:#3b2f00}
        .dooroora-leads .dl-msg{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;max-width:860px}
        .dooroora-leads .dl-msg pre{white-space:pre-wrap;font:14px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#f6f7f7;padding:14px;border-radius:6px}
        .dooroora-leads .dl-meta{display:grid;grid-template-columns:120px 1fr;gap:6px 12px;margin-bottom:14px}
        .dooroora-leads .dl-meta dt{color:#646970}
    </style>';

    if (!function_exists('mail') && !has_action('phpmailer_init')) {
        echo '<div class="notice notice-warning"><p><strong>Email notifications are off:</strong> this server has PHP mail() disabled, so new leads are saved here but not emailed. Install an SMTP plugin (for example WP Mail SMTP) to receive email alerts.</p></div>';
    }
    if (!empty($_GET['dl_done'])) {
        $msgs = array('read' => 'Marked as read.', 'unread' => 'Marked as unread.', 'delete' => 'Deleted.');
        $k = sanitize_key($_GET['dl_done']);
        if (isset($msgs[$k])) echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($msgs[$k]) . '</p></div>';
    }

    /* Single lead view */
    if (isset($_GET['view'])) {
        $id = absint($_GET['view']);
        $lead = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$t} WHERE id = %d", $id));
        echo '<h1 class="wp-heading-inline">' . esc_html__('Lead', 'dooroora') . ' #' . (int) $id . '</h1> ';
        echo '<a class="page-title-action" href="' . esc_url(dooroora_leads_url()) . '">&larr; ' . esc_html__('All leads', 'dooroora') . '</a><hr class="wp-header-end">';
        if (!$lead) { echo '<p>' . esc_html__('Lead not found.', 'dooroora') . '</p></div>'; return; }
        if ($lead->status === 'unread') {
            $wpdb->update($t, array('status' => 'read'), array('id' => $id), array('%s'), array('%d'));
        }
        $reply = 'mailto:' . rawurlencode($lead->email) . '?subject=' . rawurlencode('Re: ' . $lead->subject);
        echo '<div class="dl-msg"><dl class="dl-meta">';
        echo '<dt>Name</dt><dd>' . esc_html($lead->name) . '</dd>';
        echo '<dt>Email</dt><dd><a href="mailto:' . esc_attr($lead->email) . '">' . esc_html($lead->email) . '</a></dd>';
        echo '<dt>Topic</dt><dd>' . esc_html($topics[$lead->inquiry_type] ?? ucfirst($lead->inquiry_type)) . '</dd>';
        echo '<dt>Subject</dt><dd>' . esc_html($lead->subject) . '</dd>';
        echo '<dt>Received</dt><dd>' . esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $lead->created_at)) . '</dd>';
        echo '<dt>IP address</dt><dd>' . esc_html($lead->ip_address) . '</dd>';
        echo '</dl><pre>' . esc_html($lead->message) . '</pre>';
        echo '<p><a class="button button-primary" href="' . esc_url($reply) . '">' . esc_html__('Reply by email', 'dooroora') . '</a> ';
        echo '<a class="button" href="' . esc_url(wp_nonce_url(dooroora_leads_url(array('dl_action' => 'unread', 'id' => $id)), 'dooroora_lead_unread_' . $id)) . '">' . esc_html__('Mark unread', 'dooroora') . '</a> ';
        echo '<a class="button button-link-delete" onclick="return confirm(\'Delete this lead?\')" href="' . esc_url(wp_nonce_url(dooroora_leads_url(array('dl_action' => 'delete', 'id' => $id)), 'dooroora_lead_delete_' . $id)) . '">' . esc_html__('Delete', 'dooroora') . '</a></p>';
        echo '</div></div>';
        return;
    }

    /* List view */
    $status = isset($_GET['status']) && in_array($_GET['status'], array('unread', 'read'), true) ? $_GET['status'] : '';
    $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $paged = max(1, absint($_GET['paged'] ?? 1));
    $per = 20;

    $where = 'WHERE 1=1';
    $params = array();
    if ($status) { $where .= ' AND status = %s'; $params[] = $status; }
    if ($search !== '') {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= ' AND (name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s)';
        array_push($params, $like, $like, $like, $like);
    }
    $count_sql = "SELECT COUNT(*) FROM {$t} {$where}";
    $total = (int) ($params ? $wpdb->get_var($wpdb->prepare($count_sql, $params)) : $wpdb->get_var($count_sql));
    $list_sql = "SELECT * FROM {$t} {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
    $rows = $wpdb->get_results($wpdb->prepare($list_sql, array_merge($params, array($per, ($paged - 1) * $per))));

    $all = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$t}");
    $unread = dooroora_leads_unread_count();
    $week = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$t} WHERE created_at >= %s", gmdate('Y-m-d H:i:s', current_time('timestamp') - 7 * DAY_IN_SECONDS)));

    echo '<h1 class="wp-heading-inline">' . esc_html__('Leads', 'dooroora') . '</h1> ';
    echo '<a class="page-title-action" href="' . esc_url(wp_nonce_url(dooroora_leads_url(array('dl_export' => 1)), 'dooroora_leads_export')) . '">' . esc_html__('Export CSV', 'dooroora') . '</a>';
    echo '<a class="page-title-action" target="_blank" href="' . esc_url(home_url('/contact-us')) . '">' . esc_html__('View contact page', 'dooroora') . '</a><hr class="wp-header-end">';

    echo '<div class="dl-cards">';
    echo '<div class="dl-card"><b>' . number_format_i18n($all) . '</b><span>Total leads</span></div>';
    echo '<div class="dl-card"><b>' . number_format_i18n($unread) . '</b><span>Unread</span></div>';
    echo '<div class="dl-card"><b>' . number_format_i18n($week) . '</b><span>Last 7 days</span></div>';
    echo '</div>';

    echo '<ul class="subsubsub">';
    $views = array('' => array('All', $all), 'unread' => array('Unread', $unread), 'read' => array('Read', $all - $unread));
    $i = 0;
    foreach ($views as $key => $v) {
        $cls = ($status === $key) ? ' class="current"' : '';
        echo '<li><a' . $cls . ' href="' . esc_url(dooroora_leads_url($key ? array('status' => $key) : array())) . '">' . esc_html($v[0]) . ' <span class="count">(' . (int) $v[1] . ')</span></a>' . (++$i < count($views) ? ' |' : '') . '</li>';
    }
    echo '</ul>';

    echo '<form method="get" style="float:right;margin:6px 0"><input type="hidden" name="page" value="dooroora-leads">';
    if ($status) echo '<input type="hidden" name="status" value="' . esc_attr($status) . '">';
    echo '<input type="search" name="s" value="' . esc_attr($search) . '" placeholder="Search leads"> <button class="button">Search</button></form>';

    echo '<form method="post" style="clear:both">';
    wp_nonce_field('dooroora_leads_bulk');
    echo '<div class="tablenav top"><div class="alignleft actions bulkactions"><select name="dl_bulk"><option value="">Bulk actions</option><option value="read">Mark read</option><option value="unread">Mark unread</option><option value="delete">Delete</option></select> <button class="button action" onclick="return this.form.dl_bulk.value!==\'delete\'||confirm(\'Delete selected leads?\')">Apply</button></div></div>';

    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>';
    echo '<td class="manage-column column-cb check-column"><input type="checkbox" onclick="document.querySelectorAll(\'.dl-cb\').forEach(c=>c.checked=this.checked)"></td>';
    echo '<th style="width:16%">Name</th><th style="width:20%">Email</th><th style="width:11%">Topic</th><th>Subject</th><th style="width:14%">Received</th><th style="width:8%">Status</th></tr></thead><tbody>';

    if (!$rows) {
        echo '<tr><td colspan="7" style="padding:24px;text-align:center;color:#646970">' . ($all ? 'No leads match this view.' : 'No leads yet. New contact form messages will appear here.') . '</td></tr>';
    }
    foreach ($rows as $r) {
        $view = dooroora_leads_url(array('view' => $r->id));
        $toggle = $r->status === 'unread' ? 'read' : 'unread';
        echo '<tr class="' . ($r->status === 'unread' ? 'dl-unread' : '') . '">';
        echo '<th scope="row" class="check-column"><input class="dl-cb" type="checkbox" name="lead_ids[]" value="' . (int) $r->id . '"></th>';
        echo '<td><a href="' . esc_url($view) . '"><strong>' . esc_html($r->name) . '</strong></a><div class="row-actions">';
        echo '<span><a href="' . esc_url($view) . '">View</a> | </span>';
        echo '<span><a href="' . esc_url(wp_nonce_url(dooroora_leads_url(array('dl_action' => $toggle, 'id' => $r->id)), 'dooroora_lead_' . $toggle . '_' . $r->id)) . '">Mark ' . $toggle . '</a> | </span>';
        echo '<span class="trash"><a onclick="return confirm(\'Delete this lead?\')" href="' . esc_url(wp_nonce_url(dooroora_leads_url(array('dl_action' => 'delete', 'id' => $r->id)), 'dooroora_lead_delete_' . $r->id)) . '">Delete</a></span></div></td>';
        echo '<td><a href="mailto:' . esc_attr($r->email) . '">' . esc_html($r->email) . '</a></td>';
        echo '<td>' . esc_html($topics[$r->inquiry_type] ?? ucfirst($r->inquiry_type)) . '</td>';
        echo '<td><a href="' . esc_url($view) . '">' . esc_html($r->subject) . '</a><br><span style="color:#646970">' . esc_html(wp_trim_words($r->message, 14)) . '</span></td>';
        echo '<td>' . esc_html(mysql2date('M j, Y g:i a', $r->created_at)) . '</td>';
        echo '<td><span class="dl-badge ' . esc_attr($r->status) . '">' . esc_html(ucfirst($r->status)) . '</span></td></tr>';
    }
    echo '</tbody></table></form>';

    $pages = (int) ceil($total / $per);
    if ($pages > 1) {
        echo '<div class="tablenav bottom"><div class="tablenav-pages">' . paginate_links(array(
            'base' => add_query_arg('paged', '%#%'), 'format' => '', 'current' => $paged, 'total' => $pages,
        )) . '</div></div>';
    }
    echo '</div>';
}

/* ---------- Dashboard widget ---------- */
add_action('wp_dashboard_setup', function () {
    if (!current_user_can('manage_options')) return;
    wp_add_dashboard_widget('dooroora_leads_widget', __('Latest Leads', 'dooroora'), function () {
        global $wpdb;
        $t = dooroora_leads_table();
        $rows = $wpdb->get_results("SELECT id, name, subject, status, created_at FROM {$t} ORDER BY id DESC LIMIT 5");
        if (!$rows) { echo '<p>No leads yet.</p>'; return; }
        echo '<ul>';
        foreach ($rows as $r) {
            echo '<li style="margin-bottom:8px">' . ($r->status === 'unread' ? '<strong>' : '') . '<a href="' . esc_url(dooroora_leads_url(array('view' => $r->id))) . '">' . esc_html($r->name) . ': ' . esc_html(wp_trim_words($r->subject, 8)) . '</a>' . ($r->status === 'unread' ? '</strong>' : '') . ' <span style="color:#646970">' . esc_html(human_time_diff(strtotime($r->created_at), current_time('timestamp'))) . ' ago</span></li>';
        }
        echo '</ul><p><a class="button" href="' . esc_url(dooroora_leads_url()) . '">View all leads</a></p>';
    });
});

}
