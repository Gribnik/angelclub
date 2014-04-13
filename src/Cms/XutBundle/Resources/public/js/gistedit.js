/* FIXME: that script might be included twice for some reason */
var gistEditForms = []; // A stack of views rendered on current page

PostFormData = Backbone.Model.extend({
    defaults: {
        content: ''
    }
});

/* TODO: make an AJAX loading progress logic */

gistEditForm = Backbone.View.extend({
    /* TODO: pass form control link/button instead of list each here */
    events: {
        'click #blog-edit' : 'toggleForm',
        'click #blog-remove' : 'removeItem',
        'click a#blog-new' : 'toggleForm',
        'click a#image-new' : 'toggleForm',
        'click div.image-edit' : 'toggleForm'
    },

    initialize: function(options) {
        this.loaded = false; // Is the form loaded
        gistEditForms.push(this); // Add this view to the global views stack
        _.bindAll(this, 'initialize', 'render', 'toggleForm', 'loadForm', 'unrender', 'removeItem'); // fixes loss of context for 'this' within methods
        _.extend(this, _.pick(options, 'formPath', 'viewPort', 'template', 'editorImageUploadPath', 'removePostPath')); // Pick options, passed to the controller
        this.formModel = new PostFormData();
        this.formModel.urlRoot = this.formPath;
    },

    toggleForm: function() {
        /* Hide other opened views */
        var currentViewId = this.cid;
        gistEditForms.forEach(function(view) {
            if (view.cid != currentViewId) {
                view.unrender();
            }
        });

        if (this.loaded === true) {
            $(this.viewPort).toggle(200);
        } else {
            this.loadForm()
        }
    },

    render: function() {
        if (this.loaded === true) {
            this.viewPort.html(this.formModel.get("content"));
            var template = _.template( $(this.template).html(), {} );
            var imagesUploadPath = this.editorImageUploadPath;
            this.viewPort.html(template).find('#blogpost_content').editable({
                inlineMode: false,
                autosave: true,
                imageUploadURL: imagesUploadPath,
                imageParams: {id: "wysiwyg"}
            });
        }
    },

    unrender: function() {
        if (this.loaded === true) {
            $(this.viewPort).hide(200).html('');
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
    },

    removeItem: function() {
        var sure = confirm("Are you really want to get rid of this shit?")
        if (sure == true) {
            window.location = this.removePostPath;
        }
    }
});