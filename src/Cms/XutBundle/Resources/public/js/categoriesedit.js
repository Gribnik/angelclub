categoryDataModel = Backbone.Model.extend({
    defaults: {
        'name': '',
        '_token': ''
    }
});

categoryEditForm = Backbone.View.extend({
    events: {
        'change .category-name-edit': 'saveAction',
        'click .remove-action': 'removeAction',
        'click #create-category-submit': 'saveAction'
    },

    initialize: function(options) {
        _.extend(this, _.pick(options, 'template', 'categoryData')); // Pick options, passed to the controller
        _.bindAll(this, 'initialize', 'saveAction', 'removeAction', 'render', 'unrender'); // fixes loss of context for 'this' within methods
        console.log('view created'); // DEBUG
        this.render();
    },

    render: function() {
        this.template.show('fast')
    },

    unrender: function() {
        this.template.hide('fast').remove();
    },

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

    validateAction: function() {
        var categoryName = this.template.find('.category-name').val();
        if ($.trim(categoryName) == '') {
            return false;
        } else {
            return true;
        }
    },

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