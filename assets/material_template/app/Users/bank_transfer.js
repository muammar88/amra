function bank_transfer_Pages() {
  return `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentBankTransfer">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_bank_transfer()">
                        <i class="fas fa-money-bill-wave"></i> Tambah Bank Transfer
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_bank_transfer( 20)" id="searchBankTransfer" name="searchBankTransfer" placeholder="Nama Bank / Nomor Rekening" style="font-size: 12px;">
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
                        <tbody id="bodyTable_daftar_airlines">
                           <tr>
                              <td colspan="2">Daftar bank transfer tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_airlines"></div>
                  </div>
               </div>
            </div>`;
}
