function daftar_hotel_Pages(){
   return  `<div class="col-12 col-lg-12 px-2 pb-0 pt-0">
               <div class="row" id="contentDaftarHotel">
                  <div class="col-6 col-lg-8 my-3 ">
                     <button class="btn btn-default" type="button" onclick="add_hotel()" title="Tambah hotel baru">
                        <i class="fas fa-hotel"></i> Tambah Hotel
                     </button>
                     <label class="float-right py-2 my-0">Filter :</label>
                  </div>
                  <div class="col-6 col-lg-4 my-3 text-right">
                     <div class="input-group ">
                        <input class="form-control form-control-sm" type="text" onkeyup="get_daftar_hotel(20)" id="searchAllDaftarHotel" name="searchAllDaftarHotel" placeholder="Nama Hotel" style="font-size: 12px;">
                        <div class="input-group-append">
                           <button class="btn btn-default" type="button" onclick="get_daftar_hotel(20)">
                              <i class="fas fa-search"></i> Cari
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="col-lg-12">
                     <table class="table table-hover tablebuka">
                        <thead>
                           <tr>
                              <th style="width:45%;">Nama Hotel</th>
                              <th style="width:20%;">Kota</th>
                              <th style="width:25%;">Deskripsi</th>
                              <th style="width:10%;">Aksi</th>
                           </tr>
                        </thead>
                        <tbody id="bodyTable_daftar_hotel">
                           <tr>
                              <td colspan="4">Daftar hotel tidak ditemukan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="col-lg-12 px-3 pb-3" >
                     <div class="row" id="pagination_daftar_hotel"></div>
                  </div>
               </div>
            </div>`;
}

function daftar_hotel_getData(){
   get_daftar_hotel(20);
}

function get_daftar_hotel(perpage){
   get_data( perpage,
             { url : 'Daftar_hotel/daftar_hotels',
               pagination_id: 'pagination_daftar_hotel',
               bodyTable_id: 'bodyTable_daftar_hotel',
               fn: 'ListDaftarHotel',
               warning_text: '<td colspan="4">Daftar hotel tidak ditemukan</td>',
               param : { search : $('#searchAllDaftarHotel').val() } } );
}

function ListDaftarHotel(JSONData){
   var json = JSON.parse(JSONData);
   var  star = '';
   for (var i = 1; i <= 7; i++) {
      if(i <= json.star_hotel){
         star += `<i class="fas fa-star" style="color: #dc9325;"></i>`;
      }else{
         star += `<i class="fas fa-star"></i>`;
      }
   }
   var html = `<tr>
                  <td>${json.hotel_name} <br> ${star} (${json.star_hotel})</td>
                  <td>${json.city_name}</td>
                  <td>${json.description}</td>
                  <td>
                     <button type="button" class="btn btn-default btn-action" title="Edit Hotel"
                        onclick="edit_hotel('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                     </button>
                     <button type="button" class="btn btn-default btn-action" title="Delete Hotel"
                        onclick="delete_hotel('${json.id}')" style="margin:.15rem .1rem  !important">
                         <i class="fas fa-times" style="font-size: 11px;"></i>
                     </button>
                  </td>
               </tr>`;
   return html;
}

function add_hotel(){
   ajax_x(
      baseUrl + "Daftar_hotel/get_info_hotel", function(e) {
         if(e['error'] == false ){
            $.confirm({
               columnClass: 'col-4',
               title: 'Tambah Hotel',
               theme: 'material',
               content: formaddupdate_hotel(JSON.stringify(e['city'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_hotel(20);
                           }
                        });
                     }
                  }
               }
            });
         }else{
            frown_alert(e['error_msg']);
         }
      },[]
   );
}


function edit_hotel(id){
   ajax_x(
      baseUrl + "Daftar_hotel/get_info_edit_hotel", function(e) {
            $.confirm({
               columnClass: 'col-4',
               title: 'Edit Hotel',
               theme: 'material',
               content: formaddupdate_hotel(JSON.stringify(e['city']),JSON.stringify(e['value'])),
               closeIcon: false,
               buttons: {
                  cancel:function () {
                       return true;
                  },
                  simpan: {
                     text: 'Simpan',
                     btnClass: 'btn-blue',
                     action: function () {
                        ajax_submit_t1("#form_utama", function(e) {
                           e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
                           if ( e['error'] == true ) {
                              return false;
                           } else {
                              get_daftar_hotel(20);
                           }
                        });
                     }
                  }
               }
            });
      },[{id:id}]
   );
}

function delete_hotel(id){
   ajax_x(
      baseUrl + "Daftar_hotel/delete_hotel", function(e) {
         if( e['error'] == false ){
            get_daftar_hotel(20);
         }
         e['error'] == true ? frown_alert(e['error_msg']) : smile_alert(e['error_msg']);
      },[{id:id}]
   );
}

function formaddupdate_hotel(JSONData, JSONValue){
   var json = JSON.parse(JSONData);
   var hotel_id = '';
   var hotel_name = '';
   var selected_city = '';
   var star = '';
   var description = '';
   if(JSONValue != undefined){
      var value = JSON.parse(JSONValue);
      hotel_id = `<input type="hidden" name="id" value="${value.id}">`;
      hotel_name = value.hotel_name;
      selected_city = value.city_id;
      star = value.star_hotel;
      description = value.description;
   }
   var html = `<form action="${baseUrl }Daftar_hotel/proses_addupdate_daftar_hotel" id="form_utama" class="formName ">
                  <div class="row px-0 mx-0">
                     <div class="col-12">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 ${hotel_id}
                                 <label>Nama Hotel</label>
                                 <input type="text" name="nama_hotel" value="${hotel_name}" class="form-control form-control-sm" placeholder="Nama Hotel" />
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Kota</label>
                                 <select class="form-control form-control-sm" name="kota">`;
                        for( x in json ) {
                           html +=  `<option value="${json[x]['id']}">${json[x]['city_name']} (${json[x]['city_code']})</option>`;
                        }
                     html +=     `</select>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Bintang Hotel</label>
                                 <input type="number" name="bintang_hotel" value="${star}" class="form-control form-control-sm" placeholder="Bintang Hotel" max="7"/>
                              </div>
                           </div>
                           <div class="col-12">
                              <div class="form-group mb-2">
                                 <label>Deskripsi Hotel</label>
                                 <textarea class="form-control form-control-sm" name="description_hotel" rows="5" style="resize: none;">${description}</textarea>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>`;
   return html;

}
