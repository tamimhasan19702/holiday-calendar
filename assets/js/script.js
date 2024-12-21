/** @format */
/** @format */

jQuery(document).ready(function ($) {
  $("#mark-unused-images-button").on("click", function () {
    const button = $(this);
    button.prop("disabled", true).text("Processing...");

    $.ajax({
      url: ajaxurl, // This should be defined in your PHP code using wp_localize_script
      type: "POST",
      dataType: "json",
      data: {
        action: "mark_unused_images", // The action name that corresponds to your PHP function
        security: "<?php echo wp_create_nonce('mark_unused_images_action'); ?>", // Security nonce
      },
      success: function (response) {
        if (response.success) {
          // Alert the unused image IDs
          alert(
            "Unused Image IDs: " + response.data.unused_image_ids.join(", ")
          );

          // Optionally, you can also alert the counts
          alert(
            response.data.message +
              "\nUsed Images: " +
              response.data.used_count +
              "\nUnused Images: " +
              response.data.unused_count
          );

          // Reload the page if needed
          // location.reload();
        } else {
          alert("Error: " + response.data.message);
        }
        button.prop("disabled", false).text("Mark Unused Images");
      },
      error: function () {
        alert(
          "An error occurred while processing. Please check the console for details."
        );
        button.prop("disabled", false).text("Mark Unused Images");
      },
    });
  });
});
