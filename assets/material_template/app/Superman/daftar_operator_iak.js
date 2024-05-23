function daftar_operator_iak_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row">
                  <div class="col-6 col-lg-9 my-3 ">
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-3 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="daftar_operator_iak_getData()" id="searchAllDaftarOperatorIAK" name="searchAllDaftarOperatorIAK" placeholder="Kode Operator" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="daftar_operator_iak_getData()">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:5%;">#</th>
                              <th style="width:45%;">Tipe</th>
                              <th style="width:50%;">Nama Operator</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_operator_iak">
                           <tr>
                              <td colspan="3">Daftar operator tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_operator_iak"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_operator_iak_getData(){
   get_daftar_operator_iak(50);
}

function get_daftar_operator_iak(perpage){
   get_data(perpage, {
      url: "Superman/PPOB/daftar_operator_iak",
      pagination_id: "pagination_daftar_operator_iak",
      bodyTable_id: "bodyTable_daftar_operator_iak",
      fn: "ListDaftarOperatorIAK",
      warning_text: '<td colspan="3">Daftar operator IAK tidak ditemukan</td>',
      param: { search: $('#searchAllDaftarOperatorIAK').val() } ,
   });
}

function ListDaftarOperatorIAK(JSONData){
   var json = JSON.parse(JSONData);
   var html = `<tr>
                  <td>${json.number}</td>
                  <td>${json.type}</td>
                  <td>${json.operator_name}</td>
               </tr>`;
   return html;
}