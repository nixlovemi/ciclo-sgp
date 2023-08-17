// Livewire: https://laravel-livewire.com/docs/2.x/reference#global-livewire-js

$(document).ready(function(){
    $(document).on('click', 'div.alert button.btn-close', function(e){
        $(this).parent().fadeOut(500);
    });

    $(document).on('change', 'select.ajax-country', function(e) {
        let country = $(this).val();
        let target = $(this).data('target');
        let targetEl = $(this).closest('form').find(target);

        if (targetEl) {
            $.get(`/api/v1/provincesByCountry`, {'country': country}, function( retData ) {
                if (retData?.data?.provinces) {
                    targetEl.attr('disabled', true);

                    // remove all non-empty options
                    targetEl.find(`option`).each(function(){
                        if ( $(this).val() != '' ) {
                            $(this).remove();
                        }
                    });

                    // add new options
                    $.each(retData?.data?.provinces, function (i, item) {
                        targetEl.append($('<option>', { 
                            value: i,
                            text : item
                        }));
                    });

                    targetEl.attr('disabled', false);
                }
            });
        }
    });

    setTimeout(function(){
        $('div.alert.alert-dismissible').each(function() {
            $(this).find('.btn-close').click();
        });
    }, 12000);

    loadJqueryComponents();
});

$(document).on('click', 'div.modal-dialog .btn-modal-close', function(e) {
    $(this).closest('div.modal').modal('toggle');
});

$(document).on('change', 'form#jobFile-add select[name="jf-tipo"]', function(e) {
    const tipo = $(this).val();
    $('span[data-jf-tipo]').hide();
    $(`span[data-jf-tipo="${tipo}"]`).show();
});

$(document).on('submit', 'form#jobFile-add', function(e) {
    e.preventDefault();
    let FORM = $(this);
    let CSRF = FORM.find('input[name="_token"]').val();

    ajaxSetup(CSRF);
    let formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: FORM.attr('action'),
        data: formData,
        dataType: 'json',
        processData: false, // required for FormData with jQuery
        contentType: false, // required for FormData with jQuery
        beforeSend: function() {
            showLoader();
            disableFormWhileSaving(FORM);
        },
        success: function (retorno) {
            if (retorno.error) {
                showErrorAlert({
                    'title': 'Erro!',
                    'text': retorno.message
                });
                return;
            }

            FORM.find('.btn-modal-close').click();
            showSuccessAlert({
                'title': 'Sucesso!',
                'text': retorno.message
            });
            loadJqueryComponents();
            refreshAllLivewireTables();
        },
        complete: function() {
            closeLoader();
            enableFormWhileSaving(FORM);
        },
        error: function (data) {
            showErrorAlert({
                'title': 'Erro!',
                'text': 'Ocorreu um erro inesperado! Tente novamente.'
            });
            enableFormWhileSaving(FORM)
        }
    });
});

$(document).on('submit', 'form#linkToJob-add', function(e) {
    e.preventDefault();
    let FORM = $(this);
    const SPAN_QUOTE_CARD = $('form#job-register span#job-partials-quoteCard');

    submitModalForm(FORM, function(retorno) {
        FORM.find('.btn-modal-close').click();
        showSuccessAlert({
            'title': 'Sucesso!',
            'text': retorno.message
        });

        SPAN_QUOTE_CARD.html(retorno.data.html);
        loadJqueryComponents();
        setTimeout(function() {
            initLivewireTable();
        }, 250);
    }, null, {
        'disabled': SPAN_QUOTE_CARD.data('disabled')
    });
});

$(document).on('click', 'form#job-register div#job-part-quote-card a.job-link-quote', function(e) {
    const jobCodedId = $(this).data('jid');
    showJsonAjaxModal('GET', '/quote/linkToJobHtml', {'json':1, jobCodedId});
});

$(document).on('click', 'form#job-register #job-part-quote-card a.job-add-quote', function(e) {
    const FORM = $(this).closest('form');
    const SPAN_QUOTE_CARD = $('form#job-register span#job-partials-quoteCard');

    submitModalForm(FORM, function(retorno) {

        showSuccessAlert({
            'title': 'Sucesso!',
            'text': retorno.message
        });
        FORM.find('span#job-partials-quoteCard').html(retorno.data.html);
        setTimeout(function(){

            initLivewireTable();
            loadJqueryComponents();

        }, 250);

    }, '/quote/addFromJob', {
        'disabled': SPAN_QUOTE_CARD.data('disabled')
    }, true);
});

$(document).on('click', 'form#job-register #job-part-quote-card a.job-remove-quote', function(e) {
    const FORM = $(this).closest('form');
    execJobRemoveOrDeleteQuote(
        FORM,
        `Deseja desvincular o orçamento do Job? Essa ação não deletará o orçamento.`
    );
});

