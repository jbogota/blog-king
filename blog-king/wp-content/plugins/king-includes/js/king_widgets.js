$(document).ready(function () {

	$('ul').Sortable(
		{	accept : 		'module',
			activeclass : 	'sortableactive',
			hoverclass : 	'sortablehover',
			helperclass : 	'sorthelper',
			onStop: 		function(){	serialize();},
			revert:			true,
			tolerance:		'intersect'
		} );

	$("form div ul li a.opendiv").click( function() {

		var popup =$('#'+$(this).title()+'control' );
		popup.css({	position:'absolute',
					border:'1px solid #BBB',
					background:'#fff',
					left:'350px',
					display: 'block'});

		popup.prepend('<span class="controlhandle">'+$(this).title()+'</span><span class="controlcloser">×</span>');

        popup.Draggable({handle:'.controlhandle',
						autoSize:false });

		popup.tabs();
		$("form div span.controlcloser").click( function() {
			var container = $(this).parent();
			container.hide();
			container.children('span.controlhandle').remove();
			container.children('span.controlcloser').remove();
			container.each('li.on').removeClass("on");

			//container.children('ul.anchors li.on').removeClass("on");
		});

	});

	$('label').Tooltip(350);

});

function serialize(s)
{
	var widget_order = $.SortSerialize(s);
	$("#widget_order").val(widget_order.hash);
}
