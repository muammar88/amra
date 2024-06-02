function deposit_paket_Pages() {
  return `<div class="col-6 col-lg-6 my-3">
               <button class="btn btn-default" type="button" onclick="start_deposit_paket()">
                  <i class="fas fa-box-open"></i> Tambungan Umrah
               </button>
               <button class="btn btn-default mx-3" type="button" onclick="download_excel_data_jamaah()">
                  <i class="fas fa-print"></i> Download Excel Data Jamaah
               </button>
               <label class="float-right py-2 my-0">Filter :</label>
            </div>
            <div class="col-6 col-lg-3 my-3">
               <select class="form-control form-control-sm filter" id="filter_transaksi" name="filter_transaksi" onChange="deposit_paket_getData()">
                  <option value="belum">Belum Beli Paket</option>
                  <option value="sudah">Sudah Beli Paket</option>
                  <option value="batal">Batal Berangkat</option>
               </select>
            </div>   
            <div class="col-6 col-lg-3 my-3">   
               <div class="input-group">
                  <input class="form-control form-control-sm" type="text" onkeyup="get_deposit_paket(20)"
                     id="searchJamaahDepositPaket" name="searchJamaahDepositPaket" placeholder="Nama / Nomor Identitas Jamaah"
                     style="font-size: 12px;">
                  <div class="input-group-append">
                     <button class="btn btn-default" type="button" onclick="get_deposit_paket(20)">
                        <i class="fas fa-search"></i> Cari
                     </button>
                  </div>
               </div>
            </div>
            <div class="col-lg-12">
               <table class="table table-hover">
                  <thead>
                     <tr>
                        <th style="width:35%;">Info Jamaah</th>
                        <th style="width:50%;">Info Deposit</th>
                        <th style="width:15%;">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="bodyTable_deposit_paket">
                     <tr>
                        <td colspan="3">Daftar Tabungan Umrah jamaah tidak ditemukan</td>
                     </tr>
                  </tbody>
                </table>
            </div>
            <div class="col-lg-12 px-3 pb-3" >
               <div class="row" id="pagination_deposit_paket"></div>
            </div>`;
}

function deposit_paket_getData() {
  get_deposit_paket(20);
}

function get_deposit_paket(perpage) {
  get_data(perpage, {
    url: "Deposit_paket/daftar_deposit_paket",
    pagination_id: "pagination_deposit_paket",
    bodyTable_id: "bodyTable_deposit_paket",
    fn: "ListRiwayatDepositPaket",
    warning_text:
      '<td colspan="3">Daftar tabungan umrah jamaah tidak ditemukan</td>',
    param: {
      search: $("#searchJamaahDepositPaket").val(),
      filterTransaksi: $("#filter_transaksi option:selected").val(),
    },
  });
}

