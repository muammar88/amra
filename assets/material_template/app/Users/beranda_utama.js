function beranda_utama_Pages() {
  return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentBerandaUtama">
                  <div class="col-lg-12 col-6 px-2 py-0 mb-0" style="font-size: 14px !important;">
                     <button class="btn btn-default float-left" type="button" title="Saldo Perusahaan Sekarang">
                        <i class="fas fa-redo"></i> Reload Page
                     </button>
                     <div class="btn-group float-right ml-3">
                        <button class="btn btn-default float-right" type="button" title="Saldo Perusahaan Sekarang">
                           <i class="fas fa-money-bill-wave"></i> <b>SALDO perusahaan</b> : <b id="saldo">Rp 0,-</b>
                        </button>
                        
                     </div>
                  </div>
                  <hr class="w-100">
                  <div class="col-lg-8 col-6">
                     <div class="row" style="height: 100%;">
                        <div class="col-lg-3 col-6">
                           <!-- small box -->
                           <div class="small-box bg-info" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="jamaah_terdaftar">0</h3>
                                 <p class="my-5"><b>JAMAAH</b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">terdaftar</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-user-check"></i>
                              </div>
                              <a onClick="jamaah_terdaftar()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6">
                           <div class="small-box bg-success" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="paket_berangkat">0</h3>
                                 <p class="my-5"><b>PAKET</b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">akan berangkat</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-box-open"></i>
                              </div>
                              <a onClick="paket_berangkat()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6">
                           <div class="small-box bg-warning" style="height: 100%;">
                              <div class="inner">
                                 <h3 id="jamaah_berangkat">0</h3>
                                 <p class="my-5"><b>JAMAAH</b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">akan berangkat</span></p>
                              </div>
                              <div class="icon">
                                 <i class="fas fa-user"></i>
                              </div>
                              <a onClick="jamaah_berangkat()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                        <div class="col-lg-3 col-6">
                           <div class="small-box bg-danger" style="height: 100%;">
                              <div class="inner">
                                 <h3 style="color:white;" id="tiket_terjual">0</h3>
                                 <p class="my-5"><b>TIKET</b> <br><span class="ml-4" style="font-size: 14px;font-style: italic;">terjual bulan ini</span></p>
                              </div>
                              <div class="icon"><i class="fas fa-ticket-alt"></i></div>
                              <a onClick="tiket_terjual()" class="small-box-footer py-2 px-3 text-right" style="bottom: 0;position: absolute;width: 100%;">Detail info <i class="fas fa-arrow-circle-right"></i></a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-4 col-6">
                     <div class="row" >
                        <div class="col-6 col-lg-8 mb-3 mt-0 ">
                           <button class="btn btn-default" type="button" onClick="add_headline()" >
                              <i class="fas fa-plus"></i> Tambahkan Headline
                           </button>
                        </div>
                        <div class="col-12 col-lg-12">
                           <table class="table" >
                              <thead>
                                 <tr>
                                    <th style="width:70%;">Headline</th>
                                    <th style="width:30%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_headline">
                                 <td colspan="2">Daftar headline tidak ditemukan</td>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-0" >
                           <div class="row" id="pagination_headline"></div>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-6">
                     <div class="row " id="contents">
                        <div class="col-lg-12 text-center my-5">
                           <span style="color: #919191;font-weight: bold;">Info Area</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-6">
                     <div class="row">
                        <div class="col-lg-5">
                           <label class="float-left py-2 my-3">Permintaan Deposit Member</label>
                        </div>
                        <div class="col-lg-7 my-3 text-right">
                           <div class="input-group ">
                              <input class="form-control form-control-sm" type="text" onkeyup="get_data_jamaah_terdaftar(20)" id="searchJamaahTerdaftar" name="searchJamaahTerdaftar" placeholder="Nama / Nomor Identitas Member" style="font-size: 12px;">
                              <div class="input-group-append">
                                 <button class="btn btn-default" type="button" onclick="get_data_jamaah_terdaftar(20)">
                                    <i class="fas fa-search"></i> Cari
                                 </button>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-12">
                           <table class="table" >
                              <thead>
                                 <tr>
                                    <th style="width:30%;">Nama Member /<br> Identitas Member</th>
                                    <th style="width:25%;">Jumlah / <br>Keperluan / <br> Sumber Biaya</th>
                                    <th style="width:25%;">Bank Info</th>
                                    <th style="width:20%;">Aksi</th>
                                 </tr>
                              </thead>
                              <tbody id="body_request_deposit">
                                 <td colspan="4">Daftar request deposit tidak ditemukan</td>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-lg-12 px-3 pb-3" >
                           <div class="row" id="pagination_request_deposit"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>`;
}

function formPayMent() {
  return `<form action="${baseUrl}Beranda_utama/proses_tambah_saldo" id="payment-form" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Saldo</label>
                                 <input type="hidden" name="result_type" id="result-type" value=""></div>
                                 <input type="hidden" name="result_data" id="result-data" value=""></div>
                                 <input type="text" name="saldo" id="saldo_deposit" value="" class="form-control form-control-sm currency" placeholder="Saldo" />
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
}

