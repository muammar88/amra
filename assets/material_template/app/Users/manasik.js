function manasik_Pages() {
   return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentManasik">
                  <input type="hidden" id="desc_manasik" value="">
                  <div class="col-12 col-lg-12 my-3 ">
                     <ul class="list-group" id="manasik">
                     </ul>
                  </div>
               </div>
            </div>`;
}

function manasik_getData(){
   ajax_x(
      baseUrl + "Panduan_manasik/get_info_panduan",
      function(e) {
         if( e['error'] == false ){
            var html = ``;
            var desc = {};
            for( x in e['data'] ){
               html += `<li class="list-group-item d-flex justify-content-between align-items-center py-1" id="${e['data'][x]['part']}">
                           ${e['data'][x]['title']}
                           <div>
                              <button type="button" class="btn btn-default btn-action" title="Detail ${e['data'][x]['title']}"
                                 style="margin:.15rem .1rem !important" detail_panduan('manasik',  '${e['data'][x]['part']}')>
                                 <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </li>`;
               desc[e['data'][x]['part']] = e['data'][x]['desc'];
            }
            $(`#desc_manasik`).val(JSON.stringify(desc));
            $(`#manasik`).html(html);
         }else{
            frown_alert(e['error_msg']);
         }
      },
      [{param:'manasik'}]
   );
}