function ListRiwayatDepositPaket(JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<tr>
                  <td>
                     <table class="table table-hover mb-0">
                        <tbody>
                           <tr>
                              <td class="text-left" style="width:40%;">NAMA JAMAAH</td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" style="width:59%;">${json.fullname}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >NOMOR IDENTITAS</td>
                              <td class="px-0">:</td>
                              <td class="text-left" >${json.identity_number}</td>
                           </tr>
                           <tr>
                              <td class="text-left" >TEMPAT / TGL LAHIR</td>
                              <td class="px-0" >:</td>
                              <td class="text-left " >${json.birth_place} / ${ json.birth_date }</td>
                           </tr>
                           <tr>
                              <td class="text-left border-0" >NAMA AGEN</td>
                              <td class="px-0 border-0" >:</td>
                              <td class="text-left border-0" >${json.agen}<br>(Level : ${json.level_agen})</td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
                  <td>
                      <table class="table table-hover ">
                         <tbody>
                           <tr>
                              <td class="text-left" colspan="3" style="width:40%;"><b>TOTAL TABUNGAN UMRAH</b></td>
                              <td class="px-0" style="width:1%;">:</td>
                              <td class="text-left" colspan="3" style="width:59%;">${kurs} ${numberFormat(json.total_deposit)},-</td>
                           </tr>
                           <tr>
                              <td class="text-center" colspan="7" style="background-color: #e7e7e7;"><b>RIWAYAT TABUNGAN UMRAH</b></td>
                           </tr>
                           <tr>
                              <td class="text-center" style="width:5%;">#</td>
                              <td class="text-center" style="width:10%;"><b>INVOICE</b></td>
                              <td class="text-center" style="width:21%;" colspan="2"><b>BIAYA</b></td>
                              <td class="text-center" style="width:34%;"><b>TANGGAL TRANSAKSI</b></td>
                              <td class="text-center" style="width:25%;"><b>PENERIMA</b></td>
                              <td class="text-center" style="width:5%;"><b>AKSI</b></td>
                           </tr>`;
            var num = 1;
                for (x in json.list_deposit) {
                  html +=`<tr>
                            <td class="text-center" >${num}</td>
                            <td class="text-center" >${json.list_deposit[x].invoice}</td>
                            <td class="text-center px-0"  colspan="2">${kurs} ${numberFormat(json.list_deposit[x].biaya)},- ${json.list_deposit[x].transaction_status == 'refund' ? `<br><b style="color:red">(REFUND)</b>` : ''}</td>
                            <td class="text-center" >${json.list_deposit[x].date_transaction}</td>
                            <td class="text-center" >${json.list_deposit[x].penerima}</td>
                            <td class="text-center" >`;

                    if( json.list_deposit[x].transaction_status == 'refund'  ) {
                      html +=`<button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Refund Tabungan"
                                   onclick="cetak_kwitansi_refund_tabungan('${json.list_deposit[x].invoice}')" style="margin:.15rem .1rem  !important">
                                <i class="fas fa-print" style="font-size: 11px;"></i>
                              </button>`;
                    }else{
                      html +=`<button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Deposit"
                                   onclick="cetak_kwitansi_deposit_paket('${json.list_deposit[x].invoice}')" style="margin:.15rem .1rem  !important">
                                <i class="fas fa-print" style="font-size: 11px;"></i>
                              </button>`;
                    }
                             
                  html +=`</td>
                          </tr>`;
                  num++;
                }
                html +=`<tr>
                          <td class="text-center" colspan="7" style="background-color: #e7e7e7;"><b>RIWAYAT HANDOVER FASILITAS</b></td>
                        </tr>
                        <tr>
                           <td class="text-center px-2" colspan="7">
                              <div class="row">`;
                      if (json.riwayat_handover.length > 0) {
                        html += `<table class="table table-hover ">
                                    <tr>
                                      <td class="text-center" style="width:10%;"><b>INVOICE</b></td>
                                      <td class="text-center" style="width:86%;"><b>FASILITAS</b></td>
                                      <td class="text-center" style="width:4%;"><b>Aksi</b></td>
                                    </tr>`;
                        for (z in json.riwayat_handover) {
                          var riwayat = json.riwayat_handover[z].fasilitas;
                           html += `<tr>
                                      <td><b>${json.riwayat_handover[z].invoice}</b><br>${json.riwayat_handover[z].date_transaction_1}<br>${json.riwayat_handover[z].date_transaction_2}</td>
                                      <td class="text-left">`;
                              for (r in riwayat) {
                                html += `<span class="badge bg-info py-2 px-2 m-1" style="font-size:10px;background-color: #2d3b74!important;">#${riwayat[r]}</span>`;
                              }
                             html += `</td>
                                      <td>
                                        <button type="button" class="btn btn-default btn-action" title="Cetak Kwitansi Handover"
                                          onclick="cetak_kwitansi_handover_deposit_paket('${json.riwayat_handover[z].invoice}')" style="margin:.15rem .1rem  !important">
                                          <i class="fas fa-print" style="font-size: 11px;"></i>
                                        </button>`;
                            if (json.active == "active") {
                              html += `<button type="button" class="btn btn-default btn-action" title="Delete Transaksi Handover Fasilitas Tabungan Umrah"
                                          onclick="delete_transaksi_handover_fasilitas_deposit_paket('${json.riwayat_handover[z].invoice}')" style="margin:.15rem .1rem  !important">
                                          <i class="fas fa-times" style="font-size: 11px;"></i>
                                       </button>`;
                            }
                              html += `</td>
                                    </tr>`;
                        }
                        html += `</table>`;
                      } else {
                        html += `<div class="col-12"><center>Daftar Handover Fasilitas Tidak Ditemukan</center></div>`;
                      }
                    html += `</div>
                           </td>
                        </tr>
                        </tbody>
                      </table>
                  </td>
                  <td>`;

        if (json.active == "active") {
          html += `<button type="button" class="btn btn-default btn-action" title="Refund Tabungan Paket"
                      onclick="refund_tabungan_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-hand-holding-usd" style="font-size: 11px;"></i>
                   </button>
                   <button type="button" class="btn btn-default btn-action" title="Pembayaran"
                      onclick="pembayaran_deposit_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-money-bill-alt" style="font-size: 11px;"></i>
                   </button>
                   <button type="button" class="btn btn-default btn-action" title="Handover Fasilitas"
                      onclick="handover_barang_deposit_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-handshake" style="font-size: 11px;"></i>
                   </button>
                   <button type="button" class="btn btn-default btn-action" title="Delete Tabungan Umrah"
                      onclick="delete_deposit_paket('${json.id}')" style="margin:.15rem .1rem  !important">
                       <i class="fas fa-times" style="font-size: 11px;"></i>
                   </button>`;
        } else {
          html += `<center>Tabungan Sudah Digunakan</center>`;
        }
          html += `</td>
               </tr>`;
  return html;
}


