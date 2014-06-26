$(function() {
    
    TodoList = Backbone.Model.extend({
        urlRoot: '/todoLists/',
        initialize: function() {
        }
    });

    TodoListsCollection = Backbone.Collection.extend({
            model: TodoList,
            comparator: false,
            url: '/todoLists',
            initialize: function() {

            }
    });
   
    Item = Backbone.Model.extend({
         initialize: function() {
         }
     });
    
    ItemCollection = Backbone.Collection.extend({
         model: Item,
         comparator: false
     });
     
     ListView = Backbone.View.extend({
         tagName: 'li',
         initialize: function() {
             
         },
         render: function() {
            this.$el.html(_.template($('#todoLists-lists-template').html(), {
                name: this.model.get('name')
            }));
            return this;
         },
         events: {
             'click a' : 'viewList'
         },
         viewList: function() {
             this.trigger('list:selected', this.model);
         }
     });

    ListsView = Backbone.View.extend({
        el: $('#overlay'),
        initialize: function() {
            this.collection = new TodoListsCollection();
            this.listViews = [];
            this.isCollectionFetched = false;
        },
        render: function() {
            var doRender = function() {
                this.$el.html(_.template($('#todoLists-template').html()));
                var $ul = this.$el.find('ul:first');
                _.each(this.listViews, function(listView) {
                    $ul.append(listView.render().el);
                });                 
            };
            
            if(!this.isCollectionFetched)
            {
                
                var that = this;
                this.collection.fetch({
                    success: function(collection, response, options) {
                        that.collection.each(function(model) {
                            that.pushList(model);
                        });
                        that.isCollectionFetched = true;
                        doRender.apply(that);
                    }
                });
            }
            else
            {
                doRender.apply(this);
            }
            return this;
        },
        pushList: function(model) {
            var that = this;
            var listView = new ListView({model: model});
            listView.on('list:selected', function(list) {
                that.trigger('list:selected', list);
            });
            this.listViews.push(listView);
            return listView;
        },
        events: {
          'click #btn-add': 'createList',
          'click #btn-cancel': 'cancelCreateList',
          'click #create-list-submit' : 'saveList'
        },
        createList: function() {
            if($('#create-list-dialog').length)
            {
                return false;
            }
            this.$el.prepend(_.template($('#create-list-template').html()));
            return false;
        },
        cancelCreateList: function() {
            if(!$('#create-list-dialog').length)
            {
                return false;
            }
            this.$el.find('.dialog').remove();
            return false;
        },
        saveList: function() {
            var list = new TodoList({name: $('#list-name').val()});
            var that = this;
            list.save(null, {
                success: function() {
                    that.collection.add(list);
                    var $ul = that.$el.find('ul:first');
                    $ul.append(that.pushList(list).render().el);
                    that.cancelCreateList();
                }
            });
        }
    });
    

    ItemView = Backbone.View.extend({
        initialize: function() {
        },
        tagName: 'li',
        render: function() {
            this.$el.append(_.template($("#item-template").html(), {
                name: this.model.get('name'),
                is_done: this.model.get('is_done')
            }));
            return this;
        },
        events: {
            'change input': 'checkboxChanged'
        },
        checkboxChanged: function() {

            if(!this.model.get('is_done')){
                $(this.$('.name-display')).addClass('task-done');
            }else{
                $(this.$('.name-display')).removeClass('task-done');
            }

            this.model.set('is_done', !this.model.get('is_done'));
            this.model.unset('todo_list');
            //console.log(this.model.keys())
            this.model.save(null);
        }
    });
     
    
    TodoListView = Backbone.View.extend({
        el: $('#overlay'),
            initialize: function(){
                this.$listEl = null;
                this.itemViews = [];
                this.scrollDistance = 18;
                this.distanceScrolled = 0;
                this.itemsUrlRoot = '/todoLists/'+this.model.get('id')+'/items';
                this.collection = new ItemCollection(this.model.get('items'), {
                    urlRoot: this.itemsUrlRoot
                });
                var that = this;
                this.collection.each(function(model) {
                    that.itemViews.push(new ItemView({model: model}));
                });
            },
            render: function() {
                var html = _.template($("#todoList-template").html(), {
                    name: this.model.get('name')
                });
                
                this.$el.append(html);
                this.$listEl = this.$el.find('ul:first');
                var that = this;
                _.each(this.itemViews, function(itemView) {
                    that.$listEl.append(itemView.render().el);					
                });
                this.showOrHideScrollArrows();
                return this;
            },
            events : {
                'click #btn-add' : 'addItem',
                'click #btn-save' : 'saveItem',
                'click #btn-cancel' : 'cancelAddItem',
                'click #btn-up' : 'scrollUp',
                'click #btn-down' : 'scrollDown',
                'click #lists-button a' : 'gotoLists'
            },
            addItem: function() {
                if(!this.$el.find('#add-item').length)
                {
                    this.$listEl.append(_.template($("#add-item-template").html()));
                }
                this.scrollToBottom();
                return false;
            },
            saveItem: function() {
                this.$el.find('.error').remove();

                var item = new Item({name: $('#new-item').val(), is_done: false});
                item.urlRoot = this.itemsUrlRoot;

                var that = this;
                item.save(null, {
                    success: function() {
                        that.collection.add(item);
                        var itemView = new ItemView({model: item});
                        that.itemViews.push(itemView);
                        that.$el.find('#add-item').remove();
                        that.$listEl.append(itemView.render().el);
                    }, error: function(model, xhr, options) {
                        var errorMessage = $.parseJSON(xhr.responseText).user_message;
                        var error = $('<div class="error"><strong>Error</strong>'+errorMessage+'</div>')
                            .css('top', ($('#add-item').offset().top - 45) + 'px');
                        that.$el.prepend(error);
                    }
                });
            },
            cancelAddItem: function() {
                this.$el.find('#add-item').remove();
                this.$el.find('.error').remove();
                return false;                
            },
            scrollUp: function() {
                if(!this.canScrollUp())
                {
                    return false;
                }
                this.distanceScrolled += this.scrollDistance;
                this.$listEl.children('li:first').css('margin-top', this.distanceScrolled);
                this.showOrHideScrollArrows();
                return false;
            },
            scrollDown: function() {
                if(this.canScrollDown())
                {
                    this.distanceScrolled -= this.scrollDistance;
                }
                this.$listEl.children('li:first').css('margin-top', this.distanceScrolled);
                this.showOrHideScrollArrows();
                return false;
            },
            canScrollUp: function() {
                return (this.distanceScrolled != 0);
            },
            canScrollDown: function() {
                var firstItem = this.$listEl.children('li:first');
                var viewableHeight = this.$listEl.height();
                var totalLiHeight = 0;
                var that = this;
                var canScrollDown = false;
                this.$listEl.children('li').each(function() {
                    totalLiHeight += $(this).outerHeight();
                    if(totalLiHeight > (viewableHeight + Math.abs(that.distanceScrolled)))
                    {
                        canScrollDown = true;
                        return false;
                    }
                });
                return canScrollDown;
            },
            showOrHideScrollArrows: function() {
                this.$el.find('#btn-up').toggle(this.canScrollUp());
                this.$el.find('#btn-down').toggle(this.canScrollDown());
            },
            scrollToBottom: function() {
                var totalHeight = 0;
                this.$listEl.children('li').each(function() {
                    totalHeight += $(this).outerHeight();
                });
                var difference = totalHeight - this.$listEl.height();
                if(difference > 0)
                {
                    this.$listEl.children('li:first').css('margin-top', (0 - difference) + 'px');
                }
            },
            gotoLists: function() {console.log('here');
                this.trigger('lists:showAll');
            }
    });

    AppView = Backbone.View.extend({
        el: $('#notepad'),
        initialize: function(options) {
            this.currentChildView = null;
            this.showAllLists();
        },
        render: function() {
            this.$el.children('#overlay').html('');
            this.currentChildView.render();
            return this;
        },
        showList: function(list) {
            if(this.currentChildView)
            {
                this.currentChildView.$el.unbind();
            }
            this.currentChildView = new TodoListView({model: list});
            var self = this;
            this.currentChildView.bind('lists:showAll', function() {
                self.showAllLists();
                self.render();
            });
        },
        showAllLists: function() {
            if(this.currentChildView)
            {
                this.currentChildView.$el.unbind();
            }
            this.currentChildView = new ListsView();
            var self = this;
            this.currentChildView.bind('list:selected', function(list) {
                self.showList(list);
                self.render();
            });
        }
    });
    
    /*var todoList = new TodoList({id: 1});
    todoList.fetch({success: function() {
        var app = new AppView({
            todoListView: new TodoListView({model:todoList})
        });
        app.render();    
    }});*/
    
    var app = new AppView();
    app.render();
});
