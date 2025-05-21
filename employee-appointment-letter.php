<?php
/*
Plugin Name: Employee Letter Generator
Description: A plugin to generate, view, edit, and manage employee letters such as appointment letters and experience certificates.
Version: 3.5
Author: Tarun
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include TCPDF Library
function elg_include_tcpdf_library() {
    if (!class_exists('TCPDF')) {
        require_once plugin_dir_path(__FILE__) . 'tcpdf/tcpdf.php';
    }
}

elg_include_tcpdf_library();
// Extend TCPDF Class
class TemplatePDF extends TCPDF {
    public function Header() {
        // Now add the header content (e.g., logo)
        $logo_file = plugin_dir_path(__FILE__) . 'images/logo_ganeve.png';
        if (file_exists($logo_file)) {
            $this->Image($logo_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0);
        }
        /*
        // Add additional header text or content if needed
        $this->SetTextColor(0, 0, 0);
        // Example: First line with larger font size
        $this->SetFont('helvetica', 'B', 14); // Bold, size 14
        $this->Cell(0, 5, 'Dr. Nidhi Abrol', 0, 1, 'R'); // Right-aligned text

        // Example: Second line with smaller font size
        $this->SetFont('helvetica', '', 10); // Regular, size 10
        $this->Cell(0, 5, 'Senior Consultant Physiotherapist', 0, 1, 'R'); // Right-aligned text

        // Example: Third line with medium font size
        $this->SetFont('helvetica', '', 10); // Regular, size 12
        $this->Cell(0, 5, 'BPT, MPT (ORTHO), MIAP, DHA, FNMT', 0, 1, 'R'); // Right-aligned text
        
         // Set X and Y for the text
         $this->SetXY(100, 10); // Move to the top-right area of the page
         */
 
    }

    public function ApplyColorBorder() {
        // Get page dimensions
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        // Set the  color for the border
        $this->SetDrawColor(0, 0, 0); // Red color
        $this->SetLineWidth(7); // Width of the first border line
        $this->Line($pageWidth, 0, $pageWidth , $pageHeight); // Draw red line 10mm from the right edge
    }

    public function ApplyBackground() {
       // Get page dimensions
        $pageWidth = $this->getPageWidth();
        $pageHeight = $this->getPageHeight();

        // Set the first background image
        $bgImage1 = __DIR__ . '/images/background.png'; // Update the path to your first image
        $bgImage2 = __DIR__ . '/images/watermark.png'; // Update the path to your second image

        // First image: Full-page background with specific width
        $bgImage1Width = 10; // Set the desired width for the first image in mm
        if (file_exists($bgImage1)) {
            // Calculate the aspect ratio for the first image
            list($originalWidth1, $originalHeight1) = getimagesize($bgImage1);
            $aspectRatio1 = $originalHeight1 / $originalWidth1;
            
            // Calculate the height based on the aspect ratio and the given width
            $bgImage1Height = $bgImage1Width * $aspectRatio1;

            // Place the first image (top-left corner)
            $this->Image(
                $bgImage1,
                $pageWidth - $bgImage1Width,   // X position (top-right corner)
                0,                          // Y position (top of the page)
                $bgImage1Width,             // Width of the image
                $bgImage1Height,            // Height of the image (calculated)
                '',                         // Image type (auto-detected)
                '',                         // Link (none)
                '',                         // Align (none)
                false,                      // Resize (false to keep aspect ratio)
                300,                        // Resolution (DPI)
                '',                         // Image align (none)
                false,                      // Border
                false,                      // Fit box
                0,                          // Fit on page
                false,                      // Allow negative X or Y
                false                       // Fit box to dimensions
            );
        } else {
            // Apply a fallback solid color if the first image is missing
            $this->SetFillColor(200, 200, 200); // Light gray background
            $this->Rect(0, 0, $pageWidth, $pageHeight, 'F'); // Fill the background
        }

        // Second image: Center-right position with specific width
        $bgImage2Width = 120; // Set the desired width for the second image in mm
        if (file_exists($bgImage2)) {
            // Calculate the aspect ratio for the second image
            list($originalWidth2, $originalHeight2) = getimagesize($bgImage2);
            $aspectRatio2 = $originalHeight2 / $originalWidth2;

            // Calculate the height based on the aspect ratio and the given width
            $bgImage2Height = $bgImage2Width * $aspectRatio2;

            // Calculate the X and Y positions for center-right placement
            $xPosition = $pageWidth - $bgImage2Width; // 10mm padding from the right edge
            $yPosition = ($pageHeight - $bgImage2Height) / 2; // Center vertically

            // Place the second image
            $this->Image(
                $bgImage2,
                $xPosition,                // X position (center-right)
                $yPosition,                // Y position (centered vertically)
                $bgImage2Width,            // Width of the image
                $bgImage2Height,           // Height of the image (calculated)
                '',                        // Image type (auto-detected)
                '',                        // Link (none)
                '',                        // Align (none)
                false,                     // Resize (false to keep aspect ratio)
                300,                       // Resolution (DPI)
                '',                        // Image align (none)
                false,                     // Border
                false,                     // Fit box
                0,                         // Fit on page
                false,                     // Allow negative X or Y
                false                      // Fit box to dimensions
            );
        } else {
            // Log a message or handle the missing second image
            throw new Exception('Second background image file not found: ' . $bgImage2);
        }
    }

    // Define the Footer
    public function Footer() {
        // Set position at 15mm from bottom
        $this->SetY(-15); // Adjust Y position to leave space for two rows
        $pageWidth = $this->getPageWidth(); // Full page width
        $pageHeight = $this->getPageHeight(); // Full page height
        $footerHeight = 4; // Set the height of the footer
        $footerWidth = 60; // Set the custom width for the footer background

        // Add Footer Background
        $bg_image_footer = plugin_dir_path(__FILE__) . 'images/background2.png';
        if (file_exists($bg_image_footer)) {
            // Set the background image to start from bottom-left with custom width
            $x = 0; // Align to the left
            $y = $pageHeight - $footerHeight; // Align to the bottom
            $this->Image($bg_image_footer, $x, $y, $footerWidth, $footerHeight, '', '', '', false, 300, '', false, false, 0);
        }

        // Set font for footer
        $this->SetFont('helvetica', '', 8);

        /*// Define HTML content for the footer
        $html = '
            <table>
                <tr>
                    <td style="text-align: left; width: 50%;">
                        <span style="color: rgb(26, 38, 60);">For Appointments, call:</span>
                         <span>+91 9971 466 533, +91 9971 494 533</span>
                    </td>
                    <td style="text-align: right; width: 50%;">
                        <span>This is valid for one week</span>
                    </td>
                </tr>
            </table>
        ';

        // Write the HTML content
        $this->writeHTML($html, true, false, true, false, '');
        */
    }
}

