if (document.layers) {
  document.captureEvents(Event.KEYPRESS);
}

function process_keypress(e)
{
  if(window.event) {
    if (window.event.type == "keypress" & window.event.keyCode == 13) {
      return !(window.event.type == "keypress" & window.event.keyCode == 13);
    }
  }
  
  if(e) {
    if (e.type == "keypress" & e.keyCode == 13) {
      return !e;
    }
  }
}

//document.onkeypress = process_keypress;