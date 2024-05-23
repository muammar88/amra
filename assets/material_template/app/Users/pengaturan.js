function daftar_bank_transfer_Pages() {
  return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarBankTransfer">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_bank_transfer()">
                        <i class="fas fa-piggy-bank"></i> Tambah Bank Transfer
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_bank_transfer( 20)" id="searchAllDaftarBankTransfer" name="searchAllDaftarBankTransfer" placeholder="Nama / No Rekening Bank Transfer" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_bank_transfer( 20 )">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                             <th style="width:25%;">Nama Bank</th>
                             <th style="width:25%;">Logo Bank</th>
                             <th style="width:20%;">Rekening Atas Nama</th>
                             <th style="width:20%;">Nomor Rekening Bank</th>
                             <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_bank_transfer">
                           <tr>
                              <td colspan="4">Daftar bank transfer tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_bank_transfer"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_bank_transfer_getData() {
  get_daftar_bank_transfer(20);
}

function get_daftar_bank_transfer(perpage) {
  get_data(perpage, {
    url: "Pengaturan/daftar_bank_transfer",
    pagination_id: "pagination_daftar_bank_transfer",
    bodyTable_id: "bodyTable_daftar_bank_transfer",
    fn: "ListDaftarBankTransfer",
    warning_text: '<td colspan="4">Daftar bank transfer tidak ditemukan</td>',
    param: { search: $("#searchAllDaftarBankTransfer").val() },
  });
}

function ListDaftarBankTransfer(JSONData) {
  var json = JSON.parse(JSONData);
  return `<tr>
               <td>${json.nama_bank}</td>
               <td><img src="${json.logo_bank}" style="height: 50px;"></td>
               <td>${json.nama_rekening}</td>
                <td>${json.nomor_rekening}</td>
               <td>
                  <button type="button" class="btn btn-default btn-action" title="Edit Bank Transfer" onclick="edit_bank_transfer(${json.id})" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-action" title="Delete Bank Transfer" onclick="delete_bank_transfer(${json.id})" style="margin:.15rem .1rem  !important">
                      <i class="fas fa-times" style="font-size: 11px;"></i>
                  </button>
               </td>
            </tr>`;
}

