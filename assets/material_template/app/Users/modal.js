function modal_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarModal">
                  <div class="col-9 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="periode" id="periode" onChange="get_modal()">
                           <option value="0">Periode Sekarang</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:25%;">Modal Awal</th>
                              <th style="width:50%;" colspan="2"></th>
                              <th style="width:25%;" id="modal_awal">${kurs} 0</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_jurnal">
                           <tr>
                              <td style="width:25%;" ></td>
                              <td style="width:25%;" >Pembahan Modal</td>
                              <td style="width:25%;" id="penambahan_modal">${kurs} 0</td>
                              <td style="width:25%;" ></td>
                           </tr>
                           <tr>
                              <td></td>
                              <td>Ikhtisar Laba Rugi</td>
                              <td id="ikhtisar_laba_rugi">${kurs} 0</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td></td>
                              <td>Pengurangan Modal</td>
                              <td id="pengurangan_modal">${kurs} 0</td>
                              <td></td>
                           </tr>
                        </tbody>
                        <tfoot>
                           <tr>
                              <td><b>Modal Akhir</b></td>
                              <td colspan="2"></td>
                              <td id="modal_akhir"><b>${kurs} 0</b></td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" ></div>
               </div>
            </div>`;
}

function modal_getData(){
   ajax_x(
      baseUrl + "Modal/get_filter_modal", function(e) {
         if( e['error'] == false ) {
            var list_periode = '';
            for( y in e['list_periode'] ) {
               list_periode += `<option value="${y}">${e['list_periode'][y]}</option>`;
            }
            $('#periode').html(list_periode);
            get_modal();
         }
      },[]
   );
}

// get modal
function get_modal(){
   ajax_x(
      baseUrl + "Modal/daftar_modal", function(e) {
         if( e['error'] == false ) {

            var modal_awal = e['list']['modal_awal'];
            var penambahan_modal = e['list']['penambahan_modal'];
            var ikhtisar_laba_rugi = e['list']['ikhtisar_laba_rugi'];
            var pengurangan_modal = e['list']['pengurangan_modal'];
            var modal_akhir = e['list']['modal_akhir'];

            $('#modal_awal').html(`${kurs}` + numberFormat(modal_awal.toString()));
            $('#penambahan_modal').html(`${kurs}` + numberFormat(penambahan_modal.toString()));
            $('#ikhtisar_laba_rugi').html(`${kurs}` + numberFormat(ikhtisar_laba_rugi.toString()));
            $('#pengurangan_modal').html(`${kurs}` + numberFormat(pengurangan_modal.toString()));
            $('#modal_akhir').html(`${kurs}` + numberFormat(modal_akhir.toString()));
         }else{
            $('#modal_awal').html(`${kurs}0`);
            $('#penambahan_modal').html(`${kurs}0`);
            $('#ikhtisar_laba_rugi').html(`${kurs}0`);
            $('#pengurangan_modal').html(`${kurs}0`);
            $('#modal_akhir').html(`${kurs}0`);
         }
      },[{periode: $('#periode').val()}]
   );
}