function refund_tabungan_paket(id){
  ajax_x_t2(
    baseUrl + "Deposit_paket/get_info_refund",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          columnClass: "col-4",
          title: "Refund Tabungan",
          theme: "material",
          content: formRefundTabungan( id, JSON.stringify(e.data) ),
          closeIcon: false,
          buttons: {
            cancel: function () {
              return true;
            },
            simpan: {
              text: "Refund Tabungan",
              btnClass: "btn-blue",
              action: function () {
                var total_tabungan = hide_currency( $('#total_tabungan').val() );
                var refund = hide_currency( $('#refund').val() );
                // check refund
                if( total_tabungan >= refund ) {
                  ajax_submit_t1("#form_utama", function (e) {
                    e["error"] == true ? frown_alert(e["error_msg"]) : smile_alert(e["error_msg"]);
                    if (e["error"] == true) {
                      return false;
                    } else {
                      window.open(baseUrl + "Kwitansi/", "_blank");
                      get_deposit_paket(20);
                    }
                  });
                }else{
                  frown_alert('Refund tidak boleh lebih besar dari total tabungan.');
                  return false;
                }
              },
            },
          },
        });
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [ {id:id}]
  );
}

function formRefundTabungan(id, JSONData){
  var json = JSON.parse(JSONData);
  var html = `<form action="${baseUrl}Deposit_paket/refund_tabungan" id="form_utama" class="formName">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Invoice</label>
                                 <input type="text"  value="${json.nomor_transaksi}" class="form-control form-control-sm" readonly/>
                                 <input type="hidden" name="nomor_transaksi" id="nomor_transaksi" value="${json.nomor_transaksi}">
                                 <input type="hidden" name="id" id="id" value="${id}">
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Total Tabungan</label>
                                 <input type="text" class="form-control form-control-sm" value="${kurs} ${numberFormat(json.total_tabungan)}" placeholder="Total Tabungan Umrah" readonly/>
                                 <input type="hidden" id="total_tabungan" value="${json.total_tabungan}">
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Refund</label>
                                 <input type="text" name="refund" id="refund" class="form-control form-control-sm currency" placeholder="Refund Tabungan Umrah" required/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                <label>Batal Berangkat Umrah</label>
                                <div class="form-check">
                                  <input class="form-check-input" type="checkbox" value="batal" name="batal_berangkat" id="batalBerangkatUmrah">
                                  <label class="form-check-label" for="batalBerangkatUmrah">
                                    Batal Berangkat Umrah
                                  </label>
                                </div>
                              </div>
                           </div>
                        </div>
                     </div>
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
               </script>`;
  return html;
}

