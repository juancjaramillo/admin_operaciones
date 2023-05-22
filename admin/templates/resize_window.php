<script type="text/javascript">
	if(document.body.scrollHeight>window.outerHeight)	{	//Si hay scroll en y...
		if(document.body.scrollHeight>window.screen.availHeight){//Si es mayor que el alto disponible...
			window.outerHeight=window.screen.availHeight;
			window.moveTo(window.screenX,window.screen.availTop);
		}
		else{
			window.outerHeight=document.body.scrollHeight+30;
			window.moveTo(window.screenX,((window.screen.availHeight-window.outerHeight)/2)+window.screen.availTop);
		}
	}
	if(document.body.scrollWidth>window.outerWidth)	{	//Si hay scroll en x...
		if(document.body.scrollWidth>window.screen.availWidth){//Si es mayor que el ancho disponible...
			window.outerWidth=window.screen.availWidth;
			window.moveTo(window.screen.availLeft,window.screenY);
		}
		else{
			window.outerWidth=document.body.scrollWidth;
			window.moveTo(((window.screen.availWidth-window.outerWidth)/2)+window.screen.availLeft,window.screenY);
		}
	}
</script>

