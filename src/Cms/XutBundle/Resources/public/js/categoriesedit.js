/**
 * Category model
 * @type {Backbone.Model}
 */
categoryDataModel = Backbone.Model.extend({
    defaults: {
        'name': '',
        '_token': ''
    }
});

/**
 * View for category edit/create form
 * @type {Backbone.View}
 */
categoryEditForm = Backbone.View.extend({
    events: {
        'change .category-name-edit': 'saveAction',
        'click .remove-action': 'removeAction',
        'click #create-category-submit': 'saveAction'
    },

    initialize: function(options) {
        _.extend(this, _.pick(options, 'template', 'categoryData'));
        _.bindAll(this, 'initialize', 'saveAction', 'removeAction', 'render', 'unrender');
        this.render();
    },

    /**
     * Shows category edit form
     */
    render: function() {
        this.template.show('fast')
    },

    /**
     * Hides category edit form
     */
    unrender: function() {
        this.template.hide('fast').remove();
    },

    /**
     * Saves the form data as existing or new category. Depends on what form is being saved
     */
    saveAction: function() {
        if (false == this.validateAction()) {
            alert("Category name cannot be empty");
        } else {
            this.categoryData.save({'name': this.template.find('.category-name').val()}, {
                success: function(model, response) {
                    if (response.status == 'created') {
                        /* TODO: append the new view instead of the page reloading */
                        location.reload()
                    }
                }
            })
        }
    },

    /**
     * Validates category edit/new form
     * @returns {boolean}
     */
    validateAction: function() {
        var categoryName = this.template.find('.category-name').val();
        if ($.trim(categoryName) == '') {
            return false;
        } else {
            return true;
        }
    },

    /**
     * Removes a requested category
     */
    removeAction: function() {
        var sure = confirm("Are you really want to get rid of this shit?")
        var that = this;
        if (true == sure) {
            this.categoryData.destroy({
                success: function() {
                    that.unrender();
                }
            })
        }
    }
})