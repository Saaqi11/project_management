
$(document).on('click', '#chooseAPlan', function () {
    commonAjax('GET', $('#chooseAPanRoute').val(), setPlanModalData, setPlanModalData);
});

function setPlanModalData(response) {
    var selector = $('#choosePlanModal')
    selector.modal('show');
    selector.find('#planListBlock').html(response.responseText);
}

var requestCurrentPlan = $('#requestCurrentPlan').val();
if (requestCurrentPlan == 'no') {
    $('#chooseAPlan').trigger('click');
}

$(document).on('change', '#monthly-yearly-button', function () {
    if ($(this).is(':checked') == true) {
        $(document).find('.price-yearly').removeClass('d-none');
        $(document).find('.price-monthly').addClass('d-none');
        $(document).find('.plan_type').val(2);
    }
    else {
        $(document).find('.price-yearly').addClass('d-none');
        $(document).find('.price-monthly').removeClass('d-none');
        $(document).find('.plan_type').val(1);
    }
});

window.addEventListener('load', function () {
    if ($('#requestPlanId').val()) {
        let response = { 'responseText': $('#gatewayResponse').val() };
        setPaymentModal(response)
    }
})

function setPaymentModal(response) {
    var selector = $('#paymentMethodModal')
    selector.modal('show');
    $('#choosePlanModal').modal('hide');
    selector.find('#gatewayListBlock').html(response.responseText);
}

$(document).on('click', '.paymentGateway', function (e) {
    e.preventDefault();

    $(this).closest('#gatewaySection').find('button').removeClass('active')
    $(this).closest('#gatewaySection').find('.payment-method-item').removeClass('border border-primary')
    $(this).parent().addClass('border border-primary')
    $(this).addClass('active')
    var selectGateway = $(this).data('gateway').replace(/\s+/g, '');
    $('#selectGateway').val(selectGateway)
    $('#selectCurrency').val('');
    $('#plan_id').val($(this).data('plan_id'));
    $('#duration_type').val($(this).data('duration_type'));
    commonAjax('GET', $('#getCurrencyByGatewayRoute').val(), getCurrencyRes, getCurrencyRes, { 'id': $(this).data('id') });
    if (selectGateway == 'bank') {
        $('#bankAppend').removeClass('d-none');
        $('#bank_slip').attr('required', true);
        $('#bank_id').attr('required', true);
    } else {
        $('#bank_slip').attr('required', false);
        $('#bank_id').attr('required', false);
        $('#bankAppend').addClass('d-none');
    }
});

function getCurrencyRes(response) {
    var html = '';
    var planAmount = parseFloat($('#planAmount').val()).toFixed(2);
    Object.entries(response.data).forEach((currency) => {
        let currencyAmount = currency[1].conversion_rate * planAmount;
        html += `<tr>
                <td>
                    <div class="custom-radiobox gatewayCurrencyAmount">
                        <input type="radio" name="gateway_currency_amount" id="${currency[1].id}" class="" value="${gatewayCurrencyPrice(currencyAmount, currency[1].symbol)}">
                        <label for="${currency[1].id}">${currency[1].currency}</label>
                    </div>
                </td>
                <td><h6 class="tenant-invoice-tbl-right-text text-end">${gatewayCurrencyPrice(planAmount)} * ${currency[1].conversion_rate} = ${gatewayCurrencyPrice(currencyAmount, currency[1].symbol)}</h6></td>
            </tr>`;
    });
    $('#currencyAppend').html(html);
}

$(document).on('click', '.gatewayCurrencyAmount', function () {
    var getCurrencyAmount = '(' + $(this).find('input').val() + ')';
    $('#gatewayCurrencyAmount').text(getCurrencyAmount)
    $('#selectCurrency').val($(this).text().replace(/\s+/g, ''));
});

$(document).on('change', '#bank_id', function () {
    $('#bankDetails').removeClass('d-none');
    $('#bankDetails p').html($(this).find(':selected').data('details'));
});

$('#payBtn').on('click', function () {
    var gateway = $('#selectGateway').val()
    var currency = $('#selectCurrency').val();
    if (gateway == '') {
        toastr.error('Select Gateway');
        $('#payBtn').attr('type', 'button');
    } else {
        if (currency == '') {
            toastr.error('Select Currency');
            $('#payBtn').attr('type', 'button');
        } else {
            $('#payBtn').attr('type', 'submit');
        }
    }
});
