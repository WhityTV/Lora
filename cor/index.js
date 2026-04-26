 $(function () {
  const $art_cat_trigger = $(".art_cat_trigger");   
  const $art_cat = $(".art_cat");
  const $art_cat_area = $art_cat_trigger.add($art_cat);

  $art_cat_trigger.on("click", function () {
    $art_cat.toggle();
  });

}); 

 $(function () {
  const $last_art_trigger = $(".last_art_trigger");   
  const $last_art = $(".last_art");
  const $last_art_area = $last_art_trigger.add($last_art);

  $last_art_trigger.on("click", function () {
    $.get("last_art.php", function (data) {
      $last_art.html(data);
      $last_art.toggle();
    });
  });

}); 