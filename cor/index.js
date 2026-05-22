$(function () {
  // Article category dropdown
  const $art_cat_trigger = $(".art_cat_trigger");   
  const $art_cat = $(".art_cat");
  const $art_cat_area = $art_cat_trigger.add($art_cat);

  // Toggle category list on click
  $art_cat_trigger.on("click", function () {
    $art_cat.toggle();
  });

}); 

$(function () {
  // Latest articles panel
  const $last_art_trigger = $(".last_art_trigger");   
  const $last_art = $(".last_art");
  const $last_art_area = $last_art_trigger.add($last_art);

  // Load latest articles from server and toggle panel
  $last_art_trigger.on("click", function () {
    $.get("last_art.php", function (data) {
      $last_art.html(data);
      $last_art.toggle();
    });
  });

});

$(function () {
  // My account dropdown
  const $my_acc_trigger = $(".my_acc");
  const $my_acc_menu = $(".my_acc_menu");
  
  // Open/close menu on account click
  $my_acc_trigger.on("click", function (event) {
    event.stopPropagation();
    $my_acc_menu.toggleClass("show");
  });

  // Keep menu open when clicking inside it
  $my_acc_menu.on("click", function (event) {
    event.stopPropagation();
  });

  // Close menu when clicking anywhere else
  $(document).on("click", function () {
    $my_acc_menu.removeClass("show");
  });
});