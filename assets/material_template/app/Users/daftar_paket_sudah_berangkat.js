function daftar_paket_sudah_berangkat_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarPaket">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_paket()" title="Tambah paket baru">
                        <i class="fas fa-box"></i> Tambah Paket Baru
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
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
