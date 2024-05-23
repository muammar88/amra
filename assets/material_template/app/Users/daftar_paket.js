function daftar_paket_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarPaket">
                  <div class="col-4 col-lg-6 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_paket()" title="Tambah paket baru">
                        <i class="fas fa-box"></i> Tambah Paket Baru
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-4 col-lg-2 my-3 text-right">
                     <div class="form-group">
                        <select class="form-control form-control-sm" name="status_keberangkatan" id="status_keberangkatan" onChange="get_daftar_paket(20)" title="Status Keberangkatan">
                           <option value="belum_berangkat">Belum Berangkat</option>
                           <option value="sudah_berangkat">Sudah Berangkat</option>
                           <option value="semua">Pilih Semua</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-12 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_paket(20)" id="searchDaftarPaket" name="searchDaftarPaket" placeholder="Kode Paket/Nama Paket" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_paket(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:18%;">Nama Paket</th>
                              <th style="width:20%;">Harga</th>
                              <th style="width:15%;">Deskripsi</th>
                              <th style="width:12%;">Tgl. Berangkat</th>
                              <th style="width:12%;">Tgl. Kembali</th>
                              <th style="width:9%;">Total <br>Jamaah</th>
                              <th style="width:14%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody class="bodyTable" id="bodyTable_daftar_paket">
                           <tr>
                              <td colspan="7">Daftar paket tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_paket"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_paket_getData(){
   get_daftar_paket(20);
}

function get_daftar_paket(perpage){
   get_data( perpage,
             { url : 'Daftar_paket/daftar_pakets',
               pagination_id: 'pagination_daftar_paket',
               bodyTable_id: 'bodyTable_daftar_paket',
               fn: 'ListDaftarPaket',
               warning_text: '<td colspan="7">Daftar paket tidak ditemukan</td>',
               param : { search : $('#searchDaftarPaket').val(), status_keberangkatan : $('#status_keberangkatan').val() } } );
}

