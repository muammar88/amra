function jurnal_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarJurnal">
                  <div class="col-5 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-2 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="date" onchange="get_jurnal(20)" id="tanggal" name="tanggal" placeholder="Tanggal" style="font-size: 12px;" onKeyup="get_jurnal(20)">
                     </div>
                  </div>
                  <div class="col-2 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="periode" id="periode" onChange="get_jurnal(20)">
                           <option value="0">Periode Sekarang</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-3 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" name="akun" id="akun" onChange="get_jurnal(20)">
                           <option value="0">Pilih Akun</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;" rowspan="2">Tanggal Transaksi</th>
                              <th style="width:15%;" rowspan="2">Ref</th>
                              <th style="width:15%;" rowspan="2">Keterangan</th>
                              <th style="width:40%;" colspan="4">Akun</th>
                              <th style="width:15%;" rowspan="2">Saldo</th>
                              <th style="width:5%;" rowspan="2">Aksi</th>
                           </tr>
                           <tr>
                              <th style="width:20%;" colspan="2">Debet</th>
                              <th style="width:20%;" colspan="2">Kredit</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_jurnal">
                           <tr>
                              <td colspan="9">Daftar jurnal tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_jurnal"></div>
                  </div>
               </div>
            </div>`;
}

function jurnal_getData(){
   ajax_x(
      baseUrl + "Jurnal/get_filter_daftar_jurnal", function(e) {
         if( e['error'] == false ) {
            var list_akun = '';
            for( x in e['list_akun'] ) {
               list_akun += `<option value="${x}">${e['list_akun'][x]}</option>`;
            }
            var list_periode = '';
            for( y in e['list_periode'] ) {
               list_periode += `<option value="${y}">${e['list_periode'][y]}</option>`;
            }
            $('#akun').html(list_akun);
            $('#periode').html(list_periode);

            get_jurnal(20);
         }
      },[]
   );
}

function get_jurnal(perpage){
   get_data( perpage,
             { url : 'Jurnal/daftar_jurnal',
               pagination_id: 'pagination_jurnal',
               bodyTable_id: 'bodyTable_jurnal',
               fn: 'ListJurnal',
               warning_text: '<td colspan="9">Daftar jurnal tidak ditemukan</td>',
               param : { tanggal : $('#tanggal').val(),
                         akun: $('#akun').val(),
                         periode: $('#periode').val() } } );
}

function ListJurnal(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.input_date}</td>
                  <td>${json.ref}</td>
                  <td>${json.ket}</td>
                  <td>${json.akun_debet}</td>
                  <td>${json.nama_akun_debet}</td>
                  <td>${json.akun_kredit}</td>
                  <td>${json.nama_akun_kredit}</td>
                  <td>${kurs} ${numberFormat(json.saldo)}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Delete jurnal"
                        onclick="delete_jurnal('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}


function delete_jurnal(id){
   $.confirm({
      title: 'Peringatan',
      theme: 'material',
      columnClass: 'col-4',
      content: "Apakah anda yakin ingin menghapus transaksi jurnal ini?.",
      closeIcon: false,
      buttons: {
         cancel: function () {
              return true;
         },
         formSubmit: {
            text: 'Simpan',
            btnClass: 'btn-blue',
            action: function () {
               ajax_x(
                  baseUrl + "Jurnal/delete_jurnal", function(e) {
                     if( e['error'] == false ){
                        get_jurnal(20);
                     }
                     $.alert({
                        icon: e['error'] == true ? 'far fa-frown' : 'far fa-smile',
                        title: 'Peringatan',
                        content: e['error_msg'],
                        type: e['error'] == true ? 'red' : 'blue',
                     });
                  },[{id:id}]
               );
            }
         }
      }
   });
}
