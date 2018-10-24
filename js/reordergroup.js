(function() {

	function ReorderGroup(grouppage, indexes) {
		
		// fonction to do second request to execute follow action
		function ajaxGroupsPageQuery(jsondata) {
			var token = jsondata.query.tokens.csrftoken;
			$("#gp-special-save i.upl_loading").show();
			$("#reordergroup-alert").hide();
			$.ajax({
				type: "POST",
				url: mw.util.wikiScript('api'),
				data: { 
					action:'groupspage_reordergroup', 
					format:'json', 
					token: token, 
					indexes: indexes, 
					groupspage: grouppage
				},
			    dataType: 'json',
			    success: function (jsondata) {
			    	console.log(jsondata);
			    	$("#gp-special-save i.upl_loading").hide();
			    	$("#reordergroup-alert").show();
			    	if(jsondata.groupspage_reordergroup.success){
			    		$("#reordergroup-alert").addClass("alert-success");
			    		$("#reordergroup-alert").html(mw.msg('gp-special-success', mw.config.values.groupspageLink));
			    	}else{
			    		$("#reordergroup-alert").addClass("alert-danger");
			    		$("#reordergroup-alert").html(mw.msg('gp-special-error'));
			    	}
				}
			});
		};
		
		// first request to get token
		$.ajax({
			type: "GET",
			url: mw.util.wikiScript('api'),
			data: { action:'query', format:'json',  meta: 'tokens', type:'csrf'},
		    dataType: 'json',
		    success: ajaxGroupsPageQuery
		});
	}

	$( document ).ready(function() {

		$("#tutorials-list").sortable();

		$('#gp-special-save').click(function() {
			var indexes = $("#tutorials-list").sortable('serialize');

		    var grouppage = $("#tutorials-list").attr('data-grouppage');

		    ReorderGroup(grouppage, indexes);
		});
	});

})();