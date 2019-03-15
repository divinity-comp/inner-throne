function showdivohbother(box) {
	var chboxs = document.getElementsByName("bbptoolkit-rem-ohbother");
	var vis = "block";
	for(var i=0;i<chboxs.length;i++) {
	  if(chboxs[i].checked){
		vis = "none";
		break;
	  }
	}
	document.getElementById(box).style.display = vis;
}

function showdivifchecked(divname,elname) {
	var chboxs = document.getElementsByName(elname);
	var vis = "none";
	for(var i=0;i<chboxs.length;i++) {
	  if(chboxs[i].checked){
		vis = "block";
		break;
	  }
	}
	document.getElementById(divname).style.display = vis;
}

function hidedivifchecked(divname,elname) {
	var chboxs = document.getElementsByName(elname);
	var vis = "block";
	for(var i=0;i<chboxs.length;i++) {
	  if(chboxs[i].checked){
		vis = "none";
		break;
	  }
	}
	document.getElementById(divname).style.display = vis;
}

jQuery(document).ready(function(){
    jQuery(".tabs").hide();
    jQuery(".tab-buttons").click(function(e){
         e.preventDefault();
        var showIt =  jQuery(this).attr('id');
        jQuery(".tabs").hide();
        jQuery("#choosetab").hide();
        jQuery("#tab"+showIt).show();           
    })
})