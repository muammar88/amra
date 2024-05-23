function pelaksanaan_Pages() {
   return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentPelaksanaan">
                  <input type="hidden" id="desc_pelaksanaan" value="">
                  <div class="col-12 col-lg-12 my-3 ">
                     <ul class="list-group" id="pelaksanaan">
                     </ul>
                  </div>
               </div>
            </div>`;
}

function pelaksanaan_getData(){
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
                                 style="margin:.15rem .1rem !important">
                                 <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </li>`;
               desc[e['data'][x]['part']] = e['data'][x]['desc'];
            }
            $(`#desc_pelaksanaan`).val(JSON.stringify(desc));
            $(`#pelaksanaan`).html(html);
         }else{
            frown_alert(e['error_msg']);
         }
      },
      [{param:'pelaksanaan'}]
   );
}
