var adminAction = {
	editSlogan: function(current) {
		var newSlogan = window.prompt('Website Slogan', current);
		
		if (newSlogan != '' && newSlogan != null) {
			params = new Object;
			
			params.address = "scripts/admin_actions.php";
			params.data = 'sub_action=slogan&slogan=' + encodeURIComponent(newSlogan);
			params.success = function() { };
			
			_.ajax(params);
		}
	},
	
	backupDB: function(current) {
		params = new Object;
		
		params.address = "scripts/admin_actions.php";
		params.data = 'sub_action=backupdb';
		params.success = function() { alert(responseText); };
		
		_.ajax(params);
	},
};