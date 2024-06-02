function riwayat_deposit_tabungan_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarAirlines">
                  <div class="col-6 col-lg-7 my-3 ">
                     <button class="btn btn-default" type="button" onClick="cetak_riwayat_deposit_tabungan()">
                        <i class="fas fa-print"></i> Cetak Riwayat Deposit & Tabungan
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-1 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" id="tipe_transaksi" onChange="get_riwayat_deposit_tabungan(20)">
                           <option value="semua">Semua</option>
                           <option value="tabungan_umrah">Tabungan Umrah</option>
                           <option value="deposit_saldo">Deposit Saldo</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-6 col-lg-1 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="date" onChange="get_riwayat_deposit_tabungan(20)" id="start_date" placeholder="Start Date" style="font-size: 12px;" title="Start Date">
                     </div>
                  </div>
                  <div class="col-6 col-lg-1 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="date" onChange="get_riwayat_deposit_tabungan(20)" id="end_date"  placeholder="End Date" style="font-size: 12px;" title="End Date">
                     </div>
                  </div>
                  <div class="col-6 col-lg-2 my-3 text-right">
                     <div class="input-group ">
                        <select class="form-control form-control-sm" id="member" onChange="get_riwayat_deposit_tabungan(20)">
                           <option value="0">-- Pilih Semua --</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:10%;">Nomor Transaksi</th>
                              <th style="width:10%;">Nama Member / Jamaah</th>
                              <th style="width:12%;">Biaya</th>
                              <th style="width:8%;">Status Biaya</th>
                              <th style="width:10%;">Penerima</th>
                              <th style="width:25%;">Info</th>
                              <th style="width:10%;">Status</th>
                              <th style="width:15%;">Input Transaksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_riwayat_deposit_tabungan">
                           <tr>
                              <td colspan="8">Riwayat deposit & transaksi tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_riwayat_deposit_tabungan"></div>
                  </div>
               </div>
            </div>`;
}


function riwayat_deposit_tabungan_getData(){
   ajax_x(
      baseUrl + "Riwayat_deposit_tabungan/get_list_member_deposit_tabungan", function(e) {
         if( e['error'] == false ){
            var html = `<option value="0">-- Pilih Semua --</option>`;
            for( x in e.data ){
               html += `<option value="${e.data[x].id}">${e.data[x].fullname} (ID : ${e.data[x].identity_number})</option>`;
            }
            $('#member').html(html);
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
   // get riwayat deposit tabungan
   get_riwayat_deposit_tabungan(20);
}

function get_riwayat_deposit_tabungan(perpage){
   var tipe_transaksi = $('#tipe_transaksi').val();
   var start_date = $('#start_date').val();
   var end_date = $('#end_date').val();
   var member = $('#member').val();
   get_data( perpage,
             { url : 'Riwayat_deposit_tabungan/daftar_riwayat_deposit_tabungan',
               pagination_id: 'pagination_riwayat_deposit_tabungan',
               bodyTable_id: 'bodyTable_riwayat_deposit_tabungan',
               fn: 'ListRiwayatDepositTabungan',
               warning_text: '<td colspan="8">Riwayat deposit & transaksi tidak ditemukan</td>',
               param : { tipe_transaksi: tipe_transaksi, start_date: start_date, end_date: end_date, member:member } } );
}

function ListRiwayatDepositTabungan(JSONData){
   var json = JSON.parse(JSONData);

   var biaya =  kurs + ' 0';
   var status_biaya = '';
   if( json.kredit != 0 ){
      biaya = kurs + ' ' + numberFormat(json.kredit);
      status_biaya = '<b>(KREDIT)</b>';
   }else{
      biaya = kurs + ' ' + numberFormat(json.debet);
      status_biaya = '<b>(DEBET)</b>';
   }

   var paket_tipe = '';
   if( json.tipe_transaksi == 'paket_deposit' ){
      paket_tipe = 'TABUNGAN UMRAH';
   }else if( json.tipe_transaksi == 'paket_payment' ){
      paket_tipe = 'TABUNGAN UMRAH<br>(Beli Paket)';
   }else{
      paket_tipe = 'DEPOSIT';
   }

   return `<tr>
               <td>${json.nomor_transaction}</td>
               <td>${json.fullname}<br>(ID : ${json.identity_number})</td>
               <td>${biaya}</td>
               <td>${status_biaya}</td>
               <td>${json.penerima}</td>
               <td>${json.info} ${ json.no_register != null ? '<br> NAMA PAKET : ' + json.paket_name + ' <br> ( No Trans Paket : ' + json.no_register + ')' : ''} </td>
               <td>${paket_tipe}</td>
               <td>${json.input_date}</td>
           </tr>`;
}

function cetak_riwayat_deposit_tabungan(){
   var tipe_transaksi = $('#tipe_transaksi').val();
   var start_date = $('#start_date').val();
   var end_date = $('#end_date').val();
   var member = $('#member').val();
   // filter
   ajax_x_t2(
      baseUrl + "Riwayat_deposit_tabungan/cetak_riwayat_deposit_tabungan", function(e) {
         if( e.error == false ) {
            window.open(baseUrl + "Kwitansi/", "_blank");
         }
      },[{ tipe_transaksi: tipe_transaksi, start_date: start_date, end_date: end_date, member:member }]
   );
}

// html += `<div class="col-12 col-lg-12">
//             <div class="form-group  ">
//                <label >Penyetor</label>
//                <select class="form-control form-control-sm" id="penyetorDeposit" name="penyetorDeposit">`;
//       for( x in e['data']['listpersonal'] ){
//          html +=   `<option value="${e['data']['listpersonal'][x]['id']}">${e['data']['listpersonal'][x]['name']}</option>`;
//       }
//       html += `</select>
//             </div>
//          </div>`;
// $('#infoPenyetor').html(html);