$(document).on('click', 'form#job-register #job-part-quote-card a.job-delete-quote', function(e) {
    const FORM = $(this).closest('form');
    execJobRemoveOrDeleteQuote(
        FORM,
        `Deseja deletar o orçamento? Ele tembém será desvinculado do Job.`,
        true
    );
});

$(document).on('change', 'form#quoteItem-add select[name="qi-item"]', function(e) {
    $(this).closest('form').find('input[name="qi-price"]').val(
        $(this).find(':selected').data('price')
    );
});

$(document).on('submit', 'form#quoteItem-add', function(e) {
    e.preventDefault();
    let FORM = $(this);

    submitModalForm(FORM, function(retorno) {
        FORM.find('.btn-modal-close').click();
        showSuccessAlert({
            'title': 'Sucesso!',
            'text': retorno.message
        });

        loadJqueryComponents();
        setTimeout(function() {
            // initLivewireTable();
            refreshAllLivewireTables();
        }, 250);
    }, null);
});

function execJobRemoveOrDeleteQuote(FORM, confirmText, deleteQuote=false)
{
    var confirm = getConfirm({
        title: 'Confirmação',
        text: confirmText
    });
    confirm.fire().then((result) => {
        if (!result.isConfirmed) {
            return false;
        }

        submitModalForm(FORM, function(retorno) {

            showSuccessAlert({
                'title': 'Sucesso!',
                'text': retorno.message
            });
            FORM.find('span#job-partials-quoteCard').html(retorno.data.html);

        }, '/quote/removeFromJob', {
            'formDisabled': FORM.find('span#job-partials-quoteCard').data('disabled'),
            'deleteQuote': deleteQuote
        }, true);
    });
}

function showLoader()
{
    $.LoadingOverlay("show");
    setTimeout(function(){
        $.LoadingOverlay("hide");
    }, 10000);
}

function closeLoader()
{
    $.LoadingOverlay("hide");
}

function ajaxSetup(csrf)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf ?? $('meta[name="csrf-token"]').attr('content'),
            // 'Authorization': `Bearer ${USER_API_TOKEN_ID}`,
            // 'domain': DOMAIN_CODED
        }
    });
}

function uuidv4() {
    return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
      (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
    );
}

function getAjaxErrorMsg(data)
{
    if (typeof data.responseJSON == 'undefined' || typeof data.responseJSON.message == 'undefined') {
        return 'Erro ao processar essa requisição!';
    }

    return data.responseJSON.message;
}

function loadJqueryComponents()
{
    setTimeout(function(){
        loadMaskMoney();
        loadBootstrapSelect();
        loadDatePicker();
    }, 250);
}

function loadMaskMoney()
{
    $(".jq-mask-money").maskMoney({
        // prefix:'R$ ',
        allowNegative: true,
        thousands: '.',
        decimal: ',',
        // affixesStay: false
    });
}

function loadBootstrapSelect()
{
    $('.bootstrap-select').selectpicker({
        style: '',
        styleBase: 'form-select'
    });
}

