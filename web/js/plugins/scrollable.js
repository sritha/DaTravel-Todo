(function($){
    $.fn.scrollableList = function(options) {
        var settings = $.extend({
            scrollDistance: 18,
            btnUp: null,
            btnDown: null
        }, options );
        
        if(!settings.btnUp)
        {
            throw 'btnUp option must be set';
        }
        
        if(!settings.btnDown)
        {
            throw 'btnDown option must be set';
        }
        
        var distanceScrolled = 0;
        
        var functions = {
            scrollUp: function() {
                if(!functions['canScrollUp'].apply(this))
                {
                    return false;
                }
                distanceScrolled += settings.scrollDistance;
                $(this).children('li:first').css('margin-top', this.distanceScrolled);
                functions['toggleBtns'].apply(this);
                return false;
            },
            scrollDown: function() {
                if(functions['canScrollDown'].apply(this))
                {
                    distanceScrolled -= settings.scrollDistance;
                }
                $(this).children('li:first').css('margin-top', distanceScrolled);
                functions['showOrHideScrollArrows'].apply(this);
                return false;                
            },
            scrollToTop: function() {
                distanceScrolled = 0;
                $(this).children('li:first').css('margin-top', 0);
            },
            scrollToBottom: function() {
                
            },
            canScrollUp: function() {
                return (distanceScrolled != 0);
            },
            canScrollDown: function() {
                var firstItem = $(this).children('li:first');
                var viewableHeight = $(this).height();
                var totalLiHeight = 0;
                var that = this;
                var canScrollDown = false;
                $(this).children('li').each(function() {
                    totalLiHeight += $(this).outerHeight();
                    if(totalLiHeight > (viewableHeight + Math.abs(distanceScrolled)))
                    {
                        canScrollDown = true;
                        return false;
                    }
                });
                return canScrollDown;             
            },
            toggleBtns: function() {
                settings.btnUp.toggle(functions['canScrollUp'].apply(this));
                settings.btnDown.toggle(functions['canScrollDown'].apply(this));
            }
        }
        
        return this.each(function() {
            
        });
    }
}(jQuery));


