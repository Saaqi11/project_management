$(document).on('click', '.add', function () {
    var selector = $('#addMaintainerModal')
    selector.find('.is-invalid').removeClass('is-invalid');
    selector.find('.error-message').remove();
    selector.modal('show');
    selector.find('#addMaintainerModalLabel').html('Add Maintainer');
    selector.find('form').trigger("reset");
    selector.find('#id').val('');
});

$(document).on('click', '.edit', function () {
    commonAjax('GET', $('#getInfoRoute').val(), getDataEditRes, getDataEditRes, { 'id': $(this).data('id') });
});

function getDataEditRes(response) {
    var selector = $('#addMaintainerModal')
    selector.find('.is-invalid').removeClass('is-invalid');
    selector.find('.error-message').remove();
    selector.modal('show');
    selector.find('#addMaintainerModalLabel').html('Edit Maintainer')
    selector.find('#id').val(response.data.id)
    selector.find('#user_id').val(response.data.user_id)
    selector.find('.image').attr('src', response.data.image)
    selector.find('.property_id').val(response.data.property_id)
    selector.find('.first_name').val(response.data.first_name)
    selector.find('.last_name').val(response.data.last_name)
    selector.find('.email').val(response.data.email)
    selector.find('.contact_number').val(response.data.contact_number)
}

(function ($) {
    "use strict";
    $('#search_property').on('change', function () {
        var oTable = $('#allDatatable').DataTable();
        oTable.search($(this).val()).draw();
    })

    $('#allDatatable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        responsive: true,
        ajax: $('#route').val(),
        order: [1, 'desc'],
        ordering: false,
        autoWidth: false,
        drawCallback: function () {
            $(".dataTables_length select").addClass("form-select form-select-sm");
        },
        language: {
            'paginate': {
                'previous': '<span class="iconify" data-icon="icons8:angle-left"></span>',
                'next': '<span class="iconify" data-icon="icons8:angle-right"></span>'
            }
        },
        columns: [{
            "data": 'DT_RowIndex',
            "name": 'DT_RowIndex',
            orderable: false,
            searchable: false,
        },
        {
            "data": "image",
        },
        {
            "data": "name",
            "name": "users.first_name"
        },
        {
            "data": "name",
            "visible": false,
            "name": "users.last_name"
        },
        {
            "data": "email",
            "name": "users.email"
        },
        {
            "data": "contact_number",
            "name": "users.contact_number"
        },
        {
            "data": "property",
            "name": "properties.name"
        },
        {
            "data": "action",
            "class": "text-end",
        },
        ]
    });
})(jQuery)