// Create Custom Table for Letters
function elg_create_letters_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'employee_letters';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE wp_employee_letters (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    employee_name VARCHAR(100) NOT NULL,
    letter_type VARCHAR(50) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    joining_date DATE NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    salary DECIMAL(10,2) NULL,
    company_name VARCHAR(100) NOT NULL,
    company_address TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'elg_create_letters_table');

// Add Admin Menu for the Plugin
function elg_add_admin_menu() {
    add_menu_page(
        'Employee Letter Generator',
        'Employee Letters',
        'manage_options',
        'employee-letter-generator',
        'elg_admin_page',
        'dashicons-media-document',
        20
    );

    add_submenu_page(
        'employee-letter-generator',
        'View All Letters',
        'View Letters',
        'manage_options',
        'view-employee-letters',
        'elg_view_letters_page'
    );

    add_submenu_page(
        null,
        'Edit Letter',
        'Edit Letter',
        'manage_options',
        'edit-employee-letter',
        'elg_edit_letter_page'
    );
}
add_action('admin_menu', 'elg_add_admin_menu');

// Enqueue Font Awesome in Admin Pages
function elg_enqueue_font_awesome($hook) {
    if (strpos($hook, 'employee-letter-generator') !== false || strpos($hook, 'view-employee-letters') !== false) {
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
            [],
            '6.0.0',
            'all'
        );
    }
}
add_action('admin_enqueue_scripts', 'elg_enqueue_font_awesome');

