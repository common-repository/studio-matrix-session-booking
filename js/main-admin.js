jQuery(document).ready(function ($) {
    var $timeDivBack = jQuery('#timeDivBack');
    var $messageAlert = jQuery('#sm-message-alert');
    var $body = jQuery('body');
    $('.slotPagination').show();
    $('.logPagination').show();
    $('.manualPagination').show();

    jQuery('#sessionDatePicker').datepicker({
        dateFormat: 'dd-mm-yy',
        minDate: 0,

        onSelect: function (newText) {
            $timeDivBack.empty();
            var datePicked = newText;
            if (datePicked !== '') {
                // noinspection JSUnresolvedVariable
                $.post(stmsb_php_vars.ajaxUrl, {'action': 'showTime', 'datePicked': datePicked}, function (response) {
                    $timeDivBack.append(response);
                });
            }
        },
        beforeShowDay: function (date) {
            var availableDates = JSON.parse(unbookedDates);
            var days = date.getDate();
            if (availableDates.indexOf(days) >= 0) {
                return [true];
            } else {
                return [false];
            }
        }
    });

    function confirmDelete() {
        return confirm('Once deleted cannot be recovered !!');
    }

    var $datetimepicker12 = jQuery('#datetimepicker12');
    var $datetimepicker2 = jQuery('#datetimepicker2');
    $datetimepicker12.datetimepicker({
        format: 'DD-MM-YYYY',
        inline: true,
        minDate: new Date()
    });
    $datetimepicker2.datetimepicker({
        format: 'hh:mm A',
        inline: true,
        stepping: 15
    });


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Delete buttons
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Session Slots
    $body.on('click', '.btnSlotDelete', function () {
        var confirm = confirmDelete();
        if (confirm) {
            var id = $(this).data('id');
            // noinspection JSUnresolvedVariable
            $.post(stmsb_php_vars.ajaxUrl, {
                'action': 'stmsb_delete_session_slot',
                'deleteid': id
            }, function (response) {
                if (response) {
                    $messageAlert.append('<div class="notice notice-error is-dismissible"><p>Cannot Delete the session slot.</p></div>');
                } else {
                    $messageAlert.append('<div class="notice notice-success is-dismissible"><p>Session slot has been deleted</p></div>');
                    window.setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }
            });
        }
    });

    $("#updateSessionForm").validate({
        rules: {
            sessionTypeDuration: {
                required: true,
                digits: true,
                maxlength: 3
            },
            sessionTypeName: {
                required: true
            },
            sessionTypeProduct: {
                required: {
                    depends: function () {
                        return $("#sessionTypeProduct").val() === "";
                    }
                }
            }
        },
        messages: {
            sessionTypeDuration: {
                required: "The selected field is required",
                digits: "The field must be numbers",
                maxlength: "The field must have at most 3 digits"
            }
        }
    });

    $("#bookSessionDate").validate({
        rules: {
            sessionDates: {
                required: true,
                date : true

            },
            startTime: {
                required: true
            }
        },
        messages: {
            sessionDates: {
                required: "The selected field is required"
            },
            startTime: {
                required: "The selected field is required"
            }
        }
    });

    $("#stmsb_option_form").validate({
        rules: {
            stmsb_display_rows: {
                required: true,
                digits: true
            },
            stmsb_display_date_format:{
                required: true
            },
            stmsb_display_time_format:{
                required: true
            }

        },
        messages: {
            stmsb_display_rows: {
                required: "The selected field is required",
                digits: "The field must contain numbers"
            },
            stmsb_display_date_format:{
                required: null
            },
            stmsb_display_time_format:{
                required: null
            }
        }
    });
});