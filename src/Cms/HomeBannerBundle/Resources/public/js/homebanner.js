HBFormData = Backbone.Model.extend({
    defaults: {
        content: ''
    }
});

HBPreview = Backbone.Model.extend();

HbForm = Backbone.View.extend({
    events: {
        'click a#hb-change-button' : 'toggleForm',
        'click #hb-preview-button' : 'previewChanges'
    },

    initialize: function(options) {
        this.loaded = false; // Is the form loaded
        this.childPopup = false; // Preview window
        _.bindAll(this, 'initialize', 'render', 'toggleForm', 'loadForm', 'previewChanges', '_submitData'); // fixes loss of context for 'this' within methods
        _.extend(this, _.pick(options, 'formPath', 'previewPath', 'savePath')); // Pick options, passed to the controller
        this.formModel = new HBFormData();
        this.previewModel = new HBPreview();
        this.previewModel.urlRoot = this.previewPath;
        this.formModel.urlRoot = this.formPath;
        this.formModel.urlSave = this.savePath;
    },

    render: function() {
        if (this.loaded === true) {
            this.$el.html(this.formModel.get("content"));
            var template = _.template( $("#hb-form-template").html(), {} );
            this.$el.html( template );
        }
    },

    toggleForm: function() {
        if (this.loaded === true) {
            $('#hb-form').toggle(200);
        } else {
            this.loadForm()
        }
    },

    loadForm: function() {
        var formView = this; /* TODO: optimise it */
        this.formModel.fetch({
            success: function(formModel) {
                formView.loaded = true;
                formView.render();
            },
            error: function() {
                location.reload();
            }
        })
    },

    previewChanges: function() {
        this._submitData();
    },

    _submitData: function(event) {
        var values = {};
        var formView = this;
        if(event){ event.preventDefault(); }
        this.childPopup = window.open('about:blank', 'Preview');
        this.$('form').get(0).setAttribute('action', this.previewPath);
        _.each(this.$('form').serializeArray(), function(input) {
            values[ input.name ] = input.value;
        });

        this.previewModel.save(values, {
            iframe: true,
            files: this.$('form :file'),
            data: values,
            success: function(previewModel) {
                formView.$('form').get(0).setAttribute('action', formView.formModel.urlSave);
                if (previewModel.get('status') == 'success') {
                    /* TODO: prevent popup from blocking */
                    formView.childPopup.document.write(previewModel.get('content'));
                } else {
                    /* TODO: show an error somehow */
                    alert('There was an error processing your request');
                }
            }
        });
    }
});