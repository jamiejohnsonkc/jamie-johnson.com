alert('Hello, world!');
// jQuery(document).ready(function($) {
//     if(window.location !== "http://www.jamiejohnsonmev2.test/expertise/"){
jQuery(document).ready(function($) {
    var open = document.getElementById("open-expt-overlay-1"),
        close = document.getElementById("expt-close-1"),
        over = document.getElementById("expt-overlay-1");
    body = document.body;
    open.addEventListener("click", function() {
        over.classList.add("show");
        body.classList.add("nope");
    });
    close.addEventListener("click", function() {
        over.classList.remove("show");
        body.classList.remove("nope");
    });
});

jQuery(document).ready(function($) {
    var open = document.getElementById("open-expt-overlay-2"),
        close = document.getElementById("expt-close-2"),
        over = document.getElementById("expt-overlay-2");
    if (open) {
        open.addEventListener("click", function() {
            over.classList.add("show");
            body.classList.add("nope");
        });
        close.addEventListener("click", function() {
            over.classList.remove("show");
            body.classList.remove("nope");
        });
    }
});

jQuery(document).ready(function($) {
    var open = document.getElementById("open-expt-overlay-3"),
        close = document.getElementById("expt-close-3"),
        over = document.getElementById("expt-overlay-3");
    if (open) {
        open.addEventListener("click", function() {
            over.classList.add("show");
            body.classList.add("nope");
        });
        close.addEventListener("click", function() {
            over.classList.remove("show");
            body.classList.remove("nope");
        });
    }
});

jQuery(document).ready(function($) {
    var open = document.getElementById("open-expt-overlay-4"),
        close = document.getElementById("expt-close-4"),
        over = document.getElementById("expt-overlay-4");
    if (open) {
        open.addEventListener("click", function() {
            over.classList.add("show");
            body.classList.add("nope");
        });
        close.addEventListener("click", function() {
            over.classList.remove("show");
            body.classList.remove("nope");
        });
    }
});

jQuery(document).ready(function($) {
  var open = document.getElementById("open-expt-overlay-5"),
      close = document.getElementById("expt-close-5"),
      over = document.getElementById("expt-overlay-5");
 if(open)
  {  open.addEventListener("click", function(){
    over.classList.add("show");
   body.classList.add("nope");
  });
  close.addEventListener("click", function(){
    over.classList.remove("show");
    body.classList.remove("nope");
  });
}
});



jQuery(document).ready(function($) {
$('.moreless-button').click(function() {
  $('.moretext').slideToggle();
  if ($('.moreless-button').text() == "Read more") {
    $(this).text("Read less")
  } else {
    $(this).text("Read more")
  }
});
});
jQuery(document).ready(function($) {
    $(".moreless-button__outcomes").click(function() {
        $(".read-more__outcomes").slideToggle();
        if ($(".moreless-button__outcomes").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});

jQuery(document).ready(function($) {
    $(".moreless-button__value").click(function() {
        $(".read-more__value").slideToggle();
        if ($(".moreless-button__value").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});

jQuery(document).ready(function($) {
    $(".moreless-button__modernize").click(function() {
        $(".read-more__modernize").slideToggle();
        if ($(".moreless-button__modernize").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});

jQuery(document).ready(function($) {
    $(".moreless-button__optimize").click(function() {
        $(".read-more__optimize").slideToggle();
        if ($(".moreless-button__optimize").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});

jQuery(document).ready(function($) {
    $(".moreless-button__dev-design").click(function() {
        $(".panel__content_dev-design--read-more").slideToggle();
        if ($(".moreless-button__dev-design").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});

jQuery(document).ready(function($) {
    $(".moreless-button__planning-analysis").click(function() {
        $(".panel__content_planning-analysis--read-more").slideToggle();
        if ($(".moreless-button__planning-analysis").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});

jQuery(document).ready(function($) {
    $(".moreless-button__ops-management").click(function() {
        $(".panel__content_ops-management--read-more").slideToggle();
        if ($(".moreless-button__ops-management").text() == "Read more") {
            $(this).text("Read less");
        } else {
            $(this).text("Read more");
        }
    });
});
 $(".scroll-top").click(function() {
    $('html,body').animate({
        scrollTop: $(".second").offset().top},
        'slow');
});





// const btn = document.getElementById('button');
// btn.addEventListener('click', function() {
//     alert('Hello World!');
// });

/*var lastTop;

function stopScrolling() {
    lastTop = $(window).scrollTop();      
    $('body').addClass( 'nope' )          
         .css( { top: -lastTop } )        
         ;            
}

function continueScrolling() {                    

    $('body').removeClass( 'nope' );      
    $(window).scrollTop( lastTop );       
} 

window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
    document.getElementById("scroll-up").style.display = "block";
  } else {
    document.getElementById("scroll-up").style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
*/
// var $el, $ps, $up, totalHeight;
// $(".backstory__content .button").click(function() {
//   totalHeight = 0
//   $el = $(this);
//   $p  = $el.parent();
//   $up = $p.parent();
//   $ps = $up.find("p:not('.read-more')");
//   // measure how tall inside should be by adding together heights of all inside paragraphs (except read-more paragraph)
//   $ps.each(function() {
//     totalHeight += $(this).outerHeight();
//   });
//   $up
//     .css({
//       // Set height to prevent instant jumpdown when max height is removed
//       "height": $up.height(),
//       "max-height": 9999
//     })
//     .animate({
//       "height": totalHeight
//     });
//   // fade out read-more
//   $p.fadeOut();
//   // prevent jump-down
//   return false;
// }); 
//  var alterClass = function() {
//     var ww = document.body.clientWidth;
// if (ww >= 1023) {
//       $('.quals').removeClass('stats');
//     };
//   };
/*  $(window).resize(function(){
    alterClass();
  });*/
//Fire it when the page first loads:
/*alterClass();*/
// fitty('.fit');
// /*no-scroll*/
// var noScroll = require('..');
// global.noScroll = noScroll;
// var activators = document.getElementsByClassName('open-expt-overlay-1');
// var deactivators = document.getElementsByClassName('expt-close-1');
// document.addEventListener('click', function(e) {
//   if (e.target.className === 'js-activator') {
//     activate();
//   } else if (e.target.className === 'js-deactivator') {
//     deactivate();
//   }
// });
// function activate() {
//   noScroll.on();
//   for (var i = 0, l = activators.length; i < l; i++) {
//     activators[i].disabled = true;
//   }
//   for (var j = 0, k = deactivators.length; j < k; j++) {
//     deactivators[j].disabled = false;
//   }
// }
// function deactivate() {
//   noScroll.off();
//   for (var i = 0, l = activators.length; i < l; i++) {
//     activators[i].disabled = false;
//   }
//   for (var j = 0, k = deactivators.length; j < k; j++) {
//     deactivators[j].disabled = true;
//   }
// }