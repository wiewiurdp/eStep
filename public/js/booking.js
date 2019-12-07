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
    var url = Routing.generate('get_users_by_batch', {batchId: batchId});
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function (node) {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var users = JSON.parse(xhr.response);
                available.empty();
                picklist.available.length = [];
                loop1:
                    for (var i = 0; i < users.length; i++) {
                        var user = {};
                        user.id = users[i].id;
                        user.label = users[i].name;
                        if (users[i].role) {
                            user.label = users[i].role + ' - ' + user.label
                        }
                        for (var j = 0; j < picklist.selected.length; j++) {
                            if (user.id == picklist.selected[j].id) {
                                continue loop1
                            }
                        }
                        $('<option>').val(user.id).text(user.label).data('item', user).appendTo(available);
                        picklist.available.push({id: user.id, label: user.label});
                    }

            }
        }
    };
    xhr.send();
});

$('#button').on('click', function () {
    var selected = JSON.stringify(instance.pickList('getSelected'));
    $('#booking_usersJSON').attr('value', selected).appendTo()
});
