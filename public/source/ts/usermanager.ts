/// <reference path="../dts/jquery.d.ts" />
/// <reference path="../dts/jqueryui.d.ts" />
/// <reference path="../dts/common.d.ts" />
/// <reference path="../dts/datetimepicker.d.ts" />
/* global document, $, setTimeout, location */

"use strict"; // jshint ignore:line

var UserManage = {
    init: function(): void {
        var disableBtn = $("#disable-user-btn");
        if (disableBtn.length) {
            disableBtn.click(UserManage.disableUser);
        } else {
            $("#enable-user-btn").click(UserManage.enableUser);
        }
        $("#delete-user-btn").click(UserManage.confirmDeleteUser);
        $("#reset-pwd-btn").click(UserManage.showPasswordDialog);
        $("#revoke-api-btn").click(UserManage.confirmRevokeKey);
        $("#save-btn").click(UserManage.saveUser);
        $("#user-status").change(UserManage.checkStatus);
        $("#user-status-return").datetimepicker({
            timeFormat: "HH:mm",
            controlType: "select",
            stepMinute: 10,
        });
    },

    enableUser: function(): void {
        var userId = $("#user-id").val();
        $.post("../../api/i/users/enable", {uid: userId}, null, "json")
            .done(function(data) {
                if ($.apiSuccess(data)) {
                    $.alert("User enabled", "User Management", function() {
                        location.reload(true);
                    });
                } else {
                    $.alert("Error enabling user", "User Management");
                }
            });
    },

    disableUser: function(): void {
        var userId = $("#user-id").val();
        $.post("../../api/i/users/disable", {uid: userId}, null, "json")
            .done(function(data) {
                if ($.apiSuccess(data)) {
                    $.alert("User disabled", "User Management", function() {
                        location.reload(true);
                    });
                } else {
                    $.alert("Error disabling user", "User Management");
                }
            });
    },

    confirmDeleteUser: function(): void {
        $.confirmBox("Disabling a user is prefered over deletion.<br><br>Are you sure you want to delete this user?",
            "Delete User",
            UserManage.deleteUser
        );
    },

    deleteUser: function(): void {
        $.post("../../api/i/users/delete", {uid: $("#user-id").val()}, null, "json")
            .done(function(data) {
                if ($.apiSuccess(data)) {
                    $.alert("User deleted successfully", "User Management", function() {
                        location.assign("../../admin");
                    });
                } else {
                    $.alert("Error deleting user", "User Management");
                }
            });
        return;
    },

    showPasswordDialog: function(): void {
        $("#pwd-reset-dialog").dialog({
            modal: true,
            width: 400,
            height: 250,
            show: {
                effect: "fade",
                duration: 500
            },
            hide: {
                effect: "fade",
                duration: 250
            },
            buttons: [
                {
                    text: "Reset",
                    click: function() {
                        $(this).dialog("close");
                        UserManage.resetPassword();
                    }
                },
                {
                    text: "Cancel",
                    click: function() {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    },

    resetPassword: function(): void {
        var uid: number = $("#user-id").val();
        var pass1: string = $("#pass1").val();
        var pass2: string = $("#pass2").val();
        var force_reset: boolean = $("#force-reset-chk").prop("checked");
        $("#pass1").val("");
        $("#pass2").val("");

        if (pass1 === pass2 && pass1 !== "") {
            $.post("../../api/i/users/resetpassword", {pw: pass1, uid: uid, force_reset: force_reset}, null, "json")
                .done(function(data) {
                    $.alert(data.data, "User Management");
                });
        } else {
            $.alert("Passwords do not match or are empty", "User Management");
        }
        return;
    },

    confirmRevokeKey: function(): void {
        $.confirmBox("Are you sure you want to revoke the API key?",
            "API Key Revoke",
            UserManage.revokeKey
        );
    },

    revokeKey: function(): void {
        $.post("../../api/i/key/revoke", {uid: $("#user-id").val()}, null, "json")
            .done(function(data) {
                if ($.apiSuccess(data)) {
                    $.alert("API key revoked", "User Management");
                } else {
                    $.alert("Error revoking API key", "User Management");
                }
            });
        return;
    },

    saveUser: function(): void {
        var userid: string = $("#user-id").val();
        var fullname: string = $("#fullname").val();
        var group: string = $("#user-group").val();
        var status: string = $("#user-status").val();
        var message: string = $("#user-status-message").val();
        var returntime: string = $("#user-status-return").val();

        $.post("../../api/i/users/edit", {uid: userid, fullname: fullname, role: group}, null, "json")
            .done(function(response) {
                if ($.apiSuccess(response)) {
                    $.post("../../api/i/cheesto/update", {uid: userid, message: message, status: status, returntime: returntime}, null, "json")
                        .done(function(response) {
                            if ($.apiSuccess(response)) {
                                $.flashMessage("User saved");
                            } else {
                                $.flashMessage("Error saving user");
                            }
                        });
                } else {
                    $.flashMessage("Error saving user");
                }
            });
    },

    checkStatus: function(): void {
        var status: string = $("#user-status").val();

        if (status === "Available") {
            $("#user-status-message").val("");
            $("#user-status-return").val("00:00:00");
        }
    }
};

(function() {
    UserManage.init();
})();
