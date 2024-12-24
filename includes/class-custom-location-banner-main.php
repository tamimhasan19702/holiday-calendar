<?php 


class Custom_Location_Banner_Main {

    public function run() {
        add_action( 'init', array( $this, 'create_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_box' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_post_meta_box' ) ); // Add this line
        add_action( 'save_post', array( $this, 'save_custom_meta' ) );
        add_shortcode( 'location_name', array( $this, 'render_location_name_shortcode' ) );
        add_shortcode( 'location_banner', array( $this, 'render_location_banner_shortcode' ) );
        add_shortcode( 'location_links', array( $this, 'render_location_links_shortcode' ) );
    }
    public function create_post_type() {
        register_post_type( 'clb',
            array(
                'labels' => array(
                    'name' => __( 'Custom Location Banners' ),
                ),
                'public' => true,
                'has_archive' => true,
                'supports' => array( 'title', 'custom-fields' ),
            )
        );
    }

    public function add_custom_meta_box() {
        add_meta_box(
            'location_details_meta_box',
            'Location Details',
            array( $this, 'render_location_details_meta_box' ),
            'clb',
            'normal',
            'high'
        );
    }

    public function add_post_meta_box() {
        add_meta_box(
            'custom_location_banner_meta_box', // ID
            'Link Custom Location Banner', // Title
            array( $this, 'render_post_meta_box' ), // Callback
            'post', // Post type
            'side', // Context
            'high' // Priority
        );
    }
    public function render_post_meta_box( $post ) {
        // Get all the location banners
        $location_banners = get_posts( array(
            'post_type' => 'clb',
            'posts_per_page' => -1,
        ) );
    
        // The select field
        echo '<select name="custom_location_banner">';
        echo '<option value="">Select a location banner</option>';
        foreach ( $location_banners as $location_banner ) {
            $selected = ( $location_banner->ID == get_post_meta( $post->ID, '_custom_location_banner', true ) ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $location_banner->ID ) . '" ' . $selected . '>' . esc_html( $location_banner->post_title ) . '</option>';
        }
        echo '</select>';
    }

    public function render_location_details_meta_box( $post ) {
        // Location Name
        echo '<div class="location-meta-box">';
        echo '<label for="location_name">Location Name:</label>';
        echo '<input type="text" name="location_name" id="location_name" value="' . esc_attr( get_post_meta( $post->ID, 'location_name', true ) ) . '" />';

        // File Upload for Location Post Type Banner
        echo '<label for="location_banner">Location Banner:</label>';
        echo '<input type="file" name="location_banner" id="location_banner" accept="image/*" />';

        // Loop for Links
        $links = get_post_meta( $post->ID, 'location_links', true );
        if ( !is_array( $links ) ) {
            $links = array('');
        }
        
        // Create a container for the link fields
        echo '<div id="link-fields-container">';
        
        foreach ( $links as $index => $link ) {
            echo '<div class="link-field" id="link-field-' . $index . '">';
            echo '<label for="location_links_' . $index . '">Link ' . ($index + 1) . ':</label>';
            echo '<input type="url" name="location_links[]" id="location_links_' . $index . '" value="' . esc_attr( $link ) . '" />';
            echo '</div>';
        }
        
        echo '</div>'; // Close link-fields-container

        // Add More Links button
        echo '<button type="button" class="add-link-button" onclick="addLinkField()">Add New Link</button>';
        // Delete Last Link button
        echo '<button type="button" class="delete-link-button" onclick="deleteLastLinkField()">Delete Last Link</button>';
        echo '</div>'; // Close location-meta-box

        // JavaScript for adding and deleting link fields
        echo '<script>
            function addLinkField() {
                const linkFields = document.querySelectorAll(\'input[name="location_links[]"]\');
                const newIndex = linkFields.length; // Use the current length as the new index
                const newField = `<div class="link-field" id="link-field_${newIndex}">
                                    <label for="location_links_${newIndex}">Link ${newIndex + 1}:</label>
                                    <input type="url" name="location_links[]" id="location_links_${newIndex}" />
                                  </div>`;
                document.getElementById("link-fields-container").insertAdjacentHTML("beforeend", newField);
            }

            function deleteLastLinkField() {
                const linkFields = document.querySelectorAll(".link-field");
                if ( linkFields.length > 0 ) {
                    const lastField = linkFields[linkFields.length - 1];
                    lastField.remove();
                    renumberLinkFields(); // Renumber the fields after deletion
                }
            }

            function renumberLinkFields() {
                const linkFields = document.querySelectorAll(".link-field");
                linkFields.forEach((field, index) => {
                    const label = field.querySelector("label");
                    const input = field.querySelector("input");

                    // Update the label text
                    label.innerHTML = "Link " + (index + 1) + ":";
                    // Update the input id and name
                    input.id = "location_links_" + index;
                    input.name = "location_links[]";
                    field.id = "link-field-" + index; // Update the field id
                });
            }
        </script>';

        // CSS for styling the meta box
        echo '<style>
            .location-meta-box {
                margin: 10px 0;
            }
            .location-meta-box label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            .location-meta-box input[type="text"],
            .location-meta-box input[type="url"],
            .location-meta-box input[type="file"] {
                width: 100%;
                padding: 8px;
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
            .add-link-button, .delete-link-button {
                background-color: #0073aa;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 4px;
                cursor: pointer;
                margin-top: 10px;
                margin-left: 10px;
            }
            .add-link-button:hover, .delete-link-button:hover {
                background-color: #005177;
            }
            .link-field {
                margin-bottom: 15px;
            }
        </style>';
    }

    public function render_location_name_shortcode() {
        return $this->get_location_detail('location_name');
    }

    public function render_location_banner_shortcode() {
        $banner_url = $this->get_location_detail('location_banner_url');
        return $banner_url ? '<img src="' . esc_url($banner_url) . '" alt="Location Banner" style="max-width: 100%; height: auto;" />' : '';
    }

    public function render_location_links_shortcode($atts) {
        $link_number = isset($atts['number']) ? intval($atts['number']) : 1;
        $links = $this->get_location_detail('location_links');

        if (is_array($links) && isset($links[$link_number - 1])) {
            return '<a href="' . esc_url($links[$link_number - 1]) . '">' . esc_html($links[$link_number - 1]) . '</a>';
        }
        return 'No link available.';
    }

    private function get_location_detail($meta_key) {
        // Get the current post ID
        $post_id = get_the_ID();
        
        // Get the selected custom location banner ID
        $selected_banner_id = get_post_meta($post_id, '_custom_location_banner', true);

        if (!$selected_banner_id) {
            return '';
        }

        // Get the location detail
        return get_post_meta($selected_banner_id, $meta_key, true);
    }

    public function save_custom_meta($post_id) {
        // Check if our nonce is set and verify it
        if (!isset($_POST['custom_location_banner'])) {
            return;
        }
    
        // Save the selected banner ID
        $selected_banner = sanitize_text_field($_POST['custom_location_banner']);
        update_post_meta($post_id, '_custom_location_banner', $selected_banner);
    
        // Save additional location details
        if ($selected_banner) {
            // Save the location name
            if (isset($_POST['location_name'])) {
                $location_name = sanitize_text_field($_POST['location_name']);
                update_post_meta($selected_banner, 'location_name', $location_name);
            }
    
            // Handle file upload for the banner
            if (isset($_FILES['location_banner']) && $_FILES['location_banner']['error'] == UPLOAD_ERR_OK) {
                $banner_file = $_FILES['location_banner'];
                $upload_dir = wp_upload_dir();
                $banner_url = $upload_dir['url'] . '/' . basename($banner_file['name']);
                $upload_file = $upload_dir['path'] . '/' . basename($banner_file['name']);
                move_uploaded_file($banner_file['tmp_name'], $upload_file);
                update_post_meta($selected_banner, 'location_banner_url', $banner_url);
            }
    
            // Save the location links
            if (isset($_POST['location_links'])) {
                $location_links = array_map('sanitize_text_field', $_POST['location_links']);
                update_post_meta($selected_banner, 'location_links', $location_links);
            }
        }
    }
}