function formHeadline(JSONValue) {
  id = "";
  show = "";
  headline = "";
  if (JSONValue != undefined) {
    var value = JSON.parse(JSONValue);
    id = `<input type="hidden" name="id" value="${value.id}">`;
    show = value.tampilkan;
    headline = value.headline;
  }

  return `<form action="${baseUrl}Beranda_utama/proses_addupdate_headline" id="form_utama" class="formName ">
               <div class="row px-0 mx-0">
                  <div class="col-12">
                     <div class="row">
                        ${id}
                        <div class="col-12">
                           <div class="form-group mb-2">
                              <label>Headline</label>
                              <textarea class="form-control form-control-sm" name="headline" id="headline" placeholder="headline" rows="7" style="resize: none;">${headline}</textarea>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="form-group mb-2">
                              <label>Tampilkan</label>
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" value="tampilkan" name="show" id="show" ${
                                   show == "tampilkan" ? "checked" : ""
                                 }>
                                 <label class="form-check-label" for="show">
                                    Tampilkan
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>`;
}

function add_saldo_company() {
  $.confirm({
    title: "Tambah Saldo Perusahaan",
    theme: "material",
    columnClass: "col-4",
    content: formPayMent(),
    closeIcon: false,
    buttons: {
      cancel: function () {
        return true;
      },
      formSubmit: {
        text: "Bayar",
        btnClass: "btn-blue",
        action: function () {
          var csrfName = localStorage.getItem("csrfName");
          var csrfHash = localStorage.getItem("csrfHash");
          // data
          var datas = {};
          datas[csrfName] = csrfHash;
          datas["saldo"] = $("#saldo_deposit").val();
          console.log("datas");
          console.log(datas);
          console.log("datas");
          $.ajax({
            url: baseUrl + "Beranda_utama/get_token",
            type: "post",
            dataType: "json",
            data: datas,
            success: function (data) {
              snap.pay(data.token, {
                onSuccess: function (result) {
                  ajax_x(
                    baseUrl + "Beranda_utama/save_process_log_saldo",
                    function (e) {},
                    [result]
                  );
                },
                onPending: function (result) {
                  ajax_x(
                    baseUrl + "Beranda_utama/save_process_log_saldo",
                    function (e) {},
                    [result]
                  );
                },
                onError: function (result) {
                  ajax_x(
                    baseUrl + "Beranda_utama/save_process_log_saldo",
                    function (e) {},
                    [result]
                  );
                },
              });
            },
          });
        },
      },
    },
  });
}

function beranda_utama_getData() {
  get_data_beranda_utama();
  jamaah_terdaftar();
  get_data_headline(2);
  get_data_request_deposit(5);
}

function get_data_request_deposit(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_request_deposit",
    pagination_id: "pagination_request_deposit",
    bodyTable_id: "body_request_deposit",
    fn: "ListDaftarRequestDeposit",
    warning_text: '<td colspan="4">Daftar request deposit tidak ditemukan</td>',
    param: { search: $("#searchRequestDeposit").val() },
  });
}

