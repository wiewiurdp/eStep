var $rec = $('.reccurence'), $fin = $('.recurrenceFinishedOn');
$rec.change(function () {
    if ($rec.val() !== '0') {
        $fin.removeAttr('disabled');
    } else {
        $fin.prop('disabled', true);
    }
}).trigger('change');

var instance = $('#list').pickList({
    data: data,
});

var picklist = instance.data('picklist');
$('#batches').change(function () {
    var available = $(".available").find('select');
    var batchId = $(this).children(":selected").val();
    var url = Routing.generate('get_attendees_by_batch', {batchId: batchId});
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function (node) {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var attendees = JSON.parse(xhr.response);
                available.empty();
                picklist.available.length = [];
                loop1:
                    for (var i = 0; i < attendees.length; i++) {
                        var attendee = {};
                        attendee.id = attendees[i].id;
                        attendee.label = attendees[i].name;
                        if (attendees[i].role) {
                            attendee.label = attendees[i].role + ' - ' + attendee.label
                        }
                        for (var j = 0; j < picklist.selected.length; j++) {
                            if (attendee.id == picklist.selected[j].id) {
                                continue loop1
                            }
                        }
                        $('<option>').val(attendee.id).text(attendee.label).data('item', attendee).appendTo(available);
                        picklist.available.push({id: attendee.id, label: attendee.label});
                    }

            }
        }
    };
    xhr.send();
});

$('#button').on('click', function () {
    var selected = JSON.stringify(instance.pickList('getSelected'));
    $('#booking_attendeesJSON').attr('value', selected).appendTo()
});