// Admin Page for Generating Letters
function elg_admin_page() {
    ?>
    <div class="wrap">
        <h1>Generate Employee Letter</h1>
        <form method="post" action="">
            <label for="employee_name">Employee Name:</label><br>
            <input type="text" id="employee_name" name="employee_name" required><br><br>

            <label for="designation">Designation:</label><br>
            <input type="text" id="designation" name="designation" required><br><br>

            <label for="letter_type">Letter Type:</label><br>
            <select id="letter_type" name="letter_type" required onchange="toggleLetterFields()">
                <option value="appointment" <?php selected($letter->letter_type, 'appointment'); ?>>Appointment Letter</option>
                <option value="experience" <?php selected($letter->letter_type, 'experience'); ?>>Experience Certificate</option>
            </select><br><br>
            <div id="appointment_fields" <?php echo ($letter->letter_type === 'appointment') ? '' : 'style="display: none;"'; ?>>
                <label for="joining_date">Joining Date:</label><br>
                <input type="date" id="joining_date" name="joining_date" value="<?php echo esc_attr($letter->joining_date); ?>"><br><br>

                <label for="salary">Salary:</label><br>
                <input type="number" id="salary" name="salary" step="0.01" value="<?php echo esc_attr($letter->salary); ?>"><br><br>
            </div>

            <div id="experience_fields" <?php echo ($letter->letter_type === 'experience') ? '' : 'style="display: none;"'; ?>>
                <label for="start_date">Start Date:</label><br>
                <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($letter->start_date); ?>"><br><br>

                <label for="end_date">End Date:</label><br>
                <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($letter->end_date); ?>"><br><br>
            </div>

            <label for="company_name">Company Name:</label><br>
            <input type="text" id="company_name" name="company_name" required><br><br>

            <label for="company_address">Company Address:</label><br>
            <textarea id="company_address" name="company_address" rows="4" cols="50" required></textarea><br><br>

            <label for="letter_content">Letter Content:</label><br>
            <?php
            $default_content = '<p>Dear [Employee Name],</p>
<p>We are pleased to offer you the position of [Designation] at our company. Please find the details below.</p>
<p>Regards,</p>
<p>[Authority Name]</p>
<p>[Signature]</p>';
            wp_editor($default_content, 'letter_content', [
                'textarea_name' => 'letter_content',
                'textarea_rows' => 10,
                'media_buttons' => false,
            ]);
            ?>
            <br><br>

            <input type="submit" name="generate_letter" value="Generate Letter" class="button-primary">
        </form>
    </div>
    <script>
        function updateEditorContent() {
            const letterType = document.getElementById('letter_type').value;
            let content = '';

            if (letterType === 'appointment') {
                content = `<p>Dear [Employee Name],</p>
<p>We are pleased to offer you the position of [Designation] at our company. Please find the details below.</p>
<p>Regards,</p>
<p>[Authority Name]</p>
<p>[Signature]</p>`;
            } else if (letterType === 'experience') {
                content = `<p>To Whom It May Concern,</p>
<p>This is to certify that [Employee Name] worked with us as a [Designation].</p>
<p>Regards,</p>
<p>[Authority Name]</p>
<p>[Signature]</p>`;
            }

            // Update the WordPress editor content
            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.get('letter_content').setContent(content);
            }
        }
    </script>
    <?php

    if (isset($_POST['generate_letter'])) {
        $employee_name = sanitize_text_field($_POST['employee_name']);
        $designation = sanitize_text_field($_POST['designation']);
        $letter_type = sanitize_text_field($_POST['letter_type']);
        $joining_date = isset($_POST['joining_date']) ? sanitize_text_field($_POST['joining_date']) : null;
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $salary = isset($_POST['salary']) ? sanitize_text_field($_POST['salary']) : null;
        $company_name = sanitize_text_field($_POST['company_name']);
        $company_address = sanitize_textarea_field($_POST['company_address']);
        $content = wp_kses_post($_POST['letter_content']);

        elg_save_letter_to_db($employee_name, $designation, $letter_type, $joining_date, $start_date, $end_date, $salary, $company_name, $company_address, $content);
    }
}

// Save Letter to Database
function elg_save_letter_to_db($employee_name, $designation, $letter_type, $joining_date, $start_date, $end_date, $salary, $company_name, $company_address, $content) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'employee_letters';

    $wpdb->insert($table_name, [
        'employee_name' => $employee_name,
        'designation' => $designation,
        'letter_type' => $letter_type,
        'joining_date' => $joining_date,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'salary' => $salary,
        'company_name' => $company_name,
        'company_address' => $company_address,
        'content' => $content,
    ]);

    echo '<div class="notice notice-success is-dismissible"><p>Letter saved successfully!</p></div>';
}

