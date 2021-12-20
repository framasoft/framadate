function myPreviewRender(text) {
    text = text.replace(/[\u00A0-\u9999<>\&]/gim, function (i) {
        return "&#" + i.charCodeAt(0) + ";";
    });
    text = EasyMDE.prototype.markdown(text);
    text = DOMPurify.sanitize(text);

    return text;
}

function MDEWrapper(textarea, enableButton, disableButton) {
    this.element = textarea;
    this.enableButton = enableButton;
    this.disableButton = disableButton;
    this.simplemde = null;

    var wrapper = this;

    if (this.enableButton) {
        this.enableButton.on("click", function () {
            wrapper.enable();
        });
    }
    if (this.disableButton) {
        this.disableButton.on("click", function () {
            wrapper.disable();
        });
    }
}

MDEWrapper.prototype.enable = function () {
    var wrapper = this;
    if (this.simplemde == null) {
        this.simplemde = new EasyMDE({
            element: wrapper.element,
            forceSync: true,
            status: true,
            // previewRender: myPreviewRender,
            renderingConfig: {
                sanitizerFunction: function (text) {
                    return DOMPurify.sanitize(text);
                },
            },
            spellChecker: false,
            promptURLs: true,
            minHeight: "200px",
            maxHeight: "300px",
            autoDownloadFontAwesome: false,
        });
        if (this.enableButton) {
            this.enableButton.addClass("active");
        }
        if (this.disableButton) {
            this.disableButton.removeClass("active");
        }
    }
};

MDEWrapper.prototype.disable = function () {
    if (this.simplemde != null) {
        this.simplemde.toTextArea();
        this.simplemde = null;
        if (this.disableButton) {
            this.disableButton.addClass("active");
        }
        if (this.enableButton) {
            this.enableButton.removeClass("active");
        }
    }
};

MDEWrapper.prototype.isEnabled = function () {
    return this.simplemde != null;
};