function loadDatePicker()
{
    $(".jq-datepicker").datepicker({
        dateFormat: 'dd/mm/yy',
        closeText:"Fechar",
        prevText:"&#x3C;Anterior",
        nextText:"Próximo&#x3E;",
        currentText:"Hoje",
        monthNames: ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
        monthNamesShort:["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
			dayNames:["Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado"],
			dayNamesShort:["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
        dayNamesMin:["Dom","Seg","Ter","Qua","Qui","Sex","Sáb"],
        weekHeader:"Sm",
        firstDay:1
    });
}

function showBootstrapModal(html)
{
    $('div[id^="bootstrap-modal-"]').remove();
    const eventDivId = 'bootstrap-modal-' + uuidv4();
    $('body').append(`<div id="${eventDivId}">${html}</div>`);
    const jqObj = $('#' + eventDivId).find('div.modal');

    var myModal = new bootstrap.Modal(document.getElementById(jqObj[0].id));
    myModal.show();

    return myModal;
}

function enableFormWhileSaving(formObj)
{
    formObj.find(":input").prop("disabled", false);
}

function disableFormWhileSaving(formObj)
{
    formObj.find(":input").prop("disabled", true);
}

function showJsonAjaxModal(type, url, data, csrf=null)
{
    ajaxSetup(csrf);
    
    $.ajax({
        type,
        url,
        data,
        dataType: 'json',
        beforeSend: function(){showLoader()},
        success: function (retorno) {
            if (retorno.error) {
                showErrorAlert({
                    title: 'Erro',
                    text: retorno.message
                });
                return;
            }

            showBootstrapModal(retorno.data.html);
            loadJqueryComponents();
        },
        complete: function(){closeLoader()},
        error: function (data) {
            showErrorAlert({
                title: 'Erro',
                text: getAjaxErrorMsg(data)
            });
        }
    });
}

function submitModalForm(oForm, successFnc, actionUrl=null, customData={}, skipDisableForm=false)
{
    let FORM = oForm;
    let CSRF = FORM.find('input[name="_token"]').val();

    ajaxSetup(CSRF);
    let formData = new FormData(FORM[0]);
    for (const [key, value] of Object.entries(customData)) {
        formData.append(key, value);
    }

    $.ajax({
        type: 'POST',
        url: actionUrl ?? FORM.attr('action'),
        data: formData,
        dataType: 'json',
        processData: false, // required for FormData with jQuery
        contentType: false, // required for FormData with jQuery
        beforeSend: function() {
            showLoader();
            if (!skipDisableForm) {
                disableFormWhileSaving(FORM);
            }
        },
        success: function (retorno) {
            if (retorno.error) {
                showErrorAlert({
                    'title': 'Erro!',
                    'text': retorno.message
                });
                return;
            }

            successFnc(retorno);
        },
        complete: function() {
            closeLoader();
            if (!skipDisableForm) {
                enableFormWhileSaving(FORM);
            }
        },
        error: function (data) {
            showErrorAlert({
                'title': 'Erro!',
                'text': 'Ocorreu um erro inesperado! Tente novamente.'
            });
            if (!skipDisableForm) {
                enableFormWhileSaving(FORM);
            }
        }
    });
}

// sweet alert
/**
 * 
 * @param {*} objVar [title|text]
 */
function showAlert(typeStr, objVar)
{
    Swal.fire({
        icon: typeStr,
        title: objVar.title,
        html: objVar.text,
        // footer: '<a href="">Why do I have this issue?</a>'
    });
}

function showErrorAlert(objVar)
{
    showAlert('error', objVar);
}

function showSuccessAlert(objVar)
{
    showAlert('success', objVar);
}

function showWarningAlert(objVar)
{
    showAlert('warning', objVar);
}

function showInfoAlert(objVar)
{
    showAlert('info', objVar);
}

function getConfirm(objVar)
{
    return Swal.mixin({
        title: objVar.title,
        html: objVar.text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim!',
        cancelButtonText: "Fechar",
    });
}
// ===========

// Livewire
function initLivewireTable()
{
    Livewire.start();
}

function refreshLivewireTable(parentSelector)
{
    var id = $(`${parentSelector} div[wire\\:id]`).attr('wire:id');
    var Liv = Livewire.find(id);
    Liv.refresh();
}

function refreshAllLivewireTables()
{
    $(`div[wire\\:id]`).each(function() {
        var id = $(this).attr('wire:id');
        var Liv = Livewire.find(id);
        Liv.refresh();

        delete Liv;
    });
}

Livewire.on('laraveltable:link:open:newtab', (url) => {
    window.open(url, '_blank').focus();
});

Livewire.on('laraveltable:action:feedback', (feedbackMessage) => {
    // Replace this native JS alert by your favorite modal/alert/toast library implementation. Or keep it this way!
    // window.alert(feedbackMessage);

    showInfoAlert({
        icon: null,
        title: 'Informação',
        html: feedbackMessage,
    });
});

Livewire.on('laraveltable:action:confirm', (actionType, actionIdentifier, modelPrimary, confirmationQuestion) => {
    // You can replace this native JS confirm dialog by your favorite modal/alert/toast library implementation. Or keep it this way!
    /*
    if (window.confirm(confirmationQuestion)) {
        // As explained above, just send back the 3 first argument from the `table:action:confirm` event when the action is confirmed
        Livewire.emit('laraveltable:action:confirmed', actionType, actionIdentifier, modelPrimary);
    }
    */
    
    var confirm = getConfirm({
        title: 'Confirmação',
        text: confirmationQuestion
    });
    confirm.fire().then((result) => {
        if (!result.isConfirmed) {
            return false;
        }
        
        Livewire.emit('laraveltable:action:confirmed', actionType, actionIdentifier, modelPrimary);
    });
});

Livewire.on('laraveltable:link:open:modal', (url, urlParam) => {
    // window.open(url, '_blank').focus();

    const emptyParam = (JSON.stringify(urlParam) === '{}') || (JSON.stringify(urlParam) === '"[]"' || (JSON.stringify(urlParam) === '[]'));
    showJsonAjaxModal('GET', url, emptyParam ? null: urlParam);
});