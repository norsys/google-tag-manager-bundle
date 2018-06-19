$(function () {
    let gtmConfiguration = $('#google-tag-manager-configuration')

    let id        = gtmConfiguration.data('id')
    let onEvent   = gtmConfiguration.data('on-event')
    let dataLayer = [$.parseJSON(atob(gtmConfiguration.data('data-layer')))]

    if (onEvent === '1') {
        let eventName      = gtmConfiguration.data('event-name')
        let eventContainer = gtmConfiguration.data('event-container')

        $(eventContainer).on(eventName, function () {
            loadGoogleTagManager(id, dataLayer);
        })
    } else {
        loadGoogleTagManager(id, dataLayer);
    }
})

function loadGoogleTagManager(id, dataLayer) {
    if (dataLayer === undefined) {
        dataLayer = []
    }

    dataLayer.push({
        'gtm.start': new Date().getTime(),
        event:'gtm.js'
    })

    window.dataLayer = dataLayer

    let gtmScriptCall = document.createElement('script')
    gtmScriptCall.async = true;
    gtmScriptCall.src = 'https://www.googletagmanager.com/gtm.js?id=' + id;

    $('head').append(gtmScriptCall)
}