// View Letters Page
function elg_view_letters_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'employee_letters';

    if (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $wpdb->delete($table_name, ['id' => $delete_id]);
        echo '<div class="notice notice-success is-dismissible"><p>Letter deleted successfully.</p></div>';
    }

    $letters = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    ?>
    <div class="wrap">
        <h1>All Employee Letters</h1>
        <table class="widefat fixed" cellspacing="0" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Employee Name</th>
                    <th>Letter Type</th>
                    <th>Designation</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($letters)) : ?>
                    <?php foreach ($letters as $letter) : ?>
                        <tr>
                            <td><?php echo esc_html($letter->id); ?></td>
                            <td><?php echo esc_html($letter->employee_name); ?></td>
                            <td><?php echo esc_html(ucfirst($letter->letter_type)); ?></td>
                            <td><?php echo esc_html($letter->designation); ?></td>
                            <td><?php echo esc_html($letter->created_at); ?></td>
                            <td>
                                <!-- WhatsApp Icon -->
                                <a href="https://wa.me/?text=Hello+<?php echo urlencode($letter->employee_name); ?>,+here+is+your+letter" target="_blank" title="Send via WhatsApp Business">
                                    <i class="fab fa-whatsapp"></i>
                                </a>

                                <!-- View PDF Icon -->
                                <a href="<?php echo admin_url('admin-post.php?action=view_letter_pdf&letter_id=' . $letter->id); ?>" target="_blank" title="View PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>

                                <!-- Edit Icon -->
                                <a href="<?php echo admin_url('admin.php?page=edit-employee-letter&edit_id=' . $letter->id); ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete Icon -->
                                <a href="<?php echo admin_url('admin.php?page=view-employee-letters&delete_id=' . $letter->id); ?>" onclick="return confirm('Are you sure you want to delete this letter?');" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6">No letters found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Handle PDF Generation and Viewing
function elg_view_pdf() {
    if (isset($_GET['letter_id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'employee_letters';

        $letter_id = intval($_GET['letter_id']);
        $letter = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $letter_id));

        if ($letter) {
            elg_generate_pdf($letter);
        } else {
            wp_die('Letter not found.');
        }
    }
}
add_action('admin_post_view_letter_pdf', 'elg_view_pdf');

// Generate PDF
function elg_generate_pdf($letter) {
    elg_include_tcpdf_library();

    $pdf = new TemplatePDF();
    $pdf->SetCreator($letter->company_name);
    $pdf->SetAuthor('HR Department');
    $pdf->SetTitle($letter->letter_type . ' Letter');
    $pdf->SetSubject('Employee Letter');
    $pdf->SetKeywords('Employee, Letter, PDF');
    $pdf->SetMargins(15, 35, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 25);

    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);

    $pdf->AddPage();

    $pdf->ApplyBackground();
    $pdf->ApplyColorBorder();


    $html = '<h1 style="text-align: center;">' . ucfirst($letter->letter_type) . ' Letter</h1>';
    $html .= '<p>Dear <strong>' . esc_html($letter->employee_name) . '</strong>,</p>';
    $html .= '<p>This is your ' . esc_html($letter->letter_type) . ' letter for the position of <strong>' . esc_html($letter->designation) . '</strong>.</p>';
    if ($letter->letter_type === 'appointment') {
        $html .= '<p>Your joining date is <strong>' . esc_html($letter->joining_date) . '</strong>, and your salary is <strong>$' . esc_html($letter->salary) . '</strong>.</p>';
    } elseif ($letter->letter_type === 'experience') {
        $html .= '<p>You worked with us from <strong>' . esc_html($letter->start_date) . '</strong> to <strong>' . esc_html($letter->end_date) . '</strong>.</p>';
    }
    $html .= '<p>Company Name: <strong>' . esc_html($letter->company_name) . '</strong></p>';
    $html .= '<p>Company Address: <strong>' . nl2br(esc_html($letter->company_address)) . '</strong></p>';
    $content = $letter->content; // Fetch the content from the report
    $html .= '<p>' . nl2br(esc_html($letter->content)) . '</p>';
    $html .= '<p>Sincerely,</p>';
    $html .= '<p><strong>HR Department</strong></p>';
    $html .= $content; // Append the content to the HTML

    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output($letter->employee_name . '_' . $letter->letter_type . '_letter.pdf', 'I');
    exit;
}

