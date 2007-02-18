fx.Scroll = Class.create();
fx.Scroll.prototype = Object.extend(new fx.Base(), {
    initialize: function(options) {
        this.setOptions(options);
    },

    scrollTo: function(el){
        var desty = Position.cumulativeOffset($(el));
        var dest = desty[1];
        var client = window.innerHeight || document.documentElement.clientHeight;
        var full = document.documentElement.scrollHeight;
        var top = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
        if (dest+client > full) this.custom(top, dest - client + (full-dest));
        else this.custom(top, dest);
    },

    increase: function(){
        window.scrollTo(0, this.now);
    }
});


var ScrollLinks = {
    currentHash: false,
    start: function(){
        this.scroll = new fx.Scroll({duration: 800, onComplete: function(){ScrollLinks.end();}});
        this.allinks = document.getElementsByTagName('a');
        for (i=0; i<this.allinks.length; i++){
            var lnk = this.allinks[i];
            if ((lnk.href && lnk.href.indexOf('#') != -1) && ( (lnk.pathname == location.pathname) || ('/'+lnk.pathname == location.pathname) ) && (lnk.search == location.search)) {
                lnk.onclick = function(){
                    ScrollLinks.scroll.clearTimer();
                    this.initialHref = this.href;
                    this.initialHash = this.hash;
                    this.href = "javascript:void(0)";
                    setTimeout(function(){this.href = this.initialHref;}.bind(this), 200);
                    ScrollLinks.click(this);
                }
            }
        }
    },

    click: function(link){
        this.currentHash = link.initialHash.substr(1);
        if (this.currentHash) {
            for (j=0; j<this.allinks.length; j++){
                if (this.allinks[j].id == this.currentHash){
                    if (!window.opera) this.scroll.scrollTo(this.allinks[j]);
                    else this.scroll.scrollTo(this.allinks[j].parentNode);
                    break;
                }
            }
        }
    },

    end: function(){
        window.location.href = "#"+this.currentHash;
        this.currentHash = false;
    }
}


addLoadEvent(function(){
    ScrollLinks.start();
});