function delete_bank_transfer(id) {
  ajax_x(
    baseUrl + "Pengaturan/delete_bank_transfer",
    function (e) {
      if (e["error"] == false) {
        get_daftar_bank_transfer(20);
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    [{ id: id }]
  );
}

function edit_bank_transfer(id) {
  ajax_x(
    baseUrl + "Pengaturan/edit_bank_transfer",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          columnClass: "col-4",
          title: "Edit Bank Transfer",
          theme: "material",
          content: formaddupdate_bank_transfer(
            JSON.stringify(e["data"]),
            JSON.stringify(e["value"])
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
                if ($("#idBank").val() != 0) {
                  ajax_submit_t1("#form_utama", function (e) {
                    e["error"] == true
                      ? frown_alert(e["error_msg"])
                      : smile_alert(e["error_msg"]);
                    if (e["error"] == true) {
                      return false;
                    } else {
                      get_daftar_bank_transfer(20);
                    }
                  });
                } else {
                  frown_alert(
                    "Untuk melanjutkan, anda wajib memilih salah satu bank."
                  );
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
    [{ id: id }]
  );
}

function add_bank_transfer() {
  ajax_x(
    baseUrl + "Pengaturan/get_info_bank_transfer",
    function (e) {
      if (e["error"] == false) {
        $.confirm({
          columnClass: "col-4",
          title: "Tambah Bank Transfer",
          theme: "material",
          content: formaddupdate_bank_transfer(JSON.stringify(e["data"])),
          closeIcon: false,
          buttons: {
            cancel: function () {
              return true;
            },
            simpan: {
              text: "Simpan",
              btnClass: "btn-blue",
              action: function () {
                if ($("#idBank").val() != 0) {
                  ajax_submit_t1("#form_utama", function (e) {
                    e["error"] == true
                      ? frown_alert(e["error_msg"])
                      : smile_alert(e["error_msg"]);
                    if (e["error"] == true) {
                      return false;
                    } else {
                      get_daftar_bank_transfer(20);
                    }
                  });
                } else {
                  frown_alert(
                    "Untuk melanjutkan, anda wajib memilih salah satu bank."
                  );
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
    []
  );
}

function formaddupdate_bank_transfer(JSONData, JSONValue) {
  var daftar_bank = JSON.parse(JSONData);
  var id_daftar_bank_transfer = "";
  var bank_id = "";
  var nomor_account = "";
  var nama_account = "";
  if (JSONValue != undefined) {
    var value = JSON.parse(JSONValue);
    id_daftar_bank_transfer = `<input type="hidden" name="id" value="${value.id}">`;
    bank_id = value.bank_id;
    nomor_account = value.account_number;
    nama_account = value.account_name;
  }
  var html = `<form action="${baseUrl}Pengaturan/proses_addupdate_bank_transfer" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group">
                                 ${id_daftar_bank_transfer}
                                 <label>Nama Bank</label>
                                 <select class="form-control form-control-sm" name="bank" id="idBank">
                                    <option value="0">Pilih Bank</option>`;
  for (x in daftar_bank) {
    html += `<option value="${daftar_bank[x]["id"]}" ${
      bank_id == daftar_bank[x]["id"] ? "selected" : ""
    }>${daftar_bank[x]["nama_bank"]}</option>`;
  }
  html += `</select>
                              </div>
                           </div>

                           <div class="col-12">
                              <div class="form-group">
                                 <label>Nomor Rekening</label>
                                 <input type="text" name="nomor_rekening" value="${nomor_account}" class="form-control form-control-sm" placeholder="Nomor Rekening" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group">
                                 <label>Rekening Atas Nama</label>
                                 <input type="text" name="nama_rekening" value="${nama_account}" class="form-control form-control-sm" placeholder="Nama Pemilik Rekening" />
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
  return html;
}

function pengaturan_Pages() {
  var form = `<div class="col-lg-12 mt-0 pt-0" >
                  <form class="py-2" action="${baseUrl}Pengaturan/updatePengaturan" id="form_utama_big" class="formName" onsubmit="updatePengaturan(event)">
                     <div class="row mb-3">
                        <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                           <div class="row">
                              <div class="col-12 submitButtonArea" >
                                 <button type="button" class="btn btn-default"
                                    onclick="activeEditPengaturan()">Edit Pengaturan</button>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="row">
                              <div class="col-3">
                                 <div class="form-group">
                                    <label for="formFile" class="form-label">Logo Surat Perusahaan</label>
                                    <div class="text-center mb-3">
                                       <img src="${
                                         baseUrl +
                                         `image/company/invoice_logo/default.png`
                                       }" id="logo_perusahaan" class="rounded border" alt="Logo Surat">
                                    </div>
                                    <input class="form-control" type="file" id="photo" name="photo" disabled="">
                                    <small id="emailHelp" class="form-text text-muted">Tipe File jpg|jpeg|png. Ukuran Max 1MB. Ukuran dimensi 300x80 pixel.</small>
                                 </div>
                              </div>
                              <div class="col-9">
                                 <div class="row">
                                    <div class="col-6">
                                       <div class="form-group">
                                          <label>Deskripsi Perusahaan</label>
                                          <textarea class="form-control" id="deskripsi_perusahaan" name="deskripsi_perusahaan" rows="3" style="resize:none;" placeholder="Deskripsi Perusahaan" disabled=""></textarea>
                                       </div>
                                    </div>
                                    <div class="col-6">
                                       <div class="form-group">
                                          <label>Alamat Perusahaan</label>
                                          <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="3" style="resize:none;" placeholder="Alamat Perusahaan" disabled=""></textarea>
                                       </div>
                                    </div>
                                    <div class="col-3">
                                       <div class="form-group">
                                          <label>Nama Kota Perusahaan</label>
                                          <input type="text" class="form-control form-control-sm" id="nama_kota_perusahaan" name="nama_kota_perusahaan" value="" placeholder="Nama Kota" disabled="">
                                       </div>
                                    </div>
                                    <div class="col-2">
                                       <div class="form-group">
                                          <label>Kode Pos</label>
                                          <input type="text" class="form-control form-control-sm" id="kode_pos" name="kode_pos" value="" placeholder="Kode Pos" disabled="">
                                       </div>
                                    </div>
                                    <div class="col-3">
                                       <div class="form-group">
                                          <label>Telp</label>
                                          <input type="text" class="form-control form-control-sm" id="telpon_perusahaan" name="telpon_perusahaan" value="" placeholder="Telepon Perusahaan" disabled="">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <label>Nomor Whatsapp</label>
                                          <input type="text" class="form-control form-control-sm" id="nomor_wa_perusahaan" name="nomor_wa_perusahaan" value="" placeholder="Nomor Whatsapp Perusahaan" disabled="">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-2">
                                 <div class="form-group">
                                    <label>Email Invoice</label>
                                    <input type="text" class="form-control form-control-sm" id="email_invoice_perusahaan" name="email_invoice_perusahaan" value="" placeholder="Email Invoice Perusahaan" disabled="">
                                 </div>
                              </div>
                              <div class="col-4">
                                 <div class="form-group">
                                    <label>Judul Invoice</label>
                                    <input type="text" class="form-control form-control-sm" id="judul_invoice_perusahaan" name="judul_invoice_perusahaan" value="" placeholder="Judul Invoice Perusahaan" disabled="">
                                 </div>
                              </div>
                              <div class="col-6">
                                 <div class="form-group">
                                    <label>Alamat Invoice</label>
                                    <textarea class="form-control" id="alamat_invoice_perusahaan" name="alamat_invoice_perusahaan" rows="2" style="resize:none;" placeholder="Alamat Invoice Perusahaan" disabled=""></textarea>
                                 </div>
                              </div>
                              <div class="col-8">
                                 <div class="form-group">
                                    <label>Catatan Invoice</label>
                                    <textarea class="form-control" id="catatan_invoice_perusahaan" name="catatan_invoice_perusahaan" rows="5" style="resize:none;" placeholder="Catatan Invoice Perusahaan" disabled=""></textarea>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                           <div class="row">
                              <div class="col-12 submitButtonArea">
                                 <button type="button" class="btn btn-default "
                                    onclick="activeEditPengaturan()">Edit Pengaturan</button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>`;
  return form;
}

function updatePengaturan(e) {
  ajax_submit(e, "#form_utama_big", function (e) {
    if (e["error"] != true) {
      pengaturan_getData();
      $(".form-control").prop("disabled", true);
      $(".submitButtonArea").html(
        `<button type="button" class="btn btn-default" onclick="activeEditPengaturan()">Edit Pengaturan</button>`
      );
    }
    e["error"] == true
      ? frown_alert(e["error_msg"])
      : smile_alert(e["error_msg"]);
  });
}

function pengaturan_getData() {
  ajax_x(
    baseUrl + "Pengaturan/get_info_pengaturan",
    function (e) {
      if (e["error"] == false) {
        $("#logo_perusahaan").replaceWith(
          `<img src="${
            baseUrl + `image/company/invoice_logo/` + e.data.logo
          }" id="logo_perusahaan" class="rounded border" alt="Logo Surat">`
        );
        $("#deskripsi_perusahaan").val(e.data.description);
        $("#alamat_perusahaan").val(e.data.address);
        $("#nama_kota_perusahaan").val(e.data.city);
        $("#kode_pos").val(e.data.pos_code);
        $("#telpon_perusahaan").val(e.data.telp);
        $("#nomor_wa_perusahaan").val(e.data.whatsapp_number);
        $("#email_invoice_perusahaan").val(e.data.invoice_email);
        $("#judul_invoice_perusahaan").val(e.data.invoice_title);
        $("#catatan_invoice_perusahaan").val(e.data.invoice_note);
        $("#alamat_invoice_perusahaan").val(e.data.invoice_address);
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    []
  );
}

function activeEditPengaturan() {
  $(".form-control").prop("disabled", false);
  $(".submitButtonArea")
    .html(`<button type="button" class="btn btn-default submitButtonArea" onclick="batalEditPengaturan()">Batal</button>
                                <button type="submit" class="btn btn-success">Simpan</button>`);
}

function batalEditPengaturan() {
  pengaturan_getData();
  $(".form-control").prop("disabled", true);
  $(".submitButtonArea").html(
    `<button type="button" class="btn btn-default" onclick="activeEditPengaturan()">Edit Pengaturan</button>`
  );
}
