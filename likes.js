var Kudoable,
    __bind = function (fn, me) {
        return function () {
            return fn.apply(me, arguments);
        };
    };

 Kudoable = (function () {

     function Kudoable(element) {
         this.element = element;
         this.unlike = __bind(this.unlike, this);

         this.complete = __bind(this.complete, this);

         this.end = __bind(this.end, this);

         this.start = __bind(this.start, this);

         this.bindEvents();
         this.element.data('likeable', this);
         this.meta = jQuery('.like-meta-' + this.element.attr('data-id'));
         this.element.data('likeable', this);
     }

     Kudoable.prototype.bindEvents = function () {
         this.element.children('.like-object').mouseenter(this.start);
         this.element.children('.like-object').mouseleave(this.end);

         if (window.likesdata.unlike) {
             this.element.children('.like-object').click(this.unlike);
         } else {
             this.element.children('.like-object').css('cursor', 'default');
         }
         jQuery(document).on('touchstart', this.element.children('.like-object'), this.start);
         return jQuery(document).on('touchend', this.element.children('.like-object'), this.end);
     };

     Kudoable.prototype.isKudoable = function () {
         return this.element.hasClass('likeable');
     };

     Kudoable.prototype.isKudod = function () {
         return this.element.hasClass('like-complete');
     };

     Kudoable.prototype.start = function () {
         if (this.isKudoable() && !this.isKudod()) {
             this.element.trigger('like:active');
             this.element.addClass('like-active');

             this.meta.children('.like-hideonhover').hide();
             this.meta.children('.like-dontmove').show();

             this.timer = setTimeout(this.complete, 700);

             return this.timer;
         }
     };

     Kudoable.prototype.end = function () {
         if (this.isKudoable() && !this.isKudod()) {
             this.element.trigger('like:inactive');
             this.element.removeClass('like-active');
             this.meta.children('.like-hideonhover').show();
             this.meta.children('.like-dontmove').hide();
             if (this.timer != null) {
                 return clearTimeout(this.timer);
             }
         }
     };

     Kudoable.prototype.complete = function () {
         this.end();
         this.incrementCount();
         this.element.addClass('like-complete');
         return this.element.trigger('like:added');
     };

     Kudoable.prototype.unlike = function (event) {
         event.preventDefault();
         if (this.isKudod()) {
             this.decrementCount();
             this.element.removeClass('like-complete');
             return this.element.trigger('like:removed');
         }
     };

     Kudoable.prototype.setCount = function (count) {
         return this.meta.find('.like-count:first').html(count);
     };

     Kudoable.prototype.currentCount = function () {
         return parseInt(this.meta.find('.like-count:first').html());
     };

     Kudoable.prototype.incrementCount = function () {
         return this.setCount(this.currentCount() + 1);
     };

     Kudoable.prototype.decrementCount = function () {
         var curCount = this.currentCount();
         if (curCount > 0) {
             return this.setCount(this.currentCount() - 1);
         } else {
             return curCount;
         }
     };

     return Kudoable;

 }());

 jQuery(function ($) {

     var plugin = $.fn.likeable = function () {
         return this.each(function () {
             return new Kudoable($(this));
         });
     };

     return plugin;
 });