function ListDaftarRequestDeposit(JSONData) {
  var json = JSON.parse(JSONData);
  return `<tr>
               <td>
                  ${json.fullname}<br>
                  (NO ID : ${json.identity_number})<br>
                  <span style="color: #ffc107!important;">${json.last_update}</span>
               </td>
               <td>Rp ${numberFormat(json.total_amount)}<br>
                  <b>${json.activity_type}</b><br>
                  <b style="color:red;">Sumber Biaya : ${json.sumber_biaya}</b></td>
               <td>
                  <b>No Rek : ${json.bank_account}</b>
                  <img src="${json.logo_bank}">
               </td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Reject Permintaan" onclick="rejectRequest(${
                    json.id
                  })" style="margin:.15rem .1rem  !important;background-color: #d06464 !important;color: white!important;">
                     <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Setujui Permintaan" onclick="approveRequest(${
                    json.id
                  })" style="margin: 0.15rem 0.1rem !important;background-color: #4fa845 !important;color: white!important;">
                     <i class="fas fa-check" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function formPenolakan(id) {
  var html = `<form action="${baseUrl}Beranda_utama/proses_penolakan_request" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <input type="hidden" value="${id}" name="id">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Alasan Penolakan Request</label>
                                 <textarea class="form-control form-control-sm" name="alasan_penolakan" placeholder="Alasan Penolakan Request" rows="7" style="resize: none;"></textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
  return html;
}
function rejectRequest(id) {
  $.confirm({
    columnClass: "col-4",
    title: "Form Alasan Penolakan",
    theme: "material",
    content: formPenolakan(id),
    closeIcon: false,
    buttons: {
      cancel: function () {
        return true;
      },
      simpan: {
        text: "Tolak Request",
        btnClass: "btn-red",
        action: function () {
          ajax_submit_t1("#form_utama", function (e) {
            e["error"] == true
              ? frown_alert(e["error_msg"])
              : smile_alert(e["error_msg"]);
            if (e["error"] == true) {
              return false;
            } else {
              get_data_request_deposit(5);
            }
          });
        },
      },
    },
  });
}

