if (ANCENC !== undefined) {
    jQuery(document).ready($ => {
        const nonce = jQuery('#wp_ancenc_options_nonce').val();
        $('.ancenc-toggle').click(evt => {
            $(evt.target).siblings('input').trigger('click');
        });

        $('#wp-ancenc-save-settings').click((evt) => {
            evt.preventDefault();
            $.ajax(ANCENC.ajax_url, {
                method: 'POST',
                data: {
                    action: 'ancenc_update_settings',
                    nonce: nonce,
                    data: $('#wp-ancenc-settings-form').serialize()
                }
            }).then(data => {
                console.log(data);
            })
        })
    })
}