function download_excel_data_jamaah() {
  ajax_x_t2(
    baseUrl + "Deposit_paket/download_manifest",
    function (e) {
      if (e["error"] == false) {
        window.open(baseUrl + "Download/", "_blank");
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [
      {
        search: $("#searchJamaahDepositPaket").val(),
        filterTransaksi: $("#filter_transaksi option:selected").val(),
      },
    ]
  );
}

function cetak_kwitansi_refund_tabungan(invoice){
  ajax_x(
    baseUrl + "Deposit_paket/cetak_kwitansi_refund_tabungan",
    function (e) {
      if (e["error"] == false) {
        window.open(baseUrl + "Kwitansi/", "_blank");
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ invoice: invoice }]
  );
}

function cetak_kwitansi_deposit_paket(invoice) {
  ajax_x(
    baseUrl + "Deposit_paket/cetak_kwitansi_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        window.open(baseUrl + "Kwitansi/", "_blank");
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ invoice: invoice }]
  );
}

function cetak_kwitansi_handover_deposit_paket(invoice) {
  ajax_x(
    baseUrl + "Deposit_paket/cetak_kwitansi_handover_fasilitas_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        window.open(baseUrl + "Kwitansi/", "_blank");
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ invoice: invoice }]
  );
}

function start_deposit_paket() {
  ajax_x(
    baseUrl + "Deposit_paket/get_info_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          columnClass: "col-4",
          title: "Form Transaksi Tabungan Umrah",
          theme: "material",
          content: formaddupdate_transaksi_deposit_paket(
            JSON.stringify(e["data"])
          ),
          closeIcon: false,
          buttons: {
            cancel: function () {
              return true;
            },
            simpan: {
              text: "Simpan",
              btnClass: "btn-blue",
              action: function () {
                ajax_submit_t1("#form_utama", function (e) {
                  e["error"] == true
                    ? frown_alert(e["error_msg"])
                    : smile_alert(e["error_msg"]);
                  if (e["error"] == true) {
                    return false;
                  } else {
                    window.open(baseUrl + "Kwitansi/", "_blank");
                    get_deposit_paket(20);
                  }
                });
              },
            },
          },
        });
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    []
  );
}

