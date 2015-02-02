/* global $, window, alert */

"use strict"; // jshint ignore:line

var CategoryManage = {
	currentID: -1,
	currentSelection: [],
	addEditLog: false,

	grabNextLevel: function(parentID, container) {
	    var pid;
		if (parentID.value) {
			pid = parentID.value;
		} else {
			pid = parentID;
		}

		container = (this.addEditLog) ? '#catSpace' : '#categorySelects';

		var level = pid.split(':');

		if (this.currentSelection[level[1]]) {
			this.currentSelection.splice(level[1]);
		}

		this.currentSelection[level[1]] = pid;

        if (this.currentSelection.length === 0) {
			this.currentSelection[0] = '0:0';
		}

		$.ajax({
            type: "POST",
            url: "render/categories",
            data: { action: "grabcats", pastSelections: JSON.stringify(this.currentSelection)},
            async: false
        })
            .done(function( html ) {
                if (typeof $("#categorySelects")[0] !== 'undefined') {
                    $("#categorySelects").html("");
                    $(container).html( html );
                    CategoryManage.currentID = pid;
                }
            });
	},

	renderCategoriesFromString: function(str, callback) {
		$.get('render/editcat', {catstring: str})
			.done(function(html) {
				callback(html);
			});
	},

	createNew: function() {
		var catString = this.getCatString();
		var message = 'Add new category\n\n';

		if (this.currentSelection.length == 1) {
			message = 'Create new root category:';
			catString = '';
		}

		while (true) {
			var newCatDesc = window.prompt(message+catString);

			if (newCatDesc === '') {
				alert('Please enter a category description');
			}
			else if (newCatDesc === null) {
				return false;
			}
			else {
				this.addNew(encodeURIComponent(newCatDesc));
				break;
			}
		}
	},

	addNew: function(catDesc) {
		var newCatDesc = catDesc;
		var parent = this.currentSelection[this.currentSelection.length-1].split(':');
		parent = parent[0];

		$.post("api/i/categories/add", { parentID: parent, catDesc: newCatDesc }, null, 'json')
            .done(function( json ) {
                alert(json.data);
				CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
            });
	},

	editCat: function() {
		var cid = this.currentSelection[this.currentSelection.length-1].split(':');

		var elt = $("#level"+cid[1]+" option:selected");

		if (typeof elt.val() !== 'undefined') {
			var editString = elt.text();
            var editedCat = window.prompt("Edit Category Description:",editString);

            if (editedCat !== null && editedCat !== '') {
                $.post("api/i/categories/edit", { cid: cid[0], catDesc: encodeURIComponent(editedCat) }, null, 'json')
                    .done(function( json ) {
                        alert(json.data);
                        CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
                    });
            }
		}
	},

	deleteCat: function() {
		var myCatString = this.getCatString();
		var cid = this.currentSelection[this.currentSelection.length-1].split(':');
		cid = cid[0];

		if (!window.confirm('Delete '+ myCatString +'?\n\nWarning: All child categories will be moved up one level!')) {
			return false;
		}

        $.post("api/i/categories/delete", { cid: cid }, null ,'json')
            .done(function( json ) {
                alert(json.data);
                CategoryManage.grabNextLevel(CategoryManage.currentSelection[CategoryManage.currentSelection.length-2]);
            });
	},

	getCatString: function() {
		var catString = '';

		for (var i=0; i<this.currentSelection.length; i++) {
			if ($("#level"+(i+1))) {
				var elt = $("#level"+(i+1)+" option:selected");

				if (elt.text() != 'Select:') {
					catString += elt.text() + ':';
				}
			}
		}

		if (catString.length > 0) {
			return catString.substring(0, catString.length - 1);
		}
		else {
			return false;
		}
	},
};