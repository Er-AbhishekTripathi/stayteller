jQuery(function ($) {
    $("#send-agencies-contact").validate({
        rules: {
          name: "required",
          email: {
            required: true,
            email: true
          },
          phone: "required",
          message: "required",
        },
    });
    $('#send-agencies-contact').on('submit', function (e) {
        e.preventDefault();
        var $form = $('#send-agencies-contact');
        if (!$form.valid) return false;

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            url: urlSubmitContact,
            success: function (res) {
                let divMessage = $form.closest().find('.show-message');
                divMessage.html(res.message);
                $form.find("input[type=text], textarea").val("");
            }
        })
    })
});
