    // checking notification
    function check_notif() {
        ajax_x_t2(
            baseUrl + "Notif/checking_notif",
            function(e) {
                if(e['error'] === false){
                    var badge = '';
                    var list_notif = '';
                    var list = e['list'];
                    if( e.num > 0 ){
                        badge += `<i class="fas fa-bell" style="color: #415192;"></i>
                                  <span class="badge bg-danger">${e.num}</span>`;
                      }else{
                        badge += `<i class="fas fa-bell" style="color: #415192;"></i>`;
                    }
                    list_notif +=  `<span class="dropdown-item dropdown-header">${e.num} Notifications</span>
                                    <div class="dropdown-divider"></div>`;
                                    
                    for( x in list ){
                        list_notif +=  `<a href="#" class="dropdown-item">
                                            <i class="${list[x].icon} mr-2"></i> ${list[x].name}
                                            <span class="float-right text-muted text-sm" style="font-size: 12px !important;">${list[x].num} ${list[x].title}</span>
                                        </a>
                                        <div class="dropdown-divider"></div>`;
                    }

                    $('#listNotif').html(list_notif);
                    $('#notif-bell').html(badge);
                }else{
                    frown_alert(e['error_msg'])
                }
            },
            []
        );
    }

    check_notif();
