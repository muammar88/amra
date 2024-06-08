
function pengaturan_paket_la_Pages() {
  var form = `<div class="col-lg-12 mt-0 pt-0" >
                  <form class="py-2" action="${baseUrl}Pengaturan_paket_la/updatePengaturanPaketLa" id="form_utama_big" class="formName" onsubmit="updatePengaturanPaketLa(event)">
                     <div class="row mb-3">
                        <div class="col-12 p-2 text-right" style="background-color: #e9ecef;">
                           <div class="row">
                              <div class="col-12 submitButtonArea" >
                                 <button type="button" class="btn btn-default"
                                    onclick="activeEditPengaturanPaketLa()">Edit Pengaturan</button>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-9">
                           <div class="form-group">
                              <label>Catatan Invoice Paket LA</label>
                              <textarea class="form-control" id="note_paket_la" name="note_paket_la" rows="20" style="resize:none;" placeholder="Catatan Invoice Paket LA"></textarea>
                           </div>
                        </div>
                        <div class="col-3">
                           <div class="row"> 
                              <div class="col-12">
                                 <div class="form-group">
                                    <label>Tanda Tangan</label>
                                    <div class="text-center mb-3">
                                       <img src="${baseUrl + `image/company/invoice_logo/default.png`}" 
                                       id="tanda_tangan_img" class="rounded border float-left mb-3" alt="Tanda Tangan">
                                    </div>
                                 </div>
                              </div>
                              <div class="col-12">
                                 <div class="form-group">
                                    <label>Upload Tanda Tangan</label>
                                    <input type="file" class="form-control form-control-sm" id="tanda_tangan" name="tanda_tangan" value="" placeholder="Tanda Tangan" disabled="">
                                    <small id="emailHelp" class="form-text text-muted">Tipe File jpg|jpeg|png. Ukuran Max 1MB. Ukuran dimensi 110x80 pixel.</small>
                                 </div>
                              </div>
                              <div class="col-12">
                                 <div class="form-group">
                                    <label>Kurs</label>
                                    <select class="form-control form-control-sm" name="kurs" id="kurs" disabled="">
                                       <option value="rupiah">Rupiah (Rp)</option>
                                       <option value="dollar">Dollar ($)</option>
                                       <option value="riyal">Saudi Arabia Rupiah (SAR)</option>
                                    </select>
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
                                    onclick="activeEditPengaturanPaketLa()">Edit Pengaturan</button>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
              `;
  return form;
}

function updatePengaturanPaketLa(e){
   e.preventDefault();
   ajax_submit_ckeditor('#form_utama_big', 'note_paket_la', function(e) {
      if ( e['error'] == true ) {
         frown_alert(e["error_msg"]);
         return false;
      } else {
         smile_alert(e["error_msg"]);
         batalEditPengaturanPaketLa();
      }
   });
}

function pengaturan_paket_la_getData() {

   CKEDITOR.replace('note_paket_la', {height: 400});

   ajax_x(
    baseUrl + "Pengaturan_paket_la/get_info_pengaturan_paket_la",
    function (e) {
      if (e["error"] == false) {
         $("#tanda_tangan_img").replaceWith(
            `<img src="${ baseUrl + `image/company/tanda_tangan/` + e.data.tanda_tangan}" id="tanda_tangan_img" class="rounded border" alt="Tanda Tangan">`
         );
         
         var note_paket_la = CKEDITOR.instances.note_paket_la;

         CKEDITOR.instances['note_paket_la'].on('instanceReady', function() {
              CKEDITOR.instances['note_paket_la'].setData(e.data.note_paket_la);
         });

         $("#kurs").val(e.data.kurs);
         CKEDITOR.instances["note_paket_la"].setReadOnly(true);
      } else {
        frown_alert(e["error_msg"]);
      }
    },
    []
   );
}

function activeEditPengaturanPaketLa() {
  $(".form-control").prop("disabled", false);
  $(".submitButtonArea")
    .html(`<button type="button" class="btn btn-default submitButtonArea" onclick="batalEditPengaturanPaketLa()">Batal</button>
                                <button type="submit" class="btn btn-success">Simpan</button>`);
   CKEDITOR.instances["note_paket_la"].setReadOnly(false);
}

function batalEditPengaturanPaketLa() {
  pengaturan_paket_la_getData();
  $(".form-control").prop("disabled", true);
  $(".submitButtonArea").html(
    `<button type="button" class="btn btn-default" onclick="activeEditPengaturanPaketLa()">Edit Pengaturan</button>`
  );
}