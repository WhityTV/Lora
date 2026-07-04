$(function () {
  // The trigger is the visible menu label that opens/closes the category list.
  const $art_cat_trigger = $(".art_cat_trigger");   
  // The category panel contains the links and is initially hidden via CSS.
  const $art_cat = $(".art_cat");
  // Keep a grouped reference of trigger + panel for future extensions
  // (e.g., click-outside-to-close behavior).
  const $art_cat_area = $art_cat_trigger.add($art_cat);

  // Toggle category visibility whenever the trigger is clicked.
  $art_cat_trigger.on("click", function () {
    $art_cat.toggle();
  });

}); 

$(function () {
  // Click target that requests and displays recent article content.
  const $last_art_trigger = $(".last_art_trigger");   
  // Container where the server response HTML will be injected.
  const $last_art = $(".last_art");
  // Grouped reference kept for potential hover/outside-click logic.
  const $last_art_area = $last_art_trigger.add($last_art);

  // On click, fetch the latest articles markup and toggle panel visibility.
  $last_art_trigger.on("click", function () {
    // Request partial HTML from the backend endpoint.
    $.get("last_art.php", function (data) {
      // Update panel content with fresh server-rendered HTML.
      $last_art.html(data);
      // Show/hide the panel after content is loaded.
      $last_art.toggle();
    });
  });

});

$(function () {
  // My account dropdown interaction.
  const $my_acc_trigger = $(".my_acc");
  const $my_acc_menu = $(".my_acc_menu");
  
  // Open/close menu on account click.
  $my_acc_trigger.on("click", function (event) {
    event.stopPropagation();
    $my_acc_menu.toggleClass("show");
  });

  // Keep menu open when clicking inside it.
  $my_acc_menu.on("click", function (event) {
    event.stopPropagation();
  });

  // Close menu when clicking anywhere else.
  $(document).on("click", function () {
    $my_acc_menu.removeClass("show");
  });
});