function formaddupdate_transaksi_deposit_paket(JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<form action="${baseUrl}Deposit_paket/proses_addupdate_deposit_paket"
                     id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Invoice</label>
                                 <input type="text" name="nomor_transaksi" value="${json.nomor_transaksi}" class="form-control form-control-sm"  readonly/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nama Jamaah</label>
                                 <select class="form-control form-control-sm" name="jamaah_id" id="jamaah_id" onChange="getInfoAgen()">
                                    <option value="0"> Pilih Jamaah </option>`;
                           for (x in json.list_member) {
                              html += `<option value="${json.list_member[x]["id"]}" >
                                          ${json.list_member[x]["fullname"]} (${json.list_member[x]["nomor_identitas"]})
                                       </option>`;
                           }
                        html += `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Sumber Dana</label>
                                 <select class="form-control form-control-sm" name="sumber_dana" id="sumber_dana" onChange="CheckDepositJamaah()" >
                                    <option value="cash">Cash</option>
                                    <option value="deposit">Deposit</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-12" id="area_info_saldo_deposit">
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Biaya Deposit</label>
                                 <input type="text" name="biaya_deposit" value="" class="form-control form-control-sm currency" placeholder="Biaya Tabungan Umrah" required />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Info Deposit</label>
                                 <textarea class="form-control form-control-sm" name="info" rows="6"
                                    style="resize: none;" placeholder="Info Tabungan Umrah" required></textarea>
                              </div>
                           </div>
                           <div class="col-12" id="list_agen"></div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>
               <script>
                  $("#jamaah_id").select2({
                     dropdownParent: $(".jconfirm")
                  });
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
               </script>`;
  return html;
}

function CheckDepositJamaah() {
   var sumber_dana = $("#sumber_dana").val();
   if (sumber_dana == "deposit") {
      var jamaah = $("#jamaah_id").val();
      if (jamaah != 0) {
         ajax_x(
            baseUrl + "Deposit_paket/check_deposit_jamaah",
            function (e) {
               if( e.error == false ){
                  $('#area_info_saldo_deposit').html(`<div class="form-group">
                                                         <label>Saldo Deposit Jamaah</label>
                                                         <div class="form-control form-control-sm">
                                                            <span>${kurs} ${numberFormat(e.data)}</span>
                                                         </div>
                                                      </div>`);
               }else{
                  $('#area_info_saldo_deposit').html(``);
                  frown_alert(e.error_msg);
               }
            },
           [{ jamaah_id: jamaah }]
         );
      } else {
         frown_alert('Anda wajib memilih salah satu jamaah.');
      }
   }else{
      $('#area_info_saldo_deposit').html(``);
   }
}

function getInfoAgen(id) {
  var jamaah_id = '';
  if( id != undefined ) {
    jamaah_id = id;
  }else{
    jamaah_id = $("#jamaah_id option:selected").val();
  }
  
  ajax_x(
    baseUrl + "Deposit_paket/get_info_agen_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        var html = ``;
        var data = e["data"];
        for (x in data) {
          html += `<div class="form-group">
                           <label>Fee ${data[x].level} (${
            data[x].nama_agen
          })</label>
                           <input type="text" name="fee_agen[${
                             data[x].id
                           }]" value="${kurs} ${numberFormat(data[x].fee)}"
                           class="form-control form-control-sm currency" placeholder="Fee Agen"/>
                        </div>`;
        }
        $(`#list_agen`).html(html);
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ jamaah_id: jamaah_id }]
  );
}

function pembayaran_deposit_paket(id) {
  ajax_x(
    baseUrl + "Deposit_paket/get_info_pembayaran_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          columnClass: "col-9",
          title: "Form Pembayaran Tabungan Umrah",
          theme: "material",
          content: formpembayaran_deposit_paket(id, JSON.stringify(e["data"])),
          closeIcon: false,
          buttons: {
            cancel: function () {
              return true;
            },
            simpan: {
              text: "Simpan",
              btnClass: "btn-blue",
              action: function () {
                ajax_submit_t1("#form_utama", function (e) {
                  e["error"] == true
                    ? frown_alert(e["error_msg"])
                    : smile_alert(e["error_msg"]);
                  if (e["error"] == true) {
                    return false;
                  } else {
                    window.open(baseUrl + "Kwitansi/", "_blank");
                    get_deposit_paket(20);
                  }
                });
              },
            },
          },
        });
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ id: id }]
  );
}

