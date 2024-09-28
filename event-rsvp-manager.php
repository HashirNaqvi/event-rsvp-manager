<?php
/*
Plugin Name: Event RSVP Manager
Description: A simple plugin to manage event RSVPs, requiring user registration and admin approval.
Version: 1.0
Author: Hashir Naqvi
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Initialize the plugin
function erm_init() {
    // Add shortcodes, custom post types, etc.
}
add_action('init', 'erm_init');

// Enqueue scripts and styles
function erm_enqueue_scripts() {
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
}
add_action('wp_enqueue_scripts', 'erm_enqueue_scripts');

// Admin Bootstrap styles
function erm_enqueue_admin_styles() {
    wp_enqueue_style('admin-bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
}
add_action('admin_enqueue_scripts', 'erm_enqueue_admin_styles');

// Ensure only logged-in users can register for events
function erm_rsvp_form() {
    if (!is_user_logged_in()) {
        echo '<p>Please <a href="' . wp_login_url(get_permalink()) . '">login</a> or <a href="' . wp_registration_url() . '">register</a> to RSVP for events.</p>';
        return;
    }

    // If the user is logged in, show the event registration form
    erm_event_registration_form();
}
add_shortcode('erm_rsvp_form', 'erm_rsvp_form');  // Register the shortcode

// Event registration form with event selection dropdown
function erm_event_registration_form() {
    global $wpdb;

    // Fetch available events
    $events = get_posts(array(
        'post_type' => 'erm_event',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    if (empty($events)) {
        echo '<p>No events available for registration.</p>';
        return;
    }

    ?>
    <form method="post" action="" class="form-group">
        <div class="form-group">
            <label for="erm_event">Select Event:</label>
            <select name="erm_event" class="form-control" required>
                <?php foreach ($events as $event) : ?>
                    <option value="<?php echo esc_attr($event->ID); ?>">
                        <?php echo esc_html($event->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="erm_name">Your Name</label>
            <input type="text" name="erm_name" class="form-control" placeholder="Your Name" required />
        </div>

        <div class="form-group">
            <label for="erm_email">Your Email</label>
            <input type="email" name="erm_email" class="form-control" placeholder="Your Email" required />
        </div>

        <div class="form-group">
            <label for="erm_guests">Number of Guests</label>
            <input type="number" name="erm_guests" class="form-control" placeholder="Number of Guests" required />
        </div>

        <button type="submit" name="erm_submit" class="btn btn-primary">RSVP</button>
    </form>
    <?php

    // Handle form submission
    if (isset($_POST['erm_submit'])) {
        erm_handle_event_rsvp_submission();
    }
}

// Handle event RSVP submission
function erm_handle_event_rsvp_submission() {
    global $wpdb;

    $name = sanitize_text_field($_POST['erm_name']);
    $email = sanitize_email($_POST['erm_email']);
    $guests = intval($_POST['erm_guests']);
    $event_id = intval($_POST['erm_event']);

    // Insert RSVP request into the database
    $wpdb->insert(
        $wpdb->prefix . 'erm_rsvp',
        array(
            'name' => $name,
            'email' => $email,
            'guests' => $guests,
            'status' => 'pending',
            'event_id' => $event_id  // Store the event ID
        )
    );

    // Optionally send confirmation email
    wp_mail($email, 'RSVP Confirmation', 'Thank you for your RSVP! We will notify you once your request is approved.');
}

// Create custom post type for Events
function erm_register_event_post_type() {
    $labels = array(
        'name' => 'Events',
        'singular_name' => 'Event',
        'menu_name' => 'Events',
        'add_new' => 'Add New Event',
        'edit_item' => 'Edit Event',
        'new_item' => 'New Event',
        'view_item' => 'View Event',
        'search_items' => 'Search Events',
        'not_found' => 'No events found',
        'not_found_in_trash' => 'No events found in Trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-calendar',
        'capability_type' => 'post',
    );

    register_post_type('erm_event', $args);
}
add_action('init', 'erm_register_event_post_type');

// Create RSVP Database Table
function erm_create_rsvp_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'erm_rsvp';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        guests smallint NOT NULL,
        status varchar(20) NOT NULL DEFAULT 'pending',
        event_id mediumint(9) NOT NULL,  // Reference to event ID
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'erm_create_rsvp_table');

// Admin Menu and Pages
function erm_register_admin_menu() {
    // Main Menu for RSVP Manager
    add_menu_page('RSVP Manager', 'RSVP Manager', 'manage_options', 'erm_rsvp_manager', 'erm_rsvp_admin_page');

    // Sub-menu for Approved Guests
    add_submenu_page(
        'erm_rsvp_manager',          // Parent slug
        'Approved Guests',           // Page title
        'Approved Guests',           // Menu title
        'manage_options',            // Capability
        'erm_approved_guests',       // Menu slug
        'erm_approved_guests_page'   // Callback function
    );
}
add_action('admin_menu', 'erm_register_admin_menu');

// Admin Page: Pending RSVPs
function erm_rsvp_admin_page() {
    global $wpdb;

    // Fetch available events for filtering
    $events = get_posts(array(
        'post_type' => 'erm_event',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));

    // Filter RSVPs by event
    $selected_event = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
    $where_clause = $selected_event ? "WHERE event_id = $selected_event AND status = 'pending'" : "WHERE status = 'pending'";

    // Fetch RSVPs for the selected event
    $rsvps = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}erm_rsvp $where_clause");

    echo '<h1>Pending RSVPs</h1>';
    
    // Event filter dropdown
    echo '<form method="GET">';
    echo '<input type="hidden" name="page" value="erm_rsvp_manager" />';
    echo '<label for="event_id">Filter by Event:</label>';
    echo '<select name="event_id" onchange="this.form.submit()" class="form-control mb-3">';
    echo '<option value="">All Events</option>';
    foreach ($events as $event) {
        $selected = ($event->ID == $selected_event) ? 'selected' : '';
        echo "<option value='{$event->ID}' $selected>{$event->post_title}</option>";
    }
    echo '</select>';
    echo '</form>';

    // Display RSVPs
    echo '<table class="table table-striped"><tr><th>Name</th><th>Email</th><th>Guests</th><th>Event</th><th>Actions</th></tr>';
    foreach ($rsvps as $rsvp) {
        // Ensure event_id exists and fetch event details
        $event = !empty($rsvp->event_id) ? get_post($rsvp->event_id) : null;
        $event_title = $event ? esc_html($event->post_title) : 'Event Not Found';
        
        echo "<tr>
                <td>{$rsvp->name}</td>
                <td>{$rsvp->email}</td>
                <td>{$rsvp->guests}</td>
                <td>{$event_title}</td>
                <td>
                    <a href='?action=approve&id={$rsvp->id}' class='btn btn-success btn-sm'>Approve</a> | 
                    <a href='?action=reject&id={$rsvp->id}' class='btn btn-danger btn-sm'>Reject</a>
                </td>
              </tr>";
    }
    echo '</table>';
}

// Handle Admin Actions (Approve/Reject)
function erm_handle_admin_actions() {
    global $wpdb;

    if (isset($_GET['action']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $action = sanitize_text_field($_GET['action']);

        if ($action == 'approve') {
            $wpdb->update(
                $wpdb->prefix . 'erm_rsvp',
                array('status' => 'approved'),
                array('id' => $id)
            );
        } elseif ($action == 'reject') {
            $wpdb->delete(
                $wpdb->prefix . 'erm_rsvp',
                array('id' => $id)
            );
        }

        // Redirect to the admin page to avoid resubmission
        wp_redirect(admin_url('admin.php?page=erm_rsvp_manager'));
        exit;
    }
}
add_action('admin_init', 'erm_handle_admin_actions');

// Display Approved Guests
function erm_approved_guests_page() {
    global $wpdb;
    $approved_guests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}erm_rsvp WHERE status = 'approved'");

    echo '<h1>Approved Guests</h1>';
    if ($approved_guests) {
        echo '<table class="table table-striped">';
        echo '<thead><tr><th>Name</th><th>Email</th><th>Guests</th></tr></thead>';
        echo '<tbody>';
        foreach ($approved_guests as $guest) {
            echo "<tr>
                    <td>{$guest->name}</td>
                    <td>{$guest->email}</td>
                    <td>{$guest->guests}</td>
                  </tr>";
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No approved guests found.</p>';
    }
}
?>
