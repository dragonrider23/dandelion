/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/common.d.ts" />

"use strict"; // jshint ignore:line

var Comments, Logs;

$(document).ready(function(){
    $("#add-comment-btn").click(Comments.showAddForm);
    $("#add-comment-form").submit(Comments.saveComment);
    $("#cancel-new-btn").click(Comments.hideAddForm);
    $("#edit-log-btn").click(Logs.edit);
});

Logs = {
    edit: function(): void {
        var logid: string = $("#logid").val();
        location.assign(`edit/${logid}`);
    },
};

Comments = {
    showAddForm: function(): void {
        $("#newComment").val("");
        $("#add-comment-form").show();
        $("#newComment").focus();
    },

    hideAddForm: function(): void {
        $("#add-comment-form").hide();
    },

    saveComment: function(e): boolean {
        e.preventDefault();
        e.returnValue = false;

        var commentText: string = $("#newComment").val();
        var logid: string = $("#logid").val();

        if (!commentText) {
            $("#error-message").text("Comments can not be blank");
            return false;
        }

        $.post("../api/i/comments/add", {"comment": commentText, "logid": logid}, null, "json")
            .done(function(msg) {
                location.reload();
            });

        return true;
    }
};
