jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "percent-pre": function ( a ) {
        var x = (a == "-") ? 0 : a.replace( /%/, "" );
        return parseFloat( x );
    },
    "percent-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "percent-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "title-string-pre": function ( a ) {
        return a.match(/title="(.*?)"/)[1].toLowerCase();
    },
    "title-string-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "title-string-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );
jQuery.fn.dataTableExt.oApi.fnSetFilteringDelay = function ( oSettings, iDelay ) {
    var _that = this;
    if ( iDelay === undefined ) {
        iDelay = 250;
    }
    this.each( function ( i ) {
        $.fn.dataTableExt.iApiIndex = i;
        var
            $this = this, 
            oTimerId = null, 
            sPreviousSearch = null,
            anControl = $( 'input', _that.fnSettings().aanFeatures.f );
            anControl.unbind( 'keyup' ).bind( 'keyup', function() {
            var $$this = $this;
            if (sPreviousSearch === null || sPreviousSearch != anControl.val()) {
                window.clearTimeout(oTimerId);
                sPreviousSearch = anControl.val();  
                oTimerId = window.setTimeout(function() {
                    $.fn.dataTableExt.iApiIndex = i;
                    _that.fnFilter( anControl.val() );
                }, iDelay);
            }
        });
        return this;
    } );
    return this;
};
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "num-html-pre": function ( a ) {
        var x = a.replace( /<.*?>/g, "" );
        return parseFloat( x );
    },
    "num-html-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
    "num-html-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );