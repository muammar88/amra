function perjalanan_Pages() {
   return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentperjalanan">
                  <input type="hidden" id="desc_perjalanan" value="">
                  <div class="col-12 col-lg-12 my-3 ">
                     <ul class="list-group" id="perjalanan">
                     </ul>
                  </div>
               </div>
            </div>`;
}

function perjalanan_getData(){
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
                                 style="margin:.15rem .1rem !important" onClick="detail_panduan('perjalanan', '${e['data'][x]['part']}')">
                                 <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                              </button>
                              <button type="button" class="btn btn-default btn-action" title="Edit"
                                 style="margin:.15rem .1rem !important" onClick="edit_panduan('perjalanan', '${e['data'][x]['part']}')">
                                 <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                              </button>
                           </div>
                        </li>`;
               desc[e['data'][x]['part']] = e['data'][x]['desc'];
            }
            $(`#desc_perjalanan`).val(JSON.stringify(desc));
            $(`#perjalanan`).html(html);
         }else{
            frown_alert(e['error_msg']);
         }
      },
      [{param:'perjalanan'}]
   );
}

function detail_panduan(tab, param){
   ajax_x(
      baseUrl + "Panduan_manasik/get_detail_panduan",
      function(e) {
         if( e['error'] == false ){
            var html = `<div class="col-12">
                           <button class="btn btn-default" type="button" onclick="back_panduan('${tab}')" title="Kembali">
                              <i class="fas fa-arrow-left" style="font-size: 11px;"></i> Kembali
                           </button>
                        </div>
                        <div class="col-12 py-4">
                           <article class="border rounded py-4 px-5">
                              <center><span style="font-size:15px;"><b>${capitalizeFirstLetter(param)}</span></b></center>
                              <center><hr class="w-100 mt-3 mb-4"></center>
                           ${e['data']}
                           </article>
                        </div>`;
            $(`#content` + tab).html(html);
         }else{
            frown_alert(e['error_msg']);
         }
      },
      [{tab:tab, param:param}]
   );
}

function back_panduan(tab){
   var html = `<div class="row" id="content${tab}">
                  <input type="hidden" id="desc_${tab}" value="">
                  <div class="col-12 col-lg-12 my-3 ">
                     <ul class="list-group" id="${tab}">
                     </ul>
                  </div>
               </div>`;
   $('#content'+ tab).replaceWith(html);
   perjalanan_getData();
}

function edit_panduan(tab, part){
   ajax_x(
      baseUrl + "Panduan_manasik/get_detail_panduan",
      function(e) {
         if( e['error'] == false ){
            var formHtml = `<form action="${baseUrl }Panduan_manasik/addUpdatePanduanManasik" id="form_utama" class="formName">
                              <div class="col-12">
                                 <input type="hidden" name="tab" value="${tab}">
                                 <input type="hidden" name="part" value="${part}">
                                 <div class="form-group">
                                    <label>Artikel</label>
                                    <textarea class="form-control" id="artikel" name="artikel" rows="3" style="resize:none;" placeholder="Artikel">${e['data']}</textarea>
                                 </div>
                              </div>
                           </form>
                           <script>
                              CKEDITOR.replace('artikel');
                           </script>`;
            $.confirm({
               columnClass: 'col-10',
               title: 'Form Tambah Panduan',
               theme: 'material',
               type: 'blue',
               content: formHtml,
               closeIcon: false,
               buttons: {
                  cancel: function () {
                       return true;
                  },
                  formSubmit: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_ckeditor("#form_utama", 'artikel', function(e) {
                           $.alert({
                              title: 'Peringatan',
                              content: e['error_msg'],
                              type: e['error'] == true ? 'red' :'green'
                           });
                           // if ( e['error'] == true ) {
                           //    return false;
                           // } else {
                           //    get_daftar_artikel(20);
                           // }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },
      [{tab:tab, param:part}]
   );
}