function elg_edit_letter_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'employee_letters';

    $edit_id = intval($_GET['edit_id']);
    $letter = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $edit_id));

    if (!$letter) {
        echo '<div class="notice notice-error"><p>Letter not found.</p></div>';
        return;
    }

    // Check if content exists
    if (!isset($letter->content) || empty($letter->content)) {
        $letter->content = ''; // Default value if content is missing
    }

    if (isset($_POST['update_letter'])) {
        $employee_name = sanitize_text_field($_POST['employee_name']);
        $designation = sanitize_text_field($_POST['designation']);
        $letter_type = sanitize_text_field($_POST['letter_type']);
        $joining_date = isset($_POST['joining_date']) ? sanitize_text_field($_POST['joining_date']) : null;
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;
        $salary = isset($_POST['salary']) ? sanitize_text_field($_POST['salary']) : null;
        $company_name = sanitize_text_field($_POST['company_name']);
        $company_address = sanitize_textarea_field($_POST['company_address']);
        $content = wp_kses_post($_POST['letter_content']);

        $wpdb->update($table_name, [
            'employee_name' => $employee_name,
            'designation' => $designation,
            'letter_type' => $letter_type,
            'joining_date' => $joining_date,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'salary' => $salary,
            'company_name' => $company_name,
            'company_address' => $company_address,
            'content' => $content,
        ], ['id' => $edit_id]);

        echo '<div class="notice notice-success is-dismissible"><p>Letter updated successfully!</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Edit Employee Letter</h1>
        <form method="post" action="">
            <label for="employee_name">Employee Name:</label><br>
            <input type="text" id="employee_name" name="employee_name" value="<?php echo esc_attr($letter->employee_name); ?>" required><br><br>

            <label for="designation">Designation:</label><br>
            <input type="text" id="designation" name="designation" value="<?php echo esc_attr($letter->designation); ?>" required><br><br>

            <label for="letter_type">Letter Type:</label><br>
            <select id="letter_type" name="letter_type" required onchange="updateEditorContent()">
                <option value="appointment">Appointment Letter</option>
                <option value="experience">Experience Certificate</option>
            </select><br><br>

            <div id="appointment_fields" <?php echo ($letter->letter_type === 'appointment') ? '' : 'style="display: none;"'; ?>>
                <label for="joining_date">Joining Date:</label><br>
                <input type="date" id="joining_date" name="joining_date" value="<?php echo esc_attr($letter->joining_date); ?>"><br><br>

                <label for="salary">Salary:</label><br>
                <input type="number" id="salary" name="salary" step="0.01" value="<?php echo esc_attr($letter->salary); ?>"><br><br>
            </div>

            <div id="experience_fields" <?php echo ($letter->letter_type === 'experience') ? '' : 'style="display: none;"'; ?>>
                <label for="start_date">Start Date:</label><br>
                <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($letter->start_date); ?>"><br><br>

                <label for="end_date">End Date:</label><br>
                <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($letter->end_date); ?>"><br><br>
            </div>

            <label for="company_name">Company Name:</label><br>
            <input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($letter->company_name); ?>" required><br><br>

            <label for="company_address">Company Address:</label><br>
            <textarea id="company_address" name="company_address" rows="4" cols="50" required><?php echo esc_textarea($letter->company_address); ?></textarea><br><br>

            <label for="letter_content">Letter Content:</label><br>
            <?php
            $default_content = '<p>Dear [Employee Name],</p>
<p>We are pleased to offer you the position of [Designation] at our company. Please find the details below.</p>
<p>Regards,</p>
<p>[Authority Name]</p>
<p>[Signature]</p>';
            wp_editor($default_content, 'letter_content', [
                'textarea_name' => 'letter_content',
                'textarea_rows' => 10,
                'media_buttons' => false,
            ]);
            ?>
            <br><br>

            <input type="submit" name="update_letter" value="Update Letter" class="button-primary">
        </form>
    </div>
    <?php
}
