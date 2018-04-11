$(document).ready(function() {

    /**
     * Markdown Editor
     * @type {MDEWrapper}
     */
    wrapper = new MDEWrapper($('.js-desc textarea')[0], $('#rich-editor-button'), $('#simple-editor-button'));
    var firstOpening = true;

    /**
     * Save a list of admin polls inside LocalStorage
     * @param adminPolls
     */
    function setAdminPolls(adminPolls) {
        localStorage.setItem('admin_polls', JSON.stringify(adminPolls));
    }

    /**
     * Add an admin poll inside LocalStorage
     * @param adminPoll
     */
    function addAdminPoll(adminPoll) {
        var adminPolls = localStorage.getItem('admin_polls');
        if (adminPolls === null) {
            adminPolls = [];
        } else {
            adminPolls = JSON.parse(adminPolls);
        }
        /**
         * Test if the poll is already inside the list
         */
        var index = adminPolls.findIndex(function (existingPoll) {
            return existingPoll.url === adminPoll.url;
        });
        if (index === -1) {
            adminPolls.push(adminPoll);
        } else { // if the poll is already present, we need to update the last access date
            adminPolls[index] = adminPoll;
        }
        setAdminPolls(adminPolls);
    }

    var adminPoll = {
        url: window.location.href,
        title: $('#title-form h3').get(0).childNodes[0].nodeValue,
        accessed: (new Date()).toISOString()
    };

    if (!localStorage.getItem('admin_polls')) {
        setAdminPolls([adminPoll]);
    } else {
        addAdminPoll(adminPoll);
    }

    /**
     * Initiate popovers
     */
    $('[data-toggle="popover"]').popover();

    /**
     * Create node with text to use the Clipboard API
     * @param text
     * @returns {HTMLPreElement}
     */
    function createNode(text) {
        var node = document.createElement('pre');
        node.style.width = '1px';
        node.style.height = '1px';
        node.style.position = 'fixed';
        node.style.top = '5px';
        node.textContent = text;
        return node;
    }

    /**
     * Copy a Node to use for Clipboard API
     * @param node
     */
    function copyNode(node) {
        var selection = getSelection();
        selection.removeAllRanges();

        var range = document.createRange();
        range.selectNodeContents(node);
        selection.addRange(range);

        document.execCommand('copy');
        selection.removeAllRanges();
    }

    /**
     * Copy a text inside the clipboard
     * @param text
     */
    function copyText(text) {
        var node = createNode(text);
        document.body.appendChild(node);
        copyNode(node);
        document.body.removeChild(node);
    }

    /**
     * When clicked on a .clipboard-url link, copy link inside clipboard and show popover confirmation for 2 seconds
     */
    $('body').on('click', '.clipboard-url', function(e) {
        var btn = $(e.target);
        /**
         * Kind of workaround for clicking child instead of button (because propagation is stopped with preventDefault())
         */
        if (!btn.get(0).hasAttribute('data-toggle')) {
            btn = btn.parent();
        }
        /**
         * Try catch because reasons : https://caniuse.com/#feat=clipboard
         */
        try {
            copyText(btn.attr('href'));
            btn.popover('show');
            setTimeout(function () {
                btn.popover('hide');
            }, 2000);
            e.preventDefault();
        } catch (err) {
            console.log('Oops, unable to copy');
        }
    });

    $('#title-form .btn-edit').on('click', function() {
        $('#title-form h3').hide();
        $('.js-title').removeClass('hidden');
        $('.js-title input').focus();
        return false;
    });

    $('#title-form .btn-cancel').on('click', function() {
        $('#title-form h3').show();
        $('#title-form .js-title').addClass('hidden');
        $('#title-form .btn-edit').focus();
        return false;
    });

    $('#name-form .btn-edit').on('click', function() {
        $('#name-form p').hide();
        $('.js-name').removeClass('hidden');
        $('.js-name input').focus();
        return false;
    });

    $('#name-form .btn-cancel').on('click', function() {
        $('#name-form p').show();
        $('#name-form .js-name').addClass('hidden');
        $('#name-form .btn-edit').focus();
        return false;
    });

    $('#email-form .btn-edit').on('click', function() {
        $('#email-form p').hide();
        $('#email-form .js-email').removeClass('hidden');
        $('.js-email input').focus();
        return false;
    });

    $('#email-form .btn-cancel').on('click', function() {
        $('#email-form p').show();
        $('#email-form .js-email').addClass('hidden');
        $('#email-form .btn-edit').focus();
        return false;
    });

    $('#description-form .btn-edit').on('click', function() {
        $('#description-form .well').hide();
        $('#description-form .control-label .btn-edit').hide();
        $('#description-form .js-desc').removeClass('hidden');
        $('.js-desc textarea').focus();
        if (firstOpening) {
            firstOpening = false;
            if ($('#rich-editor-button').hasClass('active')) {
                wrapper.enable();
            }
        }
        return false;
    });

    $('#description-form .btn-cancel').on('click', function() {
        $('#description-form .well').show();
        $('#description-form .control-label .btn-edit').show();
        $('#description-form .js-desc').addClass('hidden');
        $('.js-desc .btn-edit').focus();
        return false;
    });

    $('#poll-rules-form .btn-edit').on('click', function() {
        $('#poll-rules-form p').hide();
        $('#poll-rules-form .js-poll-rules').removeClass('hidden');
        $('.js-poll-rules select').focus();
        return false;
    });

    $('#poll-rules-form .btn-cancel').on('click', function() {
        $('#poll-rules-form p').show();
        $('#poll-rules-form .js-poll-rules').addClass('hidden');
        $('.js-poll-rules .btn-edit').focus();
        return false;
    });

    $('#poll-hidden-form .btn-edit').on('click', function() {
        $('#poll-hidden-form p').hide();
        $('#poll-hidden-form .js-poll-hidden').removeClass('hidden');
        $('.js-poll-hidden input[type=checkbox]').focus();
        return false;
    });

    $('#poll-hidden-form .btn-cancel').on('click', function() {
        $('#poll-hidden-form p').show();
        $('#poll-hidden-form .js-poll-hidden').addClass('hidden');
        $('.js-poll-hidden .btn-edit').focus();
        return false;
    });

    $('#expiration-form .btn-edit').on('click', function() {
        $('#expiration-form p').hide();
        $('.js-expiration').removeClass('hidden');
        $('.js-expiration input').focus();
        return false;
    });

    $('#expiration-form .btn-cancel').on('click', function() {
        $('#expiration-form p').show();
        $('#expiration-form .js-expiration').addClass('hidden');
        $('#expiration-form .btn-edit').focus();
        return false;
    });


    $('#password-form .btn-edit').on('click', function() {
        $('#password-form p').hide();
        $('#password-form .js-password').removeClass('hidden');
        $('#password').focus();
        return false;
    });

    $('#password-form .btn-cancel').on('click', function() {
        $('#password-form p').show();
        $('#password-form .js-password').addClass('hidden');
        $('.js-password .btn-edit').focus();
        return false;
    });

    // Hiding other field when the admin wants to remove the password protection
    var removePassword = $('#removePassword');
    removePassword.on('click', function() {
        var removeButton =  removePassword.siblings('button');
        if (removePassword.is(":checked")) {
            $('#password_information').addClass('hidden');
            removeButton.removeClass('hidden');
        } else {
            $('#password_information').removeClass('hidden');
            removeButton.addClass('hidden');
        }
        removeButton.focus();
    });


});
