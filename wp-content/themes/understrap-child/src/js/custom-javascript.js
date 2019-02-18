3-open-close.js
window.addEventListener("load", function(){
  var open = document.getElementById("open-expt-overlay-1"),
      close = document.getElementById("expt-close-1"),
      over = document.getElementById("expt-overlay-1");
      body = document.body;
    
  open.addEventListener("click", function(){
    over.classList.add("show");
   body.classList.add("nope");
  });
  close.addEventListener("click", function(){
    over.classList.remove("show");
    body.classList.remove("nope");
  });
});

3-open-close.js
window.addEventListener("load", function(){
  var open = document.getElementById("open-expt-overlay-2"),
      close = document.getElementById("expt-close-2"),
      over = document.getElementById("expt-overlay-2");

  open.addEventListener("click", function(){
    over.classList.add("show");
   body.classList.add("nope");
  });
  close.addEventListener("click", function(){
    over.classList.remove("show");
    body.classList.remove("nope");
  });
});

3-open-close.js
window.addEventListener("load", function(){
  var open = document.getElementById("open-expt-overlay-3"),
      close = document.getElementById("expt-close-3"),
      over = document.getElementById("expt-overlay-3");

  open.addEventListener("click", function(){
    over.classList.add("show");
   body.classList.add("nope");
  });
  close.addEventListener("click", function(){
    over.classList.remove("show");
    body.classList.remove("nope");
  });
});

3-open-close.js
window.addEventListener("load", function(){
  var open = document.getElementById("open-expt-overlay-4"),
      close = document.getElementById("expt-close-4"),
      over = document.getElementById("expt-overlay-4");

  open.addEventListener("click", function(){
    over.classList.add("show");
   body.classList.add("nope");
  });
  close.addEventListener("click", function(){
    over.classList.remove("show");
    body.classList.remove("nope");
  });
});

3-open-close.js
window.addEventListener("load", function(){
  var open = document.getElementById("open-expt-overlay-5"),
      close = document.getElementById("expt-close-5"),
      over = document.getElementById("expt-overlay-5");

  open.addEventListener("click", function(){
    over.classList.add("show");
   body.classList.add("nope");
  });
  close.addEventListener("click", function(){
    over.classList.remove("show");
    body.classList.remove("nope");
  });
});


var lastTop;

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



var $el, $ps, $up, totalHeight;

$(".backstory__content .button").click(function() {
      
  totalHeight = 0

  $el = $(this);
  $p  = $el.parent();
  $up = $p.parent();
  $ps = $up.find("p:not('.read-more')");
  
  // measure how tall inside should be by adding together heights of all inside paragraphs (except read-more paragraph)
  $ps.each(function() {
    totalHeight += $(this).outerHeight();
  });
        
  $up
    .css({
      // Set height to prevent instant jumpdown when max height is removed
      "height": $up.height(),
      "max-height": 9999
    })
    .animate({
      "height": totalHeight
    });
  
  // fade out read-more
  $p.fadeOut();
  
  // prevent jump-down
  return false;
    
}); 

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