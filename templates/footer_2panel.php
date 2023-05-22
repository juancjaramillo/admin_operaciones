<script>
 
(function() {
 var Dom = YAHOO.util.Dom,
 Event = YAHOO.util.Event;
    
 Event.onDOMReady(function() {
    var layout = new YAHOO.widget.Layout({
    units: [
      { position: 'right', width: 250, resize: true, collapse: true, scroll: true, body: 'right1', animate: true },
      { position: 'center', body: 'center1', scroll: true }
    ]
    });
    layout.render();
 });
})();

</script>
</body>
</html>
