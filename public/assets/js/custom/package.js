$('#add').on('click', function () {
    var selector = $('#addModal');
    selector.find('.is-invalid').removeClass('is-invalid');
    selector.find('.error-message').remove();
    selector.find('form').trigger('reset');
    selector.modal('show')
})

$(document).on('click', '.edit', function () {
    commonAjax('GET', $('#packageInfoRoute').val(), getDataEditRes, getDataEditRes, { 'id': $(this).data('id') });
});

function getDataEditRes(response) {
    var selector = $('#editModal');
    selector.find('.is-invalid').removeClass('is-invalid');
    selector.find('.error-message').remove();
    selector.find('input[name=id]').val(response.data.id)
    selector.find('input[name=name]').val(response.data.name)
    selector.find('input[name=max_property]').val(response.data.max_property)
    selector.find('input[name=max_unit]').val(response.data.max_unit)
    selector.find('input[name=max_tenant]').val(response.data.max_tenant)
    selector.find('input[name=max_maintainer]').val(response.data.max_maintainer)
    selector.find('input[name=max_invoice]').val(response.data.max_invoice)
    selector.find('input[name=max_auto_invoice]').val(response.data.max_auto_invoice)
    selector.find('select[name=ticket_support]').val(response.data.ticket_support)
    selector.find('select[name=notice_support]').val(response.data.notice_support)
    selector.find('select[name=status]').val(response.data.status)
    selector.find('select[name=is_trail]').val(response.data.is_trail)
    selector.find('select[name=is_default]').val(response.data.is_default)
    selector.find('input[name=monthly_price]').val(response.data.monthly_price)
    selector.find('input[name=yearly_price]').val(response.data.yearly_price)
    selector.modal('show')
}

$('#allDataTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    responsive: true,
    ajax: $('#packageIndexRoute').val(),
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
        "data": "name",
        "name": "name"
    },
    {
        "data": "monthly_price",
        "name": "monthly_price"
    },
    {
        "data": "yearly_price",
        "name": "yearly_price"
    },
    {
        "data": "status",
    },
    {
        "data": "trail",
    },
    {
        "data": "action",
    },
    ]
});
