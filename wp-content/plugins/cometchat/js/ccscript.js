window.onload = function(){
    var ccimg = document.getElementById("ccimg"),
        cc_dom_ready = document.getElementById("cc_dom_ready"),
        cc_dashboard = document.getElementById('cc_dashboard'),
        ccinitialaccess = document.getElementById('ccinitialaccess'),
        cc_plugin_admin_panel = document.getElementById("cc_plugin_admin_panel");

    [ 'cc_plugin_admin_panel', 'ccimg', 'cc_dom_ready' ].forEach(function( id ) {
        if(id !== null){
            document.getElementById( id ).style.opacity = 10;
        }
    });
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };
    if(isMobile.any() && cc_dashboard){
        document.getElementById('cc_dashboard').style.width = '90%';
    }
    if(isMobile.any() && ccinitialaccess){
        document.getElementById('ccinitialaccess').style.width = '95%';
    }
}

function openCCAdminPanel(cc_admin_url = ''){
    if(cc_admin_url !== ''){
        window.open(cc_admin_url);
    }
}