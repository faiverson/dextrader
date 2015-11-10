var Contact = (function () {

    var $container = $('#sectionFooter'),
        $form = $container.find('form');

    return {
        init: function () {
            var self = this;
            //self.startForm()
        },
        startForm: function () {
            var self = this;
            $form.validate({
                rules: {
                    name: {
                        required: true
                    },
                    subject: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    message: {
                        required: true
                    }
                },
                submitHandler: function (form) {
                    self.set();
                },
                invalidHandler: function(event, validator) {
                    var err = '';
                    _.each(validator.invalid, function(item) {
                        err += item + '<br>';
                    });
                    toastr.error(err);
                },
                messages: {
                    name: "Nombre incompleto",
                    subject: "Ingrese un asunto",
                    email: {
                        required: "Email incompleto",
                        email: "Email no valido"
                    },
                    message: "Ingrese un mensaje"
                }
            });
        },
        set: function(data) {
            var self = this;

            self.blocking($.ajax({
                url: '/api/contact-us',
                data: {
                    name: $form.find('[name=name]').val(),
                    phone: $form.find('[name=phone]').val(),
                    email: $form.find('[name=email]').val(),
                    subject: $form.find('[name=subject]').val(),
                    message: $form.find('[name=message]').val()
                },
                success: function (response) {
                    if(response.success) {
                        self.resetForm();
                        toastr.success('Mensaje enviado! Gracias!');
                    } else {
                        toastr.error(response.error);
                    }
                }
            }), $container, 'Cargando');
        },
        resetForm: function() {
            $form.find('[name=name]').val('');
            $form.find('[name=phone]').val('');
            $form.find('[name=email]').val('');
            $form.find('[name=subject]').val('');
            $form.find('[name=message]').val('');
        },
        blocking: function (promise, el, message, options) {
            var block = '<span class="common-loading">%text%</span>';

            message = typeof message === 'undefined' ? 'Loading' : message;
            block = block.replace('%text%', message);
            el = el ? el : 'body';

            options = $.extend(options, {
                message: block
            });

            $(el).block(options);

            promise.done(function () {
                $(el).unblock();
            });
        },
    };
})();

$(document).ready(function() {
    Contact.init();
});