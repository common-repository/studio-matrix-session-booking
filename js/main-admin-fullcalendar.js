jQuery(document).ready(function ($) {
    // Get the modal
    var modal = document.getElementById('myModal');
    var span = document.getElementsByClassName("close")[0];
    // noinspection JSJQueryEfficiency
    var $sessionTableBody = $("#sessionList tbody");

    $('.calendar').fullCalendar({
        header: {
            left: 'today ',
            center: 'prev title next ',
            right: 'agendaDay,agendaWeek,month'
        },
        theme: true,
        themeSystem: 'bootstrap3',
        dayRender: function (date, cell) {
            cell.css("background-color", "#89a7f9");
        },
        weekends: true,
        events: toShowDates,
        timeFormat: 'h:mm A',
        eventRender: function (event, element) {
            if (event.title === 'No Booking') {
                $(element).css("background-color", "#d9534f");
                $(element).css("border-color", "#d9534f");
            }
            else {
                $(element).css("background-color", "#138808");
                $(element).css("border-color", "##138808");
            }

        },
        eventClick: function (event) {
            $sessionTableBody.empty();
            modal.style.display = "block";
            // noinspection JSUnresolvedVariable
            var rowHtml =
                '<tr>\
                <td>' + event.title + '</td>\
                <td>' + event.type + '</td>\
                <td>' + event.from + '</td>\
                <td>' + moment(event.start).format(stmsb_php_vars.displayDateFormat) + '</td>\
                <td>' + moment(event.start).format(stmsb_php_vars.displayTimeFormat) + '</td>\
                <td>' + moment(event.end).format(stmsb_php_vars.displayTimeFormat) + '</td>';
            rowHtml += '</tr>';
            $sessionTableBody.append($(rowHtml));
        }
    });
    $('body').on('click', '.myBtn', function () {
        var id = $(this).data('id');
        // noinspection JSUnresolvedVariable
        window.location.href = stmsb_php_vars.changeBookingUrl + '&toChangeId=' + id;
    });

    span.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
});

