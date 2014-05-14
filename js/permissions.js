var permissions = {
    // All permissions to reference for iteration
    allPermissions: {
      'createlog': false,
      'editlog': false,
      'viewlog': false,
      
      'addcat': false,
      'editcat': false,
      'deletecat': false,
      
      'adduser': false,
      'edituser': false,
      'deleteuser': false,
      
      'addgroup': false,
      'editgroup': false,
      'deletegroup': false,
      
      'viewcheesto': false,
      'updatecheesto': false,
      
      'admin': false,
    },
    
    currentPermissions: {
    },
    
    getList: function() {
        $("#groups").load("scripts/editgroups.php", "action=getlist");
    },
    
    getPermissions: function(gid) {
        var group = typeof gid !== 'undefined' ? gid : $("#groupList")[0].value;
        
        $.get("scripts/editgroups.php", { action: "getpermissions", groups: group })
          .done(function( html ) {
              permissions.showPermissions(html);
          });
    },
    
    showPermissions: function(json) {
        var permissionsObject = JSON.parse(json);
        this.currentPermissions = permissionsObject;
        
        this.drawGrid(permissionsObject);
        
        $("#permissionsBlock").css( "display", "block" );
        
        return true;
    },
    
    savePermissions: function() {
        // Copy allPermissions
        var newPermissions = this.getGrid();
        var group = $("#groupList").val();
        
        if (group !== 0) {
			var newPermissionsJson = JSON.stringify(newPermissions);

			$.post("scripts/editgroups.php", { action: "save", permissions: newPermissionsJson, gid: group })
				.done(function( msg ) {
					alert(msg);
					$("#permissionsForm")[0].reset();
					$("#permissionsBlock").css( "display", "none" );
					$("#groupList").val( 0 );
				});
        }
    },
    
    checkGrid: function(permission) {
		var newGrid = this.allPermissions;
		var theID = "#"+permission;
		var changed;
		
		if (permission == 'admin') {
			if ($(theID).is( ":checked" )) {
				// An admin user has full permissions, basically a select all
				for (var key in newGrid) {
					if (newGrid.hasOwnProperty(key)) {
						newGrid[key] = true;
					}
				}
				
				this.drawGrid(newGrid);
			}
			else {
				// Revert to original permissions grid
				changed = this.currentPermissions;
				changed[permission] = false;
				this.drawGrid(changed);
			}
		}
		
		else if (permission == 'createlog' || permission == 'editlog') {
			// Creating and editing a log inherently means the user can view logs
			if ($(theID).is( ":checked" )) {
				changed = this.getGrid();
				changed.viewlog = true;
				changed[permission] = true;
				this.drawGrid(changed);
			}
		}
		
		else if (permission == 'updatecheesto') {
			if ($(theID).is( ":checked" )) {
				changed = this.getGrid();
				changed.viewcheesto = true;
				changed[permission] = true;
				this.drawGrid(changed);
			}
		}
    },
    
    goBack: function() {
		this.drawGrid(this.currentPermissions);
    },
    
    createNew: function() {
        $( "#add-form" ).dialog({
          height: 225,
          width: 350,
          modal: true,
          buttons: {
            "Create Group": function() {
                permissions.sendNew();
                $( this ).dialog( "close" );
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
        });
    },
    
    sendNew: function() {
        $.post("scripts/editgroups.php",
			{ action: "create", name: $("#name").val(), rights: JSON.stringify(permissions.allPermissions) }
          )
          .done(function( msg ) {
              
              $( "#dialog" ).html( "<p>Rights Group Created Successfully</p>" );
              $( "#dialog" ).dialog({
                  modal: true,
                  width: 400,
                  show: {
                    effect: "fade",
                    duration: 500
                  },
                  hide: {
                    effect: "fade",
                    duration: 500
                  },
                  buttons: {
                    Ok: function() {
                      $( this ).dialog( "close" );
                    }
                  }
                });
              
                $("#permissionsForm")[0].reset();
                $("#permissionsBlock").css( "display", "none" );
                permissions.getList();
          });
    },
    
    deleteGroup: function() {
        var delGroup = $("#groupList option:selected").text();
        
        $( "#dialog" ).html( "<p>Do you really want to delete the group '"+delGroup+"'?</p>" );
        $( "#dialog" ).dialog({
          resizable: false,
          modal: true,
          buttons: {
            "Delete Group": function() {
                $( this ).dialog( "close" );
              
                var group = $("#groupList").val();
            
                $.post("scripts/editgroups.php", { action: "delete", groups: group })
                  .done(function( msg ) {
                      
                      $( "#dialog" ).html( "<p>"+msg+"</p>" );
                      $( "#dialog" ).dialog({
                          modal: true,
                          width: 400,
                          show: {
                            effect: "fade",
                            duration: 500
                          },
                          hide: {
                            effect: "fade",
                            duration: 500
                          },
                          buttons: {
                            Ok: function() {
                              $( this ).dialog( "close" );
                            }
                          }
                        });
                        
						$("#permissionsForm")[0].reset();
						$("#permissionsBlock").css( "display", "none" );
                        permissions.getList();
                  });
            },
            Cancel: function() {
              $( this ).dialog( "close" );
            }
          }
        });
    },
    
    drawGrid: function(object) {
		for (var key in object) {
           if (object.hasOwnProperty(key)) {
              var getID = "#"+key;

              $(getID).prop( "checked", object[key] );
           }
        }
        
        return true;
    },
    
    getGrid: function() {
		var theGrid = JSON.parse(JSON.stringify(this.allPermissions));

		for (var key in theGrid) {
			if (theGrid.hasOwnProperty(key)) {
				var getID = "#"+key;

				theGrid[key] = $(getID).prop( "checked" );
			}
		}
		
		return theGrid;
    },
    
    // Checks if enter key was pressed, if so submit the form
    check: function(e) {
        if (e.keyCode == 13) {
            $( "#add-form" ).dialog( "close" );
            this.sendNew();
            e.preventDefault();
        }
    },
};