function approveRequest(id) {

   $.confirm({
    columnClass: "col-4",
    title: "Pemberitahuan",
    theme: "material",
    content: "Apakah yakin anda akan menyetujui pemintaan ini?.",
    closeIcon: false,
    buttons: {
      cancel: function () {
        return true;
      },
      simpan: {
         text: "Iya",
         btnClass: "btn-red",
         action: function () {
            ajax_x(
               baseUrl + "Beranda_utama/approve_request",
               function (e) {
                  if (e["error"] == false) {
                    smile_alert(e["error_msg"]);
                    get_data_request_deposit(5);
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

function get_data_headline(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_headline",
    pagination_id: "pagination_headline",
    bodyTable_id: "body_headline",
    fn: "ListDaftarHeadline",
    warning_text: '<td colspan="2">Daftar headline tidak ditemukan</td>',
    param: [],
  });
}

function ListDaftarHeadline(JSONData) {
  var json = JSON.parse(JSONData);
  return `<tr>
               <td style="text-align:left;">${
                 json.headline
               }<br><b>(Dipos Tgl : ${json.last_update})<br>(${
    json.tampilkan == "tampilkan"
      ? 'Show : <i class="fas fa-check" style="font-size: 11px;color:green;"></i>'
      : 'Show : <i class="fas fa-times" style="font-size: 11px;color:red;"></i>'
  })</b></td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Edit Headline" onclick="editHeadline(${
                    json.id
                  })" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Headline" onclick="deleteHeadline(${
                    json.id
                  })" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function deleteHeadline(id) {
  ajax_x(
    baseUrl + "Beranda_utama/deleteHeadline",
    function (e) {
      e["error"] == true
        ? frown_alert(e["error_msg"])
        : smile_alert(e["error_msg"]);
      if (e["error"] == false) {
        get_data_headline(2);
      }
    },
    [{ id: id }]
  );
}

function add_headline() {
  $.confirm({
    title: "Tambah Headline",
    theme: "material",
    columnClass: "col-4",
    content: formHeadline(),
    closeIcon: false,
    buttons: {
      cancel: function () {
        return true;
      },
      formSubmit: {
        text: "Simpan",
        btnClass: "btn-blue",
        action: function () {
          if ($("#headline").val() != "") {
            // alert('Masuk 1231');
            ajax_submit_t1("#form_utama", function (e) {
              e["error"] == true
                ? frown_alert(e["error_msg"])
                : smile_alert(e["error_msg"]);
              if (e["error"] == true) {
                return false;
              } else {
                get_data_headline(2);
              }
            });
          } else {
            frown_alert(
              "Untuk melanjutkan proses penyimpanan, Headline tidak boleh kosong."
            );
          }
        },
      },
    },
  });
}

function editHeadline(id) {
  ajax_x(
    baseUrl + "Beranda_utama/editHeadline",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          title: "Edit Headline",
          theme: "material",
          columnClass: "col-4",
          content: formHeadline(JSON.stringify(e["value"])),
          closeIcon: false,
          buttons: {
            cancel: function () {
              return true;
            },
            formSubmit: {
              text: "Simpan",
              btnClass: "btn-blue",
              action: function () {
                if ($("#headline").val() != "") {
                  ajax_submit_t1("#form_utama", function (e) {
                    e["error"] == true
                      ? frown_alert(e["error_msg"])
                      : smile_alert(e["error_msg"]);
                    if (e["error"] == true) {
                      return false;
                    } else {
                      get_data_headline(2);
                    }
                  });
                } else {
                  frown_alert(
                    "Untuk melanjutkan proses penyimpanan, Headline tidak boleh kosong."
                  );
                }
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

function get_data_beranda_utama() {
  ajax_x(
    baseUrl + "Beranda_utama/get_info_beranda_utama",
    function (e) {
      if (e["error"] == false) {
        $("#saldo").html("Rp " + numberFormat(e.data.saldo));
        $("#jamaah_terdaftar").html(e.data.jamaah_terdaftar);
        $("#paket_berangkat").html(e.data.paket_berangkat);
        $("#jamaah_berangkat").html(e.data.jamaah_berangkat);
        $("#tiket_terjual").html(e.data.tiket_terjual);
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    []
  );
}

function jamaah_terdaftar() {
  page_jamaah_terdaftar();
  get_data_jamaah_terdaftar(20);
}

function page_jamaah_terdaftar() {
  var html = `<div class="col-lg-4">
                  <label class="float-left py-2 my-3">Jamaah Terdaftar</label>
               </div>
               <div class="col-lg-8 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="get_data_jamaah_terdaftar(20)" id="searchJamaahTerdaftar" name="searchJamaahTerdaftar" placeholder="Nama / Nomor Identitas Jamaah" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_data_jamaah_terdaftar(20)">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:35%;">Nama Jamaah /<br>Nomor Identitas</th>
                           <th style="width:25%;">Tempat/<br>Tanggal Lahir</th>
                           <th style="width:20%;">Nomor Passport</th>
                           <th style="width:10%;">Total Pembelian</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable">
                        <tr>
                           <td colspan="5">Daftar jamaah tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination"></div>
               </div>`;
  $("#contents").html(html);
}

function get_data_jamaah_terdaftar(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_jamaah_terdaftar",
    pagination_id: "pagination",
    bodyTable_id: "bodyTable",
    fn: "ListDaftarJamaahTerdaftar",
    warning_text: '<td colspan="5">Daftar jamaah tidak ditemukan</td>',
    param: { search: $("#searchJamaahTerdaftar").val() },
  });
}

function ListDaftarJamaahTerdaftar(JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<tr>
                  <td>${json.fullname}/<br>${json.identity_number}</td>
                  <td>${json.birth_place} /<br>${json.birth_date}</td>
                  <td>${json.passport_number}</td>
                  <td>${json.total_pembelian}</td>
               </tr>`;
  return html;
}

function paket_berangkat() {
  page_paket_berangkat();
  get_data_paket_berangkat(20);
}

function page_paket_berangkat() {
  var html = `<div class="col-lg-4">
                  <label class="float-left py-2 my-3">Paket yang akan Berangkat</label>
               </div>
               <div class="col-lg-8 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="get_data_paket_berangkat(20)" id="searchPaketNameKode" name="searchPaketNameKode" placeholder="Nama Paket / Kode Paket" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_data_paket_berangkat(20)">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:35%;">Info Paket</th>
                           <th style="width:31%;">Deskripsi</th>
                           <th style="width:24%;">Tgl. Berangkat (B)<br> Tgl. Pulang (P)</th>
                           <th style="width:10%;">Total Jamaah</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable">
                        <tr>
                           <td colspan="6">Daftar paket yang akan berangkat tidak ditemukan</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination"></div>
               </div>`;
  $("#contents").html(html);
}

function get_data_paket_berangkat(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_paket_berangkat",
    pagination_id: "pagination",
    bodyTable_id: "bodyTable",
    fn: "ListDaftarPaketBerangkat",
    warning_text:
      '<td colspan="4">Daftar paket yang akan berangkat tidak ditemukan</td>',
    param: { search: $("#searchPaketNameKode").val() },
  });
}

function ListDaftarPaketBerangkat(JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<tr>
                  <td><b>Kode</b> : ${json.kode} <br> ${json.paket_name}<br><br>
                  <span style="float:left;">Harga Paket :</span>
                     <ul class="text-left pl-3">`;
  if (json.paket_type.length > 0) {
    for (x in json.paket_type) {
      html += `<li>${json.paket_type[x].paket_type_name} : Rp ${numberFormat(
        json.paket_type[x].price
      )}</li>`;
    }
  } else {
    html += `<li>Harga Paket Tidak Ditemukan.</li>`;
  }
  html += `</ul>
                  </td>
                  <td style="text-align:left;">${json.description}</td>
                  <td style="text-align:left;"><b>(B)</b> ${json.departure_date} /<br><b>(P)</b> ${json.return_date}</td>
                  <td>${json.jumlah_jamaah} Jamaah</td>
               </tr>`;
  return html;
}

function jamaah_berangkat() {
  page_jamaah_berangkat();
  get_data_jamaah_berangkat(20);
}

function page_jamaah_berangkat() {
  var html = `<div class="col-lg-4">
                  <label class="float-left py-2 my-3">Jamaah akan Berangkat</label>
               </div>
               <div class="col-lg-8 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="get_data_jamaah_berangkat(20)" id="searchJamaahBerangkat" name="searchJamaahBerangkat" placeholder="Nama / Nomor Identitas Jamaah" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_data_jamaah_berangkat(20)">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:20%;" class="align-middle">Nomor Identitas /<br> Nama Jamaah</th>
                           <th style="width:15%;" class="align-middle">Total Harga</th>
                           <th style="width:15%;" class="align-middle">Tempat /<br> Tanggal Lahir</th>
                           <th style="width:20%;" class="align-middle">Kode /<br> Nama Paket</th>
                           <th style="width:20%;" class="align-middle">Tgl Berangkat <br>Tgl Kembali</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable">
                        <tr><td colspan="5">Daftar jamaah tidak ditemukan</td></tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination"></div>
               </div>`;
  $("#contents").html(html);
}

function get_data_jamaah_berangkat(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_jamaah_berangkat",
    pagination_id: "pagination",
    bodyTable_id: "bodyTable",
    fn: "ListDaftarJamaahBerangkat",
    warning_text: '<td colspan="6">Daftar jamaah tidak ditemukan</td>',
    param: { search: $("#searchJamaahBerangkat").val() },
  });
}

function ListDaftarJamaahBerangkat(JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<tr>
                  <td>${json.identity_number} <br> ${json.fullname}</td>
                  <td>Rp ${numberFormat(json.price)}</td>
                  <td>${json.birth_place},<br> ${json.birth_date} </td>
                  <td><b>Kode</b> : ${json.kode} <br> ${json.paket_name}</td>
                  <td>${json.departure_date}<br>${json.return_date}</td>
               </tr>`;
  return html;
}

function tiket_terjual() {
  page_tiket_terjual();
  get_data_tiket_terjual(20);
}

function page_tiket_terjual() {
  var html = `<div class="col-lg-4">
                  <label class="float-left py-2 my-3">Jamaah akan Berangkat</label>
               </div>
               <div class="col-lg-8 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="text" onkeyup="get_data_tiket_terjual(20)" id="searchTiketTerjual" name="searchTiketTerjual" placeholder="Nama / Nomor Identitas Jamaah" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_data_tiket_terjual(20)">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:20%;">No Register</th>
                           <th style="width:40%;">Info Tiket </th>
                           <th style="width:40%;">Info Pembayaran</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable">
                        <tr><td colspan="3">Daftar jamaah tidak ditemukan</td></tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination"></div>
               </div>`;
  $("#contents").html(html);
}

function get_data_tiket_terjual(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_tiket_terjual",
    pagination_id: "pagination",
    bodyTable_id: "bodyTable",
    fn: "ListDaftarTiketTerjual",
    warning_text: '<td colspan="6">Daftar tiket terjual tidak ditemukan</td>',
    param: { search: $("#searchTiketTerjual").val() },
  });
}

function ListDaftarTiketTerjual(JSONData) {
  var json = JSON.parse(JSONData);
  var detail_transaction = `<table class="table mb-0">
                                 <tbody>`;
  for (x in json.detail_transaction) {
    detail_transaction += `<tr>
                                       <td style="width:20%;border:none" class="text-left px-0 pb-0"><b>PAX</b></td>
                                       <td style="width:20%;border:none" class="text-left px-1 pb-0">: ${
                                         json.detail_transaction[x]["pax"]
                                       }</td>
                                       <td style="width:30%;border:none" class="text-left px-0 pb-0"><b>NAMA AIRLINES</b></td>
                                       <td style="width:30%;border:none" class="text-left px-1 pb-0">: ${
                                         json.detail_transaction[x][
                                           "airlines_name"
                                         ]
                                       }</td>
                                    </tr>
                                    <tr>
                                       <td style="border:none" class="text-left px-0 py-0"><b>KODE BOOKING</b></td>
                                       <td style="border:none" class="text-left px-1 py-0">: <span style="color:red"><b>${
                                         json.detail_transaction[x][
                                           "code_booking"
                                         ]
                                       }</b></span></td>
                                       <td style="border:none" class="text-left px-0 py-0"><b>TANGGAL BERANGKAT</b></td>
                                       <td style="border:none" class="text-left px-1 py-0">: ${
                                         json.detail_transaction[x][
                                           "departure_date"
                                         ]
                                       }</td>
                                    </tr>
                                    <tr>
                                       <td style="border:none" class="text-left px-0 pt-0"><b>HARGA TRAVEL</b></td>
                                       <td style="border:none" class="text-left px-1 pt-0">: Rp ${numberFormat(
                                         json.detail_transaction[x][
                                           "travel_price"
                                         ]
                                       )}</td>
                                       <td style="border:none" class="text-left px-0 pt-0"><b>HARGA KOSTUMER</b></td>
                                       <td style="border:none" class="text-left px-1 pt-0">: Rp ${numberFormat(
                                         json.detail_transaction[x][
                                           "costumer_price"
                                         ]
                                       )}</td>
                                    </tr>
                                    <tr style="background-color: #ffcbcb;">
                                       <td style="border:none" class="text-right mb-1 pl-0 py-1" colspan="3"><b>SUBTOTAL</b></td>
                                       <td class="text-left mb-1 px-1 py-1" style="background-color: #f59393;border:none">: Rp ${numberFormat(
                                         json.detail_transaction[x]["total"]
                                       )}</td>
                                    </tr>
                                    <tr><td style="border:none" class="py-2" colspan="4"></td></tr>`;
  }
  detail_transaction += `</tbody>
                              </table>`;

  var info_pembayaran = `<table class="table table-hover mb-1">
                              <tbody>
                                 <tr>
                                    <td style="width:50%;border:none" class="text-left px-0 py-0"><b>TOTAL TRANSAKSI TIKET</b></td>
                                    <td style="width:50%;border:none" class="text-left px-1 py-0">: Rp ${numberFormat(
                                      json.total
                                    )} </td>
                                 </tr>
                                 <tr>
                                    <td style="border:none" class="text-left px-0 py-0"><b>TOTAL PEMBAYARAN</b></td>
                                    <td style="border:none" class="text-left px-1 py-0">: Rp ${numberFormat(
                                      json.total_sudah_bayar
                                    )}</td>
                                 </tr>
                                 <tr>
                                    <td style="border:none" class="text-left px-0 py-0"><b>SISA PEMBAYARAN</b></td>
                                    <td style="border:none" class="text-left px-1 py-0">: Rp ${numberFormat(
                                      json.sisa
                                    )}</td>
                                 </tr>
                              </tbody>
                           </table>
                           <div class="row">
                              <div class="col-12 text-left">
                                 <label class="mb-0">RIWAYAT PEMBAYARAN <span style="color:red;font-style:italic;font-size:10px">(Tiga transaksi terakhir)</span></label>
                                 <ul class="list mt-0 mb-1 pl-3">`;
  if (json.riwayat_transaksi_tiket.length > 0) {
    for (y in json.riwayat_transaksi_tiket) {
      info_pembayaran += `<li style="border-bottom: 1px dashed #c3bdbd;" class="mb-1">${
        json.riwayat_transaksi_tiket[y]["ket"] == "refund"
          ? '<span style="color:red"><b>[REFUND]</b></span>'
          : ""
      } Tanggal Transaksi: ${
        json.riwayat_transaksi_tiket[y]["tanggal_transaksi"]
      } | No Invoice: <b style="color:red">${
        json.riwayat_transaksi_tiket[y]["invoice"]
      } </b> | Biaya: Rp ${numberFormat(
        json.riwayat_transaksi_tiket[y]["biaya"]
      )} | Nama Petugas: ${
        json.riwayat_transaksi_tiket[y]["nama_petugas"]
      } | Nama Pelanggan : ${
        json.riwayat_transaksi_tiket[y]["nama_pelanggan"]
      } | Nomor Identitas : ${
        json.riwayat_transaksi_tiket[y]["nomor_identitas"]
      } </li>`;
    }
  } else {
    info_pembayaran += `<li style="color:red;" class="mb-1 text-center">Riwayat pembayaran tiket tidak ditemukan </li>`;
  }
  info_pembayaran += `</ul>
                              </div>
                           </div>`;
  var html = `<tr>
                  <td>
                     <b>${json.nomor_register}</b><br><br>${json.transaction_date}
                  </td>
                  <td>${detail_transaction}</td>
                  <td>${info_pembayaran}</td>
               </tr>`;
  return html;
}

function riwayat_saldo() {
  page_riwayat_saldo();
  get_data_riwayat_saldo(20);
}

function page_riwayat_saldo() {
  var html = `<div class="col-lg-8">
                  <label class="float-left py-2 my-3">Riwayat Saldo Perusahaan</label>
                  <label class="float-right py-2 my-3">Filter Tanggal Transaksi : </label>
               </div>
               <div class="col-lg-4 my-3 text-right">
                  <div class="input-group ">
                     <input class="form-control form-control-sm" type="date" id="start_date" name="start_date" title="Mulai Tanggal" placeholder="Start Date" style="font-size: 12px;">
                     <input class="form-control form-control-sm" type="date" id="end_date" name="end_date" title="Sampai Tanggal" placeholder="End Date" style="font-size: 12px;">
                     <div class="input-group-append">
                        <button class="btn btn-default" type="button" onclick="get_data_riwayat_saldo(20)">
                           <i class="fas fa-search"></i> Cari
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-lg-12">
                  <table class="table table-hover tablebuka">
                     <thead>
                        <tr>
                           <th style="width:25%;">Saldo</th>
                           <th style="width:20%;">Tipe Request</th>
                           <th style="width:20%;">Status</th>
                           <th style="width:30%;">Tanggal Transaksi</th>
                           <th style="width:5%;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="bodyTable">
                        <tr><td colspan="5">Daftar riwayat saldo tidak ditemukan</td></tr>
                     </tbody>
                  </table>
               </div>
               <div class="col-lg-12 px-3 pb-3" >
                  <div class="row" id="pagination"></div>
               </div>`;
  $("#contents").html(html);
}

function get_data_riwayat_saldo(perpage) {
  get_data(perpage, {
    url: "Beranda_utama/daftar_riwayat_saldo",
    pagination_id: "pagination",
    bodyTable_id: "bodyTable",
    fn: "ListDaftarRiwayatSaldo",
    warning_text: '<td colspan="5">Daftar riwayat saldo tidak ditemukan</td>',
    param: {
      start_date: $("#start_date").val(),
      end_date: $("#end_date").val(),
    },
  });
}

function ListDaftarRiwayatSaldo(JSONData) {
  var json = JSON.parse(JSONData);
  var html = `<tr>
                  <td>Rp ${numberFormat(json.saldo)}</td>
                  <td>${json.request_type}</td>
                  <td>${json.status}</td>
                  <td>${json.tanggal_transaksi}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Detail Riwayat Status Transaksi"
                        onclick="detail_riwayat_saldo('${
                          json.id
                        }')" style="margin:.15rem .1rem !important">
                        <i class="fas fas fa-info-circle" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
  return html;
}
