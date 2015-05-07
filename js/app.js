jQuery(document).ready(function($){

    var i18n = typeof wuco_i18n !== 'undefined' ? wuco_i18n : {},
        text = $.extend({
            selectAll: 'Select all',
            selectNone: 'Select none'
        }, i18n);

    $('.wuco-checkbox-list').checkboxList({text: text});

});

(function($){

    $.widget('wuco.checkboxList', {
        options: {
            prefix: 'wuco-checkbox-list',
            text: {
                selectAll: 'Select all',
                selectNone: 'Select none'
            }
        },

        _create: function(){
            this.parentItem = this.element.children('li');
            this.parentItem.addClass(this.options.prefix + '-parent');

            this.parentCheckbox = this.element.find('input[type="checkbox"]').not('li ul li input[type="checkbox"]');
            this.parentCheckbox.addClass(this.options.prefix + '-parent-checkbox');

            this.childList = this.parentItem.find('ul');
            this.childCheckbox = this.childList.find('input[type="checkbox"]');

            this._addBulkActions();

            this._addEvents();
        },

        _addEvents: function(){

            this.parentCheckbox.on('change', {context: this}, function(e){
                var self = e.data.context,
                    list = $(this).closest('li').find('ul');

                if($(this).is(':checked')){
                    list.find('input[type="checkbox"]').prop('checked', true);
                } else {
                    list.find('input[type="checkbox"]').prop('checked', false);
                }

            });

            this.childCheckbox.on('change', {context: this}, function(e){
                var self = e.data.context,
                    parent = $(this).closest('.' + self.options.prefix + '-parent');

                if(!$(this).is(':checked')){
                    parent.find('.' + self.options.prefix + '-parent-checkbox').prop('checked', false);
                }
            });

            this.actions.on('click', '.select-all', {context: this}, function(e){
                var self = e.data.context;
                self.parentCheckbox.prop('checked', true).change();
                return false;
            });

            this.actions.on('click', '.select-none', {context: this}, function(e){
                var self = e.data.context;
                self.parentCheckbox.prop('checked', false).change();
                return false;
            });


        },

        _addBulkActions: function(){
            this.actions = $('<div class="' + this.options.prefix + '-actions" />');
            this.actions
                .append('<a href="#" class="select-all">' + this.options.text.selectAll + '</a>')
                .append(' | ')
                .append('<a href="#" class="select-none">' + this.options.text.selectNone + '</a>')
                .insertAfter(this.element);
        }
    });

})(jQuery);