function formpembayaran_deposit_paket(id, JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<form action="${baseUrl}Deposit_paket/proses_pembayaran_deposit_paket"
                     id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-2">
                              <div class="form-group">
                                 <label>Invoice</label>
                                 <input type="hidden" name="id" value="${id}">
                                 <input type="text" name="nomor_transaksi" value="${json.nomor_transaksi}" class="form-control form-control-sm"  readonly/>
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nama Jamaah</label>
                                 <input type="text" name="fullname" value="${json.fullname}" class="form-control form-control-sm" placeholder="Nama Jamaah" readonly />
                              </div>
                           </div>
                           <div class="col-5">
                              <div class="form-group">
                                 <label>Nomor Identitas Jamaah</label>
                                 <input type="text" name="identity_number" value="${json.identity_number}" class="form-control form-control-sm" placeholder="Nomor Identitas Jamaah" readonly />
                              </div>
                           </div>`;

                if (json.nama_agen != "") {
                  html += `<div class="col-3">
                              <div class="form-group">
                                 <label>Nama Agen</label>
                                 <input type="text" name="nama_agen" value="${json.nama_agen}" class="form-control form-control-sm" placeholder="Nama Agen" readonly />
                              </div>
                           </div>`;
                }

                  html += `<div class="col-3">
                              <div class="form-group">
                                <label>Sumber Pembayaran</label>
                                <select class="form-control form-control-sm" name="sumber_pembayaran" id="sumber_pembayaran" onChange="defineSumberPembayaran()">
                                  <option value="cash">Cash</option>
                                  <option value="pinjam">Pinjam Koperasi</option>
                                </select>
                              </div>
                           </div>
                           <div class="col-2" id="tenor_area">
                           </div>
                           <div class="col-4" id="tanggal_mulai_area">
                           </div>
                           <div class="col-3">
                              <div class="form-group">
                                 <label>Biaya Deposit</label>
                                 <input type="text" name="biaya_deposit" value="" class="form-control form-control-sm currency" placeholder="Biaya Tabungan Umrah" required />
                              </div>
                           </div>
                           <div class="col-9">
                              <div class="form-group">
                                 <label>Info Deposit</label>
                                 <textarea class="form-control form-control-sm" name="info" rows="3"
                                    style="resize: none;" placeholder="Info Tabungan Umrah" required></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="row"></div>
                     </div>
                  </div>
               </form>
               <script>
                  $("#jamaah_id").select2({
                     dropdownParent: $(".jconfirm")
                  });
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
               </script>`;
  return html;
}

function defineSumberPembayaran(){
  var sumber_pembayaran = $('#sumber_pembayaran').val();
  if( sumber_pembayaran == 'cash' ) {
    $('#tenor_area').html('');
    $('#tanggal_mulai_area').html('');
    
  }else{
    $('#tenor_area').html(`<div class="form-group">
                               <label>Tenor Pinjaman</label>
                               <input type="number" name="tenor" value="" class="form-control form-control-sm" placeholder="Tenor" />
                            </div>`);
    $('#tanggal_mulai_area').html(`<div class="form-group">
                                     <label>Mulai Pembayaran</label>
                                     <input type="date" name="tanggal_mulai" value="" class="form-control form-control-sm" placeholder="Tanggal Mulai" />
                                  </div>`);
    
  }
}

function handover_barang_deposit_paket(id) {
  ajax_x(
    baseUrl + "Deposit_paket/get_info_handover_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          columnClass: "col-8",
          title: "Form Handover Fasilitas",
          theme: "material",
          content: formhandover_fasilitas_deposit_paket(
            id,
            JSON.stringify(e["data"])
          ),
          closeIcon: false,
          buttons: {
            cancel: function () {
              return true;
            },
            simpan: {
              text: "Simpan",
              btnClass: "btn-blue",
              action: function () {
                ajax_submit_t1("#form_utama", function (e) {
                  e["error"] == true
                    ? frown_alert(e["error_msg"])
                    : smile_alert(e["error_msg"]);
                  if (e["error"] == true) {
                    return false;
                  } else {
                    window.open(baseUrl + "Kwitansi/", "_blank");
                    get_deposit_paket(20);
                  }
                });
              },
            },
          },
        });
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ id: id }]
  );
}

function formhandover_fasilitas_deposit_paket(id, JSONData) {
  var json = JSON.parse(JSONData);
  var form = `<form action="${baseUrl}Deposit_paket/proses_handover_fasilitas_deposit_paket" id="form_utama" class="formName ">
               <input type="hidden" name="id" value="${id}">
               <input type="hidden" name="nomor_transaksi" value="${json.nomor_transaksi}">
               <div class="row px-0 mx-0">
                  <div class="col-6" >
                     <label>History Penerimaan Fasilitas</label>
                  </div>
                  <div class="col-6 float-right text-right" >
                     <label>Kode Invoice: #${json.nomor_transaksi}</label>
                  </div>
                  <div class="col-12" >
                     <table class="table table-hover">
                        <thead>
                           <tr>
                              <th style="width:20%;" scope="col">Invoice</th>
                              <th style="width:20%;" scope="col">Nama Item</th>
                              <th style="width:20%;" scope="col">Penerima</th>
                              <th style="width:20%;" scope="col">Tgl. Terima</th>
                              <th style="width:20%;" scope="col">Petugas</th>
                           </tr>
                        </thead>
                        <tbody id="list_facilities">`;

  if (Object.keys(json.riwayat_handover).length > 0) {
    for (x in json.riwayat_handover) {
      form += `<tr>
                                          <td><b>${json.riwayat_handover[x].invoice}</b></td>
                                          <td>${json.riwayat_handover[x].facilities_name}</td>
                                          <td>${json.riwayat_handover[x].receiver_name}<br>(${json.riwayat_handover[x].receiver_identity})</td>
                                          <td>${json.riwayat_handover[x].date_transaction}</td>
                                          <td>${json.riwayat_handover[x].petugas}</td>
                                       <tr>`;
    }
  } else {
    form += `<tr>
                                       <td colspan="5"><center>Data History Transaksi Handover Fasilitas Tidak Ditemukan</center></td>
                                    <tr>`;
  }

  form += `</tbody>
                     </table>
                  </div>
                  <div class="col-12" >
                     <div class="row" >
                        <div class="col-4" >
                           <div class="form-group">
                              <label class="col-form-label col-form-label-sm">Nama Penerima Fasilitas</label>
                              <input class="form-control form-control-sm" type="text" placeholder="Nama Penerima Fasilitas" id="nama_penerima" name="nama_penerima">
                           </div>
                           <div class="form-group">
                              <label class="col-form-label col-form-label-sm">No Identitas Penerima Fasilitas</label>
                              <input class="form-control form-control-sm" type="text" placeholder="No Identitas Penerima Fasilitas" id="no_identitas" name="no_identitas">
                           </div>
                        </div>
                        <div class="col-8" >
                           <label>Fasilitas paket yang belum diterima</label>
                           <div class="row" >`;
  if (Object.keys(json.sisa).length > 0) {
    for (y in json.sisa) {
      form += `<div class="col-4">
                                    <div class="form-check">
                                       <label class="form-check-label">
                                          <input class="form-check-input fasilitas" type="checkbox" value="${y}" name="fasilitas[${y}]">
                                          ${json.sisa[y]}
                                       </label>
                                    </div>
                                 </div>`;
    }
  } else {
    form += `<div class="col-12 pt-4">
                                 <center><span>Daftar Fasilitas Tidak Ditemukan.</span></center>
                              </div>`;
  }
  form += `</div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>`;

  return form;
}

function delete_deposit_paket(id) {
  $.confirm({
    columnClass: "col-4",
    title: "Peringantan",
    theme: "material",
    content: `Jika anda menghapus tabungan umrah ini, maka semua fee agen yang terikut dengan transaksi tabungan umrah ini juga akan ikut terhapus. Apakah anda yakin akan menghapus transaksi tabungan umrah ini?.`,
    closeIcon: false,
    buttons: {
      tidak: function () {
        return true;
      },
      simpan: {
        text: "Ya",
        btnClass: "btn-red",
        action: function () {
          ajax_x(
            baseUrl + "Deposit_paket/delete_pool_deposit_paket",
            function (e) {
              if (e["error"] == false) {
                get_deposit_paket(20);
              } else {
                frown_alert(e["error_msg"]);
              }
            },
            [{ id: id }]
          );
        },
      },
    },
  });
}

function delete_transaksi_handover_fasilitas_deposit_paket(invoice) {
  ajax_x(
    baseUrl + "Deposit_paket/delete_transaksi_handover_fasilitas_deposit_paket",
    function (e) {
      if (e["error"] == false) {
        smile_alert(e["error_msg"]);
        get_deposit_paket(20);
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ invoice: invoice }]
  );
}