function ListDaftarPaket(JSONData){
   var json = JSON.parse(JSONData);

   var harga = `<ul class="pl-3 list">`;
      for( x in json.paket_type){
         harga += `<li>${json.paket_type[x]['paket_type_name']} : Rp ${numberFormat(json.paket_type[x]['price'])}</li>`;
      }

   var html = `<tr>
                  <td><b>${json.kode}</b> : <b>${json.jenis_kegiatan.toUpperCase()}</b> <br> ${json.paket_name} </td>
                  <td>${harga}</td>
                  <td><p align="justify">${json.description}</p></td>
                  <td>${json.departure_date} <br> ${json.status_keberangkatan}</td>
                  <td>${json.return_date}</td>
                  <td>${json.jumlah_jamaah} Orang</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Paket"
                        onclick="beli_paket('${json.id}', 'daftar_paket')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-exchange-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Edit Paket"
                        onclick="edit_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Paket"
                        onclick="delete_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}


function formaddupdate_paket(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   var kode_paket = json.kode;
   var paket_type = JSON.stringify(json.paket_type);
   var fasilitas = JSON.stringify(json.fasilitas);
   var airlines = JSON.stringify(json.airlines);
   var hotel = JSON.stringify(json.hotel);
   var kota = JSON.stringify(json.kota_kunjungan);
   var muthawif = JSON.stringify(json.muthawif);
   var bandara = JSON.stringify(json.bandara);
   var jenis_kegiatan = JSON.stringify([{id: 'haji', name:"Haji"}, {id:'umrah', name:"Umrah"}, {id:'haji_umrah',  'name':"Haji dan Umrah"}]);
   var provider_visa_list = JSON.stringify(json.provider_visa);
   var asuransi_list = JSON.stringify(json.asuransi);

   var paket_id = '';
   var photo = '';
   var nama_paket = '';
   var jenis_kegiatan_selected = '';
   var deskripsi = '';
   var tanggal_keberangkatan = '';
   var tanggal_kepulangan = '';
   var biaya_mahram = '';
   var quota_jamaah = '';
   var berangkat_dari = '';
   var kota_selected = '';
   var fasilitas_value = new Array();
   var show = '';
   var airlines_value = new Array();
   var hotel_value = new Array();
   var kota_value = new Array();
   var bandara_keberangkatan = '';
   var bandara_tujuan = '';
   var waktu_keberangkatan = '';
   var waktu_sampai = '';

   var provider_visa_selected = '';
   var asuransi_selected = '';
   var no_polis = '';
   var tgl_input_polis = '';
   var tgl_awal_polis = '';
   var tgl_akhir_polis = '';

   var itinerary = [];
   var paket_price = [];

   var paket_muthawif = [];

   var fee_agen = 0;
   var fee_cabang = 0;
   if( JSONValue != undefined ){
      var value = JSON.parse(JSONValue);
      paket_id = `<input type="hidden" name="id"  value="${value.id}">`;
      jenis_kegiatan_selected = value.jenis_kegiatan;
      kode_paket = value.kode
      photo = value.photo;
      nama_paket = value.paket_name;
      deskripsi = value.description;
      tanggal_keberangkatan = value.departure_date;
      tanggal_kepulangan = value.return_date;
      berangkat_dari = value.departure_from;
      biaya_mahram = 'Rp ' + numberFormat(value.mahram_fee);
      quota_jamaah = value.jamaah_quota;
      kota_value = value.city_visited;
      airlines_value = value.airlines;
      hotel_value = value.hotel;
      fasilitas_value = value.facilities;
      show = value.show_homepage == 1 ? 'checked': '';
      waktu_keberangkatan = value.departure_time;
      waktu_sampai = value.time_arrival;
      bandara_keberangkatan = value.airport_departure;
      bandara_tujuan = value.airport_destination;

      provider_visa_selected = value.provider_id;
      asuransi_selected = value.asuransi_id;
      no_polis = value.no_polis;
      tgl_input_polis = value.tgl_input_polis;
      tgl_awal_polis = value.tgl_awal_polis;
      tgl_akhir_polis = value.tgl_akhir_polis;

      paket_price = value.paket_price;
      paket_muthawif = value.paket_muthawif;

      fee_agen = value.fee_agen;
      fee_cabang = value.fee_cabang;

      itinerary = value.itinerary;
   }

   var html =  `<div class="col-lg-12 mt-0 pt-0" >
                  <form class="py-2" action="${baseUrl }Daftar_paket/proses_addupdate_paket" id="form_utama_big" class="formName" onsubmit="proses_addupdate_paket(event)">
                     <div class="row mb-3">
                        ${submitBtnPaket()}
                     </div>
                     <div class="row">
                        <div class="col-lg-8">
                           <div class="row">
                              <div class="col-lg-2">
                                 <div class="form-group form-group-input row">
                                    <label class="col-sm-12 col-form-label">Kode Paket</label>
                                    <div class="col-sm-12">
                                       <input type="text" class="form-control form-control-sm" value="${kode_paket}" readonly style="font-weight: bold;">
                                       <input type="hidden" name="kode_paket" id="kode_paket" value="${kode_paket}">
                                       ${paket_id}
                                    </div>
                                 </div>
                              </div>
                              <div class="col-lg-5">
                                 ${uploadImageForm('Photo', 'photo', photo, '<span class="red">*</span>', 'paket')}
                              </div>
                              <div class="col-12 col-lg-5">
                                 ${inputTextForm('Nama Paket', 'nama_paket', nama_paket, ' required', '<span class="red">*</span>')}
                              </div>
                              <div class="col-lg-2">
                                 ${selectForm('Jenis Kegiatan', 'jenis_kegiatan', jenis_kegiatan, '', jenis_kegiatan_selected ,'py-1')}
                              </div>
                              <div class="col-12 col-lg-5">
                                 ${selectForm('Provider Visa', 'provider_visa', provider_visa_list, '', provider_visa_selected ,'py-1')}
                              </div>
                              <div class="col-12 col-lg-5">
                                 ${selectForm('Asuransi', 'asuransi', asuransi_list, '', asuransi_selected ,'py-1')}
                              </div>
                               <div class="col-12 col-lg-3">
                                 ${inputTextForm('NoPolis', 'nopolis', no_polis, ' ', '')}
                              </div>
                              <div class="col-12 col-lg-3">
                                 ${inputDateForm('Tgl Input Polis', 'tgl_input_polis', tgl_input_polis, ' ', '')}
                              </div>
                              <div class="col-12 col-lg-3">
                                 ${inputDateForm('Tgl Awal Polis', 'tgl_awal_polis', tgl_awal_polis, ' ', '')}
                              </div>
                              <div class="col-12 col-lg-3">
                                 ${inputDateForm('Tgl Akhir Polis', 'tgl_akhir_polis', tgl_akhir_polis, ' ', '')}
                              </div>
                           </div>
                        </div>
                        <div class="col-4" >
                           ${textAreaFrom('Deskripsi Paket', 'deskripsi_paket', deskripsi, 'Deskripsi Paket', 'rows="11"')}
                        </div>
                        <div class="col-4">
                           ${formListTipePaket('Daftar Tipe Paket', paket_type, JSON.stringify(paket_price))}
                        </div>
                        <div class="col-8">
                           <div class="row">
                              <div class="col-lg-4">
                                 ${inputDateForm('Tanggal Keberangkatan', 'tgl_keberangkatan', tanggal_keberangkatan)}
                              </div>
                              <div class="col-lg-4">
                                 ${inputDateForm('Tanggal Kepulangan', 'tgl_kepulangan', tanggal_kepulangan)}
                              </div>
                              <div class="col-lg-4">
                                 ${currencyForm('Biaya Mahram', 'biaya_mahram', biaya_mahram)}
                              </div>
                              <div class="col-lg-3">
                                 ${numberForm('Quota Jamaah', 'quota', quota_jamaah )}
                              </div>
                              <div class="col-lg-3">
                                 ${selectForm('Berangkat Dari', 'berangkat_dari', kota, '', berangkat_dari,'py-1')}
                              </div>
                              <div class="col-lg-6">
                                 ${multipleCheckbox('Fasilitas Paket', 'fasilitas', fasilitas, '6', JSON.stringify(fasilitas_value))}
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-9">
                           <div class="row">
                              <div class="col-lg-3">
                                 <fieldset class="form-group row">
                                    <label class="col-sm-12 col-form-label">Tampilkan Paket Dihalaman Depan</label>
                                    <div class="col-lg-12">
                                       <div class="form-check">
                                          <label class="form-check-label">
                                             <input class="form-check-input" type="checkbox" value="1" name="show" ${show}>
                                             Tampilkan
                                          </label>
                                       </div>
                                    </div>
                                 </fieldset>
                              </div>
                              <div class="col-lg-3">
                                 ${multipleCheckbox('Airlines', 'airlines', airlines, '12', JSON.stringify(airlines_value))}
                              </div>
                              <div class="col-lg-3">
                                 ${multipleCheckbox('Hotel', 'hotel', hotel, '12', JSON.stringify(hotel_value))}
                              </div>
                              <div class="col-lg-3">
                                 ${multipleCheckbox('Kota Kunjungan', 'kota', kota, '12', JSON.stringify(kota_value))}
                              </div>
                              <div class="col-lg-3">
                                 ${selectForm('Bandara Keberangkatan', 'bandara_asal', bandara, '', bandara_keberangkatan, 'py-1')}
                              </div>
                              <div class="col-lg-3">
                                 ${selectForm('Bandara Tujuan', 'bandara_tujuan', bandara, '', bandara_tujuan, 'py-1')}
                              </div>
                              <div class="col-lg-3">
                                 ${inputDateTimeForm('Waktu Keberangkatan', 'waktu_keberangkatan', waktu_keberangkatan)}
                              </div>
                              <div class="col-lg-3">
                                 ${inputDateTimeForm('Waktu Sampai', 'waktu_sampai', waktu_sampai )}
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-3">
                           <div class="row">
                              ${formAddMuthawifPaket('Daftar Muthawif', muthawif, JSON.stringify(paket_muthawif))}
                           </div>
                        </div>
                        <div class="col-lg-12">
                           <div class="row">
                              <div class="col-12 col-lg-12 px-3 " >
                                 <div class="form-group form-group-input row">
                                    <label for="exampleSelect1" class="col-sm-12 col-form-label">Itinerary</label>
                                    <div class="col-sm-12 px-0" id="listItinerary">
                                       ${formAddItinerary(JSON.stringify(itinerary))}
                                    </div>
                                    <div class="col-sm-12 px-0 pt-2" >
                                       <button type="button" class="btn btn-default btn-action" title="Delete" onclick="tambahItinerary()" style="width:100%;">
                                          <i class="fas fa-plus" style="font-size: 11px;"></i> Tambah Rencana Perjalanan
                                       </button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        ${submitBtnPaket()}
                     </div>
                  </form>
                  <script>
                     $(document).on( "keyup", ".currency", function(e){
                         var e = window.event || e;
                         var keyUnicode = e.charCode || e.keyCode;
                             if (e !== undefined) {
                                 switch (keyUnicode) {
                                     case 16: break;
                                     case 27: this.value = ''; break;
                                     case 35: break;
                                     case 36: break;
                                     case 37: break;
                                     case 38: break;
                                     case 39: break;
                                     case 40: break;
                                     case 78: break;
                                     case 110: break;
                                     case 190: break;
                                     default: $(this).formatCurrency({ colorize: true, negativeFormat: '-%s%n', roundToDecimalPlace: -1, eventOnDecimalsEntered: true });
                                 }
                             }
                     } );
                  </script>
               </div>`;
   return html;
}

function proses_addupdate_paket(e){
   ajax_submit(e, "#form_utama_big", function(e) {
      if( e['error'] != true ){
         menu( this, 'daftar_paket', 'Paket & Paket LA', 'fas fa-box-open', '', 'submodul');
      }
      e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
	});
}

function add_paket(){
   ajax_x(
      baseUrl + "Daftar_paket/get_info_paket", function(e) {
         if( e['error'] == false ){
             $('#contentDaftarPaket').html(formaddupdate_paket(JSON.stringify(e['data'])));
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}

function submitBtnPaket(){
   return  `<div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
               <div class="row">
                  <div class="col-12">
                     <button type="button" class="btn btn-default" onclick="menu( this, 'daftar_paket', 'Paket & Paket LA', 'fas fa-box-open', '', 'submodul')">Batal</button>
                     <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
               </div>
            </div>`;
}

// function textAreaFrom(label, name, value, placeholder){
//    return  `<div class="form-group form-group-input row">
//                <label class="col-sm-12 col-form-label">${label}</label>
//                <textarea class="form-control form-control-sm" name="${name}" placeholder="${placeholder}" rows="7" style="resize: none;">${value}</textarea>
//             </div>`;
// }

function formListTipePaket(label, JSONdata, valueData){
   var value = [];
   if( valueData != undefined ) {
      value = JSON.parse(valueData);
   }
   var data = JSON.parse(JSONdata);
   var html = `<fieldset class="form-group row">
                  <label for="staticEmail" class="col-sm-12 col-form-label">${label}</label>`;
      for( x in data ) {
         html += `<div class="col-lg-4">
                     <div class="form-check">
                        <label class="form-check-label">
                           <input class="form-check-input" name="tipe_paket[${data[x]['id']}]" type="checkbox"
                              value="${data[x]['id']}" ${value[data[x]['id']] != undefined ? ' checked ': ''} >${data[x]['name']}
                        </label>
                     </div>
                  </div>
                  <div class="col-lg-8">
                     <div class="form-group form-group-input row">
                        <input type="text" name="paket_type_price[${data[x]['id']}]"
                              placeholder="Biaya Paket ${data[x]['name']}"
                              class="form-control form-control-sm currency"
                              value="${value[data[x]['id']] != undefined ? 'Rp ' + numberFormat(value[data[x]['id']]) : 'Rp 0'}" />
                     </div>
                  </div>`;
      }
      html +=`</fieldset>`;
   return html;
}

function tambahMuthawif(){
   var listMuthawif = JSON.parse($('#jsonMuthawif').val());
   var html = `<div class="row" >
                  <div class="col-sm-10 py-1">
                     <select class="form-control form-control-sm muthawif" name="muthawif[]">
                        <option value="0">Pilih Muthawif</option>`;
         for( x in listMuthawif ){
               html += `<option value="${listMuthawif[x]['id']}">${listMuthawif[x]['name']}</option>`;
         }
      html +=       `</select>
                  </div>
                  <div class="col-sm-2 py-1 px-0 pr-2 text-right">
                     <button type="button" class="btn btn-default btn-action" title="Delete Muthawif" onclick="deleteMuthawif(this)">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </div>
               </div>`;
   $('#listMuthawif' ).append( html );
}

function deleteMuthawif(e){
   if( $('.muthawif').length > 1 ){
      $(e).parent().parent().remove();
   }else{
      frown_alert('Anda setidaknya wajib menyisakan minimal 1 orang muthawif');
   }
}

function tambahItinerary(){
   $('#listItinerary' ).append( formAddItinerary( JSON.stringify([]) ) );
}

function formAddItinerary(JSONdata){
   var html = '';
   if(JSONdata != undefined ){
      var data = JSON.parse(JSONdata);
      if( data.length > 0 ){
         for ( x in data ){
            html  +=`<div class="row itineraryClass" >
                        <div class="col-sm-3 py-1">
                           <div class="form-group">
                              <input type="datetime-local" name="tanggal_aktifitas[]" placeholder="Tanggal Aktifitas"
                                 class="form-control form-control-sm" value="${data[x]['activity_date']}" />
                           </div>
                        </div>
                        <div class="col-sm-4 py-1">
                           <div class="form-group">
                              <input type="text" name="judul_aktifitas[]" placeholder="Judul Aktifitas"
                                 class="fullname form-control form-control-sm" value="${data[x]['activity_title']}" />
                           </div>
                        </div>
                        <div class="col-sm-4 py-1">
                           <div class="form-group">
                              <textarea name="deskripsi_aktifitas[]" class="form-control form-control-sm" rows="2"
                                 style="resize: none;" placeholder="Deskripsi">${data[x]['description']}</textarea>
                           </div>
                        </div>
                        <div class="col-sm-1 py-1 px-0 pr-2 text-right">
                           <button type="button" class="btn btn-default btn-action" title="Delete" onclick="deleteItinerary(this)">
                              <i class="fas fa-times" style="font-size: 11px;"></i>
                           </button>
                        </div>
                     </div>`;
         }
      }else{
         html  +=`<div class="row itineraryClass" >
                     <div class="col-sm-3 py-1">
                        <div class="form-group">
                           <input type="datetime-local" name="tanggal_aktifitas[]" placeholder="Tanggal Aktifitas"
                              class="form-control form-control-sm" />
                        </div>
                     </div>
                     <div class="col-sm-4 py-1">
                        <div class="form-group">
                           <input type="text" name="judul_aktifitas[]" placeholder="Judul Aktifitas"
                              class="fullname form-control form-control-sm" />
                        </div>
                     </div>
                     <div class="col-sm-4 py-1">
                        <div class="form-group">
                           <textarea name="deskripsi_aktifitas[]" class="form-control form-control-sm" placeholder="Deskripsi" rows="2" style="resize: none;"></textarea>
                        </div>
                     </div>
                     <div class="col-sm-1 py-1 px-0 pr-2 text-right">
                        <button type="button" class="btn btn-default btn-action" title="Delete" onclick="deleteItinerary(this)">
                           <i class="fas fa-times" style="font-size: 11px;"></i>
                        </button>
                     </div>
                  </div>`;
      }
   }
   return html;
}

function deleteItinerary(e){
   if( $('.itineraryClass').length > 1 ){
      $(e).parent().parent().remove();
   }else{
      frown_alert('Anda setidaknya wajib menyisakan minimal 1 orang itinerary');
   }
}

// delete paket
function delete_paket(id){
   $.confirm({
      columnClass: 'col-4',
      title: 'Peringatan',
      theme: 'material',
      content: 'Jika anda menghapus paket, berarti anda juga ikut menghapus semua data yang bersangkutan dengan paket, seperti transaksi yang terjadi pada paket ini. Apakah ingin melanjutkan proses ini?.',
      closeIcon: false,
      buttons: {
         cancel:function () {
             return true;
         },
         lanjutkan: {
            text: 'Lanjutkan',
            btnClass: 'btn-red',
            action: function () {
               ajax_x(
                  baseUrl + "Daftar_paket/delete_paket", function(e) {
                     if( e['error'] == false ){
                        get_daftar_paket(20);
                    }
                    e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                  },[{id:id}]
               );
            }
         }
      }
   });
}

function edit_paket(id){
   ajax_x(
      baseUrl + "Daftar_paket/get_info_edit_paket", function(e) {
         if( e['error'] == false ){
             $('#contentDaftarPaket').html(formaddupdate_paket(JSON.stringify(e['data']), JSON.stringify(e['value'])));
         }else{
            frown_alert(e['error_msg']);
         }
      },[{id:id}]
   );
}
