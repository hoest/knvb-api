$(function() {
  $(".knvbteam-slider").each(function() {
    var sliderElt = $(this);
    $(".game", sliderElt).hide();
    $(".game", sliderElt).first().show();

    sliderElt.prepend("<a class=\"prev\" href title=\"Vorige wedstrijd\">Vorige</a>");
    $("a.prev", sliderElt).off("click").on("click", function(event) {
      var current = $(".game:visible", sliderElt);
      var next = $(".game:visible", sliderElt).next(".game");

      if(next.length) {
        current.hide();
        next.show();
      }

      checkButtonState(sliderElt);
      event.preventDefault();
    });

    sliderElt.append("<a class=\"next\" href title=\"Volgende wedstrijd\">Volgende</a>");
    $("a.next", sliderElt).off("click").on("click", function(event) {
      var current = $(".game:visible", sliderElt);
      var prev = $(".game:visible", sliderElt).prev(".game");

      if(prev.length) {
        current.hide();
        prev.show();
      }

      checkButtonState(sliderElt);
      event.preventDefault();
    });

    checkButtonState(sliderElt);
  });
});

function checkButtonState(sliderElt) {
  if($(".game:visible", sliderElt).next(".game").length) {
    $("a.prev", sliderElt).show();
  }
  else {
    $("a.prev", sliderElt).hide();
  }

  if($(".game:visible", sliderElt).prev(".game").length) {
    $("a.next", sliderElt).show();
  }
  else {
    $("a.next", sliderElt).hide();
  }
}
