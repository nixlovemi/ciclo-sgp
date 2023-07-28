$(document).ready(function(){
    $(document).on('click', 'div.alert button.btn-close', function(e){
        $(this).parent().fadeOut(500);
    });
});

// Livewire
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
    window.alert(feedbackMessage);

    /* ADJUST BEFORE USING IT
    showInfoAlert({
        icon: null,
        title: 'Informação',
        html: feedbackMessage,
    });
    */
});

Livewire.on('laraveltable:action:confirm', (actionType, actionIdentifier, modelPrimary, confirmationQuestion) => {
    // You can replace this native JS confirm dialog by your favorite modal/alert/toast library implementation. Or keep it this way!
    if (window.confirm(confirmationQuestion)) {
        // As explained above, just send back the 3 first argument from the `table:action:confirm` event when the action is confirmed
        Livewire.emit('laraveltable:action:confirmed', actionType, actionIdentifier, modelPrimary);
    }
    
    /* ADJUST BEFORE USING IT
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
    */
});

/* CHANGE BEFORE USING IT
Livewire.on('laraveltable:link:open:modal', (url, urlParam) => {
    // window.open(url, '_blank').focus();

    ajaxSetup();

    const emptyParam = (JSON.stringify(urlParam) === '{}') || (JSON.stringify(urlParam) === '"[]"' || (JSON.stringify(urlParam) === '[]'));
    $.ajax({
        type: 'GET',
        url,
        data: emptyParam ? null: urlParam,
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
});
*/

/* REMOVE????
Livewire.on('laraveltable:action:fixStickyScrollbar', () => {
    // to fix layout scrollbar
    // .scrollToFixed
    setTimeout(function(){
        window.dispatchEvent(new Event('resize'));
    }, 350);
});
*/
// ========