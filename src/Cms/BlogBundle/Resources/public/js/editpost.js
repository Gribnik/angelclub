/* FIXME: that script might be included twice for some reason */
PostFormData = Backbone.Model.extend({
    defaults: {
        content: ''
    }
});

PostForm = Backbone.View.extend({
    events: {
        'click #blog-edit' : 'toggleForm',
        'click a#blog-new' : 'toggleForm'
    },

    initialize: function(options) {
        this.loaded = false; // Is the form loaded
        _.bindAll(this, 'initialize', 'render', 'toggleForm', 'loadForm', 'unrender'); // fixes loss of context for 'this' within methods
        _.extend(this, _.pick(options, 'formPath', 'viewPort')); // Pick options, passed to the controller
        this.formModel = new PostFormData();
        this.formModel.urlRoot = this.formPath;
    },

    toggleForm: function() {
        if (this.loaded === true) {
            $('#blog-form-edit').toggle(200);
        } else {
            this.loadForm()
        }
    },

    render: function() {
        if (this.loaded === true) {
            this.viewPort.html(this.formModel.get("content"));
            var template = _.template( $('#blog-form-template').html(), {} );
            this.viewPort.html(template).find('#blogpost_content').editable({
                inlineMode: false,
                autosave: true
            });
        }
    },

    unrender: function() {
        if (this.loaded === true) {
            this.toggleForm();
            this.viewPort.html('');
            this.loaded = false;
        }
    },

    loadForm: function() {
        var formView = this;
        this.formModel.fetch({
            success: function(formModel) {
                formView.loaded = true;
                formView.render();
            },
            error: function() {
                location.reload();
            }
        })
    }
});