var $=jQuery.noConflict();
function bmpinserthtml(html, field) { 
var obj = document.getElementById(field);
    try { 
        if (obj.selectionStart || obj.selectionStart === 0) {
            obj.focus();
            var os = obj.selectionStart;
            var oe = obj.selectionEnd;
            var np = os + html.length;
            obj.value = obj.value.substring(0, os) + html + obj.value.substring(oe, obj.value.length);
            o.setSelectionRange(np, np);
        } else if (document.selection) {
            obj.focus();
            sel = document.selection.createRange();
            sel.text = html;
        } else {
            obj.value += html;
        }
        
    } catch (e) {
    }
}

function bmpdisplayNone(){
    $('.popup ul').css("display", "none");
}

$(document).ready(function(){

      $("#nav>li>a").hover(function(){
      $('#nav ul').css("display", "block");
    });

    $("#nav ul").hover(function(){
      $('#nav ul').css("display", "block");
    },function(){$('#a').css("display", "none");});
    
    $("#nav1>li>a").hover(function(){
      $('#nav1 ul').css("display", "block");
    });
    $("#nav1 ul").hover(function(){
      $('#nav1 ul').css("display", "block");
    },function(){$('#b').css("display", "none");});

    $("#nav2>li>a").hover(function(){
      $('#nav2 ul').css("display", "block");
    });
    $("#nav2 ul").hover(function(){
      $('#nav2 ul').css("display", "block");
    },function(){$('#c').css("display", "none");});

    $("#nav3>li>a").hover(function(){
      $('#nav3 ul').css("display", "block");
    });
    $("#nav3 ul").hover(function(){
      $('#nav3 ul').css("display", "block");
    },function(){$('#d').css("display", "none");});


    $('.wrap_style').css('height',$('.content_style').height());
});
function reset_data(link)
{
  $.ajax({
      type: 'POST',
      url: link,
      data: {
          'action': 'bmp_admin_reset_data',
      },
      success: function (data) {
        
       alert("Binary MLM Data Successfully Reset");
          
        }
    
  });

}