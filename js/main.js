jQuery(document).ready(function($){
    if( typeof unbookedDates !== 'undefined' ){
        var $FrontDivTime =jQuery('#FrontDivTime');
        var availableDates = JSON.stringify(unbookedDates);
        var x = JSON.parse(availableDates);
        var datesToEnable = [];
        for (var i=0; i<x.length; i++) {
            datesToEnable.push(moment( x[i]*1000).format('l'))  ;
        }

        $('#frontDatePicker').datepicker({
            minDate: 0,
            dateFormat: 'dd-mm-yy',
            maxDate: '+12m',
            onSelect: function (newText){
                $FrontDivTime.empty();
                var datePicked = newText;
                if(datePicked != null){
                    // noinspection JSUnresolvedVariable
                    $.post(stmsb_php_vars.ajaxUrl,{'action':'stmsb_show_front_time', 'datePicked':datePicked, 'currentProductId':currentProductId },function(response){
                        $FrontDivTime.append(response);
                        $FrontDivTime.find('option.withDates').each(
                            function (idx, elem) {
                                var gmtString = $(elem).val();
                                // noinspection JSUnresolvedVariable
                                var gmtDate = moment.utc(gmtString, stmsb_php_vars.storeDateTimeFormat, true);
                                // noinspection JSUnresolvedVariable
                                var localString = gmtDate.local().format(stmsb_php_vars.displayTimeFormat);
                                $(elem).text(localString);
                            });
                    });
                }
            },
            beforeShowDay: disabledAll
        });

        function disabledAll(date) {
            var m = date.getMonth() + 1, d = date.getDate(), y = date.getFullYear();
            for ( var i=0; i < datesToEnable.length; i++ )
            {
                if( $.inArray( m + '/' + d + '/' + y, datesToEnable) !== -1 )
                {
                    return [1];
                }
            }
            return [0];
        }
        $FrontDivTime.change(function(){
            var bookedTime = document.getElementById("FrontDivTime").value;
            $('#selectedDateTime').val(bookedTime);
            $(".single_add_to_cart_button").show